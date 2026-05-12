<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;

class StripeController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        Log::info('Creando sesión de pago', [
            'amount' => $request->amount,
            'product_name' => $request->product_name,
        ]);
        try {

            $request->validate([
                'amount' => 'required|numeric|min:1',
                'product_name' => 'required|string|max:255'
            ]);

            Stripe::setApiKey(config('services.stripe.secret'));

            $amount = intval($request->amount * 100);

            $session = Session::create([
                'payment_method_types' => ['card'],
                'mode' => 'payment',
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => $request->product_name,
                            ],
                            'unit_amount' => $amount,
                        ],
                        'quantity' => 1,
                    ]
                ],
                'success_url' => env('FRONT_URL') . '/pago/exito',
                'cancel_url' => env('FRONT_URL') . '/pago/cancelado',
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
                    Log::info('Pago completado',json_decode(json_encode($session), true));
                        

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
