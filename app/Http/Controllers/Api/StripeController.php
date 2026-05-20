<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;

class StripeController extends Controller
{
    public function createCheckoutSession(Request $request)
    {

      
        try {

            $request->validate([
                'shipping_address' => 'required|string|max:255'
            ]);

            Stripe::setApiKey(config('services.stripe.secret'));
            $cartItems = Cart::where('user_id', auth()->id())->with('product')->get();
            $amount = 0;
            foreach ($cartItems as $item) {
                $amount += $item->priceUnit->price;
            }
            $amount_stripe = intval($amount * 100);
            $order = Order::create([
                    'user_id' => auth()->id(),
                    'status' => 'pending',
                    'shipping_address' => $request->shipping_address,
                    'total' => $amount,    
            ]);
            $session = Session::create([
                'payment_method_types' => ['card'],
                'mode' => 'payment',
                'metadata' => [
                        'order_id' => $order->id, 
                        'user_id' => auth()->id(),
                ],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => "Pedito #{$order->id}",
                            ],
                            'unit_amount' => $amount_stripe,
                        ],
                        'quantity' => 1,
                    ]
                ],
                'success_url' => env('FRONT_URL') . '/pago/exito',
                'cancel_url' => env('FRONT_URL') . '/pago/cancelado',
            ]);

          

            Log::info('Creando sesión de pago', [
                'amount' => $amount_stripe,
                'shipping_address' => $request->shipping_address,
                'session_id' => $session->id,
                'session_url' => $session->url,
            ]);

            return response()->json([
                'url' => $session->url,
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->server('HTTP_STRIPE_SIGNATURE');

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
                            'status' => 'completed',
                            'payment_method' => $session->payment_method_types[0] ?? null,
                        ]);
                    }
                    Log::info('Pago completado',$request->all());
                    Log::info('Limpiando carrito del usuario', ['user_id' => $session['metadata']['user_id']]);
                    Cart::where('user_id', $session['metadata']['user_id'])->delete();
                    break;
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
