<?php

namespace App\Http\Controllers;

use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;

class WebhookController extends Controller
{
    protected $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function stripe(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload in Stripe webhook', ['error' => $e->getMessage()]);
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid signature in Stripe webhook', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($event->data->object);
                break;

            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;

            default:
                Log::info('Unhandled Stripe webhook event', ['type' => $event->type]);
        }

        return response('Webhook handled', 200);
    }

    private function handleCheckoutCompleted($session)
    {
        try {
            $order = $this->checkoutService->processSuccessfulPayment($session->id);
            Log::info('Order created from Stripe checkout', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            Log::error('Failed to process Stripe checkout completion', [
                'session_id' => $session->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function handlePaymentSucceeded($paymentIntent)
    {
        Log::info('Payment succeeded', ['payment_intent_id' => $paymentIntent->id]);
        // Additional payment success handling if needed
    }

    private function handlePaymentFailed($paymentIntent)
    {
        Log::warning('Payment failed', [
            'payment_intent_id' => $paymentIntent->id,
            'failure_reason' => $paymentIntent->last_payment_error->message ?? 'Unknown'
        ]);
        // Handle payment failure (notifications, etc.)
    }
}
