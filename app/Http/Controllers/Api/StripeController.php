<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShippingRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\AddressOrder;
use App\OrderStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;

class StripeController extends Controller
{
    public function createCheckoutSession(StoreShippingRequest $request)
    {
        DB::beginTransaction();
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            //Transaccion 
            $cartItems = Cart::where('user_id', Auth::id())->with('product', 'priceUnit')->get();
            $amount = 0;
            foreach ($cartItems as $item) {
                $amount += $item->priceUnit->price;
            }
            $amount_stripe = intval($amount * 100);

            $cartItems = Cart::where('user_id', Auth::id())->with('product', 'priceUnit')->get();

            $order = Order::create([
                'user_id' => Auth::id(),
                'status' => OrderStatusEnum::Pendiente->value,
                'total' => $amount,
            ]);
            AddressOrder::create([
                'order_id' => $order->id,
                'address_line' =>  $request->shipping_address,
                'city' => $request->locality,
                'province' => $request->province,
                'postal_code' => $request->postal_code,
            ]);
            

            foreach ($cartItems ?? [] as $item) {
                $order->items()->create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_price_id' => $item->price_unit_id,
                    'image' => $item->image,
                ]);
            }



            $session = Session::create([
                'payment_method_types' => ['card'],
                'mode' => 'payment',
                'customer_email' => Auth::user()->email,
                'metadata' => [
                    'order_id'  => $order->id,
                    'user_id'   => Auth::id(),
                    'name'      => Auth::user()->name,
                    'email'     => Auth::user()->email,
                ],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => "Pedido #{$order->id}",
                            ],
                            'unit_amount' => $amount_stripe,
                        ],
                        'quantity' => 1,
                    ]
                ],
                'success_url' => env('FRONT_URL') . '/pago/exito',
                'cancel_url' => env('FRONT_URL') . '/pago/cancelado',
            ]);

            DB::commit();
            return response()->json([
                'url' => $session->url,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear la sesión de pago', [
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {

            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook_secret')
            );

            Log::info('Evento Stripe recibido', [
                'type' => $event->type,
            ]);

            switch ($event->type) {

                case 'checkout.session.completed':

                    $session = $event->data->object;

                    $metadata = $session->metadata ?? null;

                    if (!$metadata) {
                        Log::warning('Session sin metadata');
                        break;
                    }

                    $orderId = $metadata->order_id ?? null;
                    $userId = $metadata->user_id ?? null;

                    if (!$orderId || !$userId) {
                        Log::warning('Faltan datos en metadata', [
                            'metadata' => $metadata
                        ]);
                        break;
                    }

                    $order = Order::find($orderId);

                    if (!$order) {
                        Log::warning('Pedido no encontrado', [
                            'order_id' => $orderId
                        ]);
                        break;
                    }

                    $order->update([
                        'status' => OrderStatusEnum::Procesando->value,
                        'payment_method' => $session->payment_method_types[0] ?? null,
                    ]);

                    // Vaciar carrito del usuario
                    Cart::where('user_id', $userId)->delete();

                    Log::info('Pago completado correctamente', [
                        'order_id' => $orderId,
                        'user_id' => $userId,
                    ]);

                    break;

                default:
                    Log::info('Evento ignorado', [
                        'type' => $event->type
                    ]);
                    break;
            }

            return response()->json([
                'success' => true
            ]);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {

            Log::error('Firma Stripe inválida', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Firma inválida'
            ], 400);
        } catch (\UnexpectedValueException $e) {

            Log::error('Payload inválido', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Payload inválido'
            ], 400);
        } catch (\Exception $e) {

            Log::error('Error webhook Stripe', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            
            return response()->json([
                'received' => true
            ]);
        }
    }
}
