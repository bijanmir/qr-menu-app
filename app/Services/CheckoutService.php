<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\Table;
use App\Events\OrderReceived;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class CheckoutService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createSession(array $cart, array $tableInfo): StripeSession
    {
        $restaurant = Restaurant::findOrFail($tableInfo['restaurant_id']);
        $table = Table::findOrFail($tableInfo['table_id']);

        // Create line items for Stripe
        $lineItems = [];
        foreach ($cart['items'] as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item['name'],
                        'description' => $this->formatModifiers($item['modifiers']),
                    ],
                    'unit_amount' => intval($item['price'] * 100), // Convert to cents
                ],
                'quantity' => $item['quantity'],
            ];
        }

        // Add tax and service fees
        if ($cart['tax'] > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => 'Tax'],
                    'unit_amount' => intval($cart['tax'] * 100),
                ],
                'quantity' => 1,
            ];
        }

        if ($cart['service_fee'] > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => 'Service Fee'],
                    'unit_amount' => intval($cart['service_fee'] * 100),
                ],
                'quantity' => 1,
            ];
        }

        return StripeSession::create([
            'payment_method_types' => ['card', 'apple_pay', 'google_pay'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('customer.order.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('customer.restaurant', $restaurant->slug),
            'metadata' => [
                'restaurant_id' => $restaurant->id,
                'table_id' => $table->id,
                'table_code' => $table->code,
                'cart_data' => json_encode($cart),
            ],
        ]);
    }

    public function processSuccessfulPayment(string $sessionId): Order
    {
        $session = StripeSession::retrieve($sessionId);
        $metadata = $session->metadata;

        return DB::transaction(function () use ($session, $metadata) {
            // Create order
            $order = Order::create([
                'restaurant_id' => $metadata['restaurant_id'],
                'table_id' => $metadata['table_id'],
                'status' => 'pending',
                'channel' => 'dine-in',
                'subtotal' => $session->amount_subtotal / 100,
                'total' => $session->amount_total / 100,
                'currency' => strtoupper($session->currency),
                'payment_status' => 'paid',
            ]);

            // Create order items
            $cartData = json_decode($metadata['cart_data'], true);
            foreach ($cartData['items'] as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $cartItem['item_id'],
                    'name' => $cartItem['name'],
                    'price' => $cartItem['price'],
                    'quantity' => $cartItem['quantity'],
                    'modifiers' => $cartItem['modifiers'],
                    'notes' => $cartItem['notes'],
                ]);
            }

            // Create payment record
            $order->payments()->create([
                'provider' => 'stripe',
                'intent_id' => $session->payment_intent,
                'amount' => $session->amount_total / 100,
                'currency' => strtoupper($session->currency),
                'status' => 'succeeded',
                'method' => $session->payment_method_types[0] ?? 'card',
                'processed_at' => now(),
            ]);

            // Broadcast to KDS
            broadcast(new OrderReceived($order))->toOthers();

            return $order;
        });
    }

    private function formatModifiers(array $modifiers): string
    {
        if (empty($modifiers)) {
            return '';
        }

        $formatted = [];
        foreach ($modifiers as $group) {
            $modifierNames = collect($group['modifiers'])->pluck('name')->toArray();
            $formatted[] = $group['group_name'] . ': ' . implode(', ', $modifierNames);
        }

        return implode(' | ', $formatted);
    }
}
