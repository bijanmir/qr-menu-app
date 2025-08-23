<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CheckoutService;
use App\Services\CartService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $checkoutService;
    protected $cartService;

    public function __construct(CheckoutService $checkoutService, CartService $cartService)
    {
        $this->checkoutService = $checkoutService;
        $this->cartService = $cartService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'table_id' => 'required|exists:tables,id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1|max:99',
            'items.*.modifiers' => 'sometimes|array',
            'items.*.notes' => 'sometimes|string|max:500',
        ]);

        // Build cart from API request
        $cart = ['items' => [], 'subtotal' => 0, 'tax' => 0, 'service_fee' => 0, 'total' => 0];
        
        foreach ($validated['items'] as $requestItem) {
            $item = \App\Models\Item::findOrFail($requestItem['item_id']);
            
            $cartItem = $this->cartService->addItem(
                $item,
                $requestItem['quantity'],
                $requestItem['modifiers'] ?? [],
                $requestItem['notes'] ?? null
            );
            
            $cart['items'][] = $cartItem;
        }

        // Recalculate totals
        $cart = $this->cartService->calculateTotals($cart);

        // Create checkout session
        $tableInfo = [
            'restaurant_id' => $validated['restaurant_id'],
            'table_id' => $validated['table_id'],
        ];

        $checkoutSession = $this->checkoutService->createSession($cart, $tableInfo);

        return response()->json([
            'data' => [
                'checkout_url' => $checkoutSession->url,
                'session_id' => $checkoutSession->id,
                'cart_summary' => $cart,
            ]
        ], 201);
    }

    public function show(Request $request, Order $order)
    {
        $user = $request->user();
        
        // Check if user can view this order
        if ($order->restaurant->tenant_id !== $user->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $order->load(['restaurant', 'table', 'items', 'payments']);

        return response()->json([
            'data' => $order
        ]);
    }
}
