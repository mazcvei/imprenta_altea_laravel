<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShippingRequest;
use App\Models\Cart;
use App\Models\Order;
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
            $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
            $amount = 0;
            foreach ($cartItems as $item) {
                $amount += $item->priceUnit->price;
            }
            $amount_stripe = intval($amount * 100);

            $cartItems = Cart::where('user_id', Auth::id())->with('product','priceUnit')->get();

            $order = Order::create([
                    'user_id' => Auth::id(),
                    'status' => OrderStatusEnum::Pendiente->value,
                    'shipping_address' => $request->shipping_address,
                    'total' => $amount,    
            ]);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_price_id' => $item->price_unit_id,
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
        //$sigHeader = $request->server('HTTP_STRIPE_SIGNATURE');
        $sigHeader = $request->header('Stripe-Signature');
        Log::info('Webhook de Stripe', [
            'stripe_secret' => config('services.stripe.webhook_secret'),
            'signature' => $sigHeader,
        ]);
        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook_secret')
            );

            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    $orderId = $session['metadata']['order_id'];
                    $order = Order::find($orderId);
                    if ($order) {
                        $order->update([
                            'status' => OrderStatusEnum::Procesando->value,
                            'payment_method' => $session->payment_method_types[0] ?? null,
                        ]);
                    }
                    Log::info('Pago completado', [
                        'session' => $session
                    ]);
                    Cart::where('user_id', $session['metadata']['user_id'])->delete();
                    break;
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error en el webhook de Stripe', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
