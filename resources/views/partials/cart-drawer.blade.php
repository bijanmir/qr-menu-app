{{-- resources/views/partials/cart-drawer.blade.php --}}
<div class="flex flex-col h-full">
    <!-- Cart Header -->
    <div class="flex items-center justify-between p-4 border-b border-neutral-200">
        <h2 class="text-lg font-semibold text-neutral-900">Your Order</h2>
        <button 
            onclick="closeCartDrawer()"
            class="p-2 rounded-lg hover:bg-neutral-100"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Cart Items -->
    <div class="flex-1 overflow-y-auto p-4">
        @if(isset($cartItems) && count($cartItems) > 0)
            <div class="space-y-4" id="cart-items">
                @foreach($cartItems as $index => $item)
                    @include('partials.cart-item', ['item' => $item, 'index' => $index])
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center h-full text-neutral-500">
                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.8 9H19m-7-9v9"></path>
                </svg>
                <p>Your cart is empty</p>
                <p class="text-sm">Add some items to get started!</p>
            </div>
        @endif
    </div>

    <!-- Cart Summary & Checkout -->
    @if(isset($cartItems) && count($cartItems) > 0)
    <div class="p-4 border-t border-neutral-200 bg-neutral-50">
        <div class="space-y-2 mb-4">
            <div class="flex justify-between text-sm">
                <span>Subtotal</span>
                <span>${{ number_format($subtotal ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span>Tax</span>
                <span>${{ number_format($tax ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span>Service Fee</span>
                <span>${{ number_format($serviceFee ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between font-semibold text-lg border-t border-neutral-200 pt-2">
                <span>Total</span>
                <span>${{ number_format($total ?? 0, 2) }}</span>
            </div>
        </div>
        
        <button 
            hx-post="{{ route('customer.checkout') }}"
            hx-target="#modal-container"
            class="w-full btn-primary btn-lg"
        >
            Checkout
        </button>
    </div>
    @endif
</div>
