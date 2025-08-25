<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Cart;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'integer|min:1|max:99',
            'modifiers' => 'array',
            'notes' => 'string|max:500'
        ]);

        $item = Item::with('modifierGroups.modifiers')->findOrFail($validated['item_id']);
        
        // Verify item is available
        if (!$item->visible || !$item->available) {
            return response()->json(['error' => 'Item not available'], 422);
        }

        $cartItem = $this->cartService->addItem(
            $item,
            $validated['quantity'] ?? 1,
            $validated['modifiers'] ?? [],
            $validated['notes'] ?? null
        );

        if (request()->headers->get('HX-Request')) {
            // For HTMX requests (like from the modal), return the cart drawer HTML
            return $this->renderCartDrawer();
        }

        // For regular AJAX requests (like from the quick add button), return JSON
        return response()->json(['success' => true, 'cart_item' => $cartItem]);
    }

    public function updateLine(Request $request, int $index)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        $this->cartService->updateQuantity($index, $validated['quantity']);

        if (request()->headers->get('HX-Request')) {
            return $this->renderCartDrawer();
        }

        return response()->json(['success' => true]);
    }

    public function removeLine(int $index)
    {
        $this->cartService->removeItem($index);

        if (request()->headers->get('HX-Request')) {
            return $this->renderCartDrawer();
        }

        return response()->json(['success' => true]);
    }

    public function checkout(Request $request)
    {
        $cart = $this->cartService->getCart();
        
        if (empty($cart['items'])) {
            return response()->json(['error' => 'Cart is empty'], 422);
        }

        $tableInfo = Session::get('current_table');
        if (!$tableInfo) {
            return response()->json(['error' => 'Table information missing'], 422);
        }

        // Create checkout session with Stripe
        $checkoutService = app(CheckoutService::class);
        $checkoutSession = $checkoutService->createSession($cart, $tableInfo);

        if (request()->headers->get('HX-Request')) {
            return view('modals.checkout', compact('checkoutSession', 'cart'));
        }

        return response()->json(['checkout_url' => $checkoutSession->url]);
    }

    public function count()
    {
        $count = $this->cartService->getItemCount();
        return response()->json(['count' => $count]);
    }

    public function drawer()
    {
        return $this->renderCartDrawer();
    }

    private function renderCartDrawer()
    {
        $cart = $this->cartService->getCart();
        
        return view('partials.cart-drawer', [
            'cartItems' => $cart['items'],
            'subtotal' => $cart['subtotal'],
            'tax' => $cart['tax'],
            'serviceFee' => $cart['service_fee'],
            'total' => $cart['total']
        ]);
    }
}
