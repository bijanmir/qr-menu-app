<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const CART_SESSION_KEY = 'cart';
    private const TAX_RATE = 0.08; // 8% tax - should be configurable
    private const SERVICE_FEE_RATE = 0.02; // 2% service fee - should be configurable

    public function getCart(): array
    {
        $cart = Session::get(self::CART_SESSION_KEY, [
            'items' => [],
            'subtotal' => 0,
            'tax' => 0,
            'service_fee' => 0,
            'total' => 0,
        ]);

        return $this->calculateTotals($cart);
    }

    public function addItem(Item $item, int $quantity = 1, array $modifiers = [], ?string $notes = null): array
    {
        $cart = $this->getCart();
        
        // Calculate item price with modifiers
        $itemPrice = $item->price;
        $selectedModifiers = [];
        
        foreach ($modifiers as $modifierGroupId => $modifierIds) {
            $modifierGroup = $item->modifierGroups()->find($modifierGroupId);
            if (!$modifierGroup) continue;
            
            $groupModifiers = [];
            foreach ((array)$modifierIds as $modifierId) {
                $modifier = $modifierGroup->modifiers()->find($modifierId);
                if ($modifier && $modifier->available) {
                    $itemPrice += $modifier->price_adjustment;
                    $groupModifiers[] = [
                        'id' => $modifier->id,
                        'name' => $modifier->name,
                        'price_adjustment' => $modifier->price_adjustment,
                    ];
                }
            }
            
            if (!empty($groupModifiers)) {
                $selectedModifiers[$modifierGroupId] = [
                    'group_name' => $modifierGroup->name,
                    'modifiers' => $groupModifiers,
                ];
            }
        }

        // Create cart item
        $cartItem = [
            'id' => uniqid(),
            'item_id' => $item->id,
            'name' => $item->name,
            'price' => $itemPrice,
            'base_price' => $item->price,
            'quantity' => $quantity,
            'modifiers' => $selectedModifiers,
            'notes' => $notes,
            'image' => $item->image,
        ];

        $cart['items'][] = $cartItem;
        $cart = $this->calculateTotals($cart);
        
        Session::put(self::CART_SESSION_KEY, $cart);
        
        return $cartItem;
    }

    public function updateQuantity(int $index, int $quantity): void
    {
        $cart = $this->getCart();
        
        if (isset($cart['items'][$index])) {
            if ($quantity <= 0) {
                $this->removeItem($index);
                return;
            }
            
            $cart['items'][$index]['quantity'] = $quantity;
            $cart = $this->calculateTotals($cart);
            Session::put(self::CART_SESSION_KEY, $cart);
        }
    }

    public function removeItem(int $index): void
    {
        $cart = $this->getCart();
        
        if (isset($cart['items'][$index])) {
            unset($cart['items'][$index]);
            $cart['items'] = array_values($cart['items']); // Reindex array
            $cart = $this->calculateTotals($cart);
            Session::put(self::CART_SESSION_KEY, $cart);
        }
    }

    public function clear(): void
    {
        Session::forget(self::CART_SESSION_KEY);
    }

    public function getItemCount(): int
    {
        $cart = $this->getCart();
        return collect($cart['items'])->sum('quantity');
    }

    private function calculateTotals(array $cart): array
    {
        $subtotal = 0;
        
        foreach ($cart['items'] as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        $tax = $subtotal * self::TAX_RATE;
        $serviceFee = $subtotal * self::SERVICE_FEE_RATE;
        $total = $subtotal + $tax + $serviceFee;
        
        $cart['subtotal'] = round($subtotal, 2);
        $cart['tax'] = round($tax, 2);
        $cart['service_fee'] = round($serviceFee, 2);
        $cart['total'] = round($total, 2);
        
        return $cart;
    }
}
