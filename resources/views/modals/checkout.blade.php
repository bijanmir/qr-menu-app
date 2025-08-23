<!-- Checkout Modal -->
<div id="checkout-modal" class="fixed inset-0 z-50 overflow-y-auto" style="display: block;">
    <div class="flex min-h-screen items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeCheckoutModal()"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full max-h-screen overflow-y-auto">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b">
                <h2 class="text-xl font-semibold text-gray-900">Checkout</h2>
                <button onclick="closeCheckoutModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Order Summary -->
            <div class="p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Order Summary</h3>
                
                @if($cartItems && count($cartItems) > 0)
                    <div class="space-y-3 mb-6">
                        @foreach($cartItems as $cartItem)
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <span class="text-sm bg-gray-100 rounded-full w-6 h-6 flex items-center justify-center mr-3">{{ $cartItem['quantity'] }}</span>
                                        <span class="font-medium">{{ $cartItem['name'] }}</span>
                                    </div>
                                    
                                    @if(isset($cartItem['modifiers']) && count($cartItem['modifiers']) > 0)
                                        <div class="ml-9 mt-1 space-y-1">
                                            @foreach($cartItem['modifiers'] as $modifier)
                                                <div class="text-sm text-gray-600">+ {{ $modifier['name'] }}</div>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    @if(!empty($cartItem['special_instructions']))
                                        <div class="ml-9 mt-1 text-sm text-gray-500 italic">
                                            Note: {{ $cartItem['special_instructions'] }}
                                        </div>
                                    @endif
                                </div>
                                <span class="font-semibold">${{ number_format($cartItem['total'], 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Order Totals -->
                    <div class="border-t pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Subtotal</span>
                            <span>${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Tax</span>
                            <span>${{ number_format($tax, 2) }}</span>
                        </div>
                        @if(isset($tip) && $tip > 0)
                            <div class="flex justify-between text-sm">
                                <span>Tip</span>
                                <span>${{ number_format($tip, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between font-bold text-lg border-t pt-2">
                            <span>Total</span>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">Your cart is empty</p>
                    </div>
                @endif
            </div>

            @if($cartItems && count($cartItems) > 0)
                <!-- Customer Information Form -->
                <div class="px-6 pb-6">
                    <form hx-post="{{ route('customer.checkout') }}" 
                          hx-target="#checkout-result"
                          hx-swap="innerHTML"
                          hx-indicator="#checkout-loading"
                          class="space-y-4">
                        @csrf

                        <h3 class="font-semibold text-gray-900 mb-4">Contact Information</h3>
                        
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                            <input type="text" 
                                   name="customer_name" 
                                   id="customer_name" 
                                   required
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Your full name">
                        </div>

                        <div>
                            <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                            <input type="tel" 
                                   name="customer_phone" 
                                   id="customer_phone" 
                                   required
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="(555) 123-4567">
                        </div>

                        <div>
                            <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Email (Optional)</label>
                            <input type="email" 
                                   name="customer_email" 
                                   id="customer_email"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="your@email.com">
                        </div>

                        <!-- Service Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service Type *</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="service_type" value="dine_in" class="text-blue-600 focus:ring-blue-500" checked>
                                    <span class="ml-2">Dine In</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="service_type" value="takeout" class="text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2">Takeout</span>
                                </label>
                                @if(isset($restaurant) && $restaurant->supports_delivery)
                                    <label class="flex items-center">
                                        <input type="radio" name="service_type" value="delivery" class="text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2">Delivery</span>
                                    </label>
                                @endif
                            </div>
                        </div>

                        <!-- Tip Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tip</label>
                            <div class="grid grid-cols-4 gap-2 mb-2">
                                <button type="button" onclick="setTip(0.15)" class="tip-btn px-3 py-2 text-sm border rounded hover:bg-gray-50">15%</button>
                                <button type="button" onclick="setTip(0.18)" class="tip-btn px-3 py-2 text-sm border rounded hover:bg-gray-50">18%</button>
                                <button type="button" onclick="setTip(0.20)" class="tip-btn px-3 py-2 text-sm border rounded hover:bg-gray-50">20%</button>
                                <button type="button" onclick="setTip(0.25)" class="tip-btn px-3 py-2 text-sm border rounded hover:bg-gray-50">25%</button>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm">$</span>
                                <input type="number" 
                                       name="tip_amount" 
                                       id="tip_amount" 
                                       step="0.01" 
                                       min="0"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Custom tip amount">
                            </div>
                        </div>

                        <!-- Special Instructions -->
                        <div>
                            <label for="order_notes" class="block text-sm font-medium text-gray-700 mb-1">Special Instructions</label>
                            <textarea name="order_notes" 
                                      id="order_notes" 
                                      rows="2"
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Any special requests for your order..."></textarea>
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="payment_method" value="card" class="text-blue-600 focus:ring-blue-500" checked>
                                    <span class="ml-2">Credit/Debit Card</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="payment_method" value="cash" class="text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2">Pay at Restaurant</span>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                                <span class="htmx-indicator" id="checkout-loading">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                                <span class="htmx-indicator-off">
                                    Place Order - ${{ number_format($total, 2) }}
                                </span>
                            </button>
                        </div>
                    </form>

                    <!-- Result container for HTMX response -->
                    <div id="checkout-result"></div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function closeCheckoutModal() {
    document.getElementById('checkout-modal').remove();
}

function setTip(percentage) {
    const subtotal = {{ $subtotal ?? 0 }};
    const tipAmount = subtotal * percentage;
    document.getElementById('tip_amount').value = tipAmount.toFixed(2);
    
    // Update tip button styling
    document.querySelectorAll('.tip-btn').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'border-blue-500');
        btn.classList.add('hover:bg-gray-50');
    });
    
    event.target.classList.add('bg-blue-100', 'border-blue-500');
    event.target.classList.remove('hover:bg-gray-50');
}

// Auto-format phone number
document.getElementById('customer_phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 6) {
        value = `(${value.slice(0,3)}) ${value.slice(3,6)}-${value.slice(6,10)}`;
    } else if (value.length >= 3) {
        value = `(${value.slice(0,3)}) ${value.slice(3)}`;
    }
    e.target.value = value;
});
</script>