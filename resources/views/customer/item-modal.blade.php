<!-- Item Modal (returned as HTMX response) -->
<div id="item-modal" class="fixed inset-0 z-50 overflow-y-auto" style="display: block;">
    <div class="flex min-h-screen items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeModal()"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full max-h-screen overflow-y-auto">
            <!-- Close Button -->
            <button onclick="closeModal()" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- Item Image -->
            @if($item->image)
                <div class="aspect-w-16 aspect-h-9">
                    <img src="{{ $item->image }}" alt="{{ $item->name }}" class="w-full h-48 object-cover rounded-t-lg">
                </div>
            @else
                <div class="w-full h-48 bg-gray-200 rounded-t-lg flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endif

            <!-- Item Details -->
            <div class="p-6">
                <div class="mb-4">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $item->name }}</h2>
                    @if($item->description)
                        <p class="text-gray-600 mb-4">{{ $item->description }}</p>
                    @endif
                    
                    <!-- Price -->
                    <div class="flex items-center justify-between mb-6">
                        <span class="text-2xl font-bold text-green-600">${{ number_format($item->price, 2) }}</span>
                        @if($item->is_86ed)
                            <span class="bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded-full">Currently Unavailable</span>
                        @endif
                    </div>
                </div>

                <!-- Add to Cart Form -->
                <form hx-post="{{ route('customer.cart.add') }}" 
                      hx-target="#cart-sidebar" 
                      hx-swap="innerHTML"
                      hx-on::after-request="closeModal(); updateCartCount();"
                      class="space-y-6">
                    @csrf
                    <input type="hidden" name="item_id" value="{{ $item->id }}">

                    <!-- Modifier Groups -->
                    @if($item->modifierGroups && $item->modifierGroups->count() > 0)
                        @foreach($item->modifierGroups as $group)
                            <div class="border rounded-lg p-4">
                                <h3 class="font-semibold text-gray-900 mb-3">
                                    {{ $group->name }}
                                    @if($group->required)
                                        <span class="text-red-500 text-sm">(Required)</span>
                                    @endif
                                </h3>

                                <div class="space-y-2">
                                    @if($group->selection_type === 'single')
                                        <!-- Radio buttons for single selection -->
                                        @foreach($group->modifiers as $modifier)
                                            <label class="flex items-center justify-between p-2 hover:bg-gray-50 rounded cursor-pointer">
                                                <div class="flex items-center">
                                                    <input type="radio" 
                                                           name="modifier_groups[{{ $group->id }}]" 
                                                           value="{{ $modifier->id }}"
                                                           class="text-blue-600 focus:ring-blue-500"
                                                           @if($group->required && $loop->first) required @endif>
                                                    <span class="ml-3 text-gray-900">{{ $modifier->name }}</span>
                                                </div>
                                                @if($modifier->price > 0)
                                                    <span class="text-gray-600">+${{ number_format($modifier->price, 2) }}</span>
                                                @endif
                                            </label>
                                        @endforeach
                                    @else
                                        <!-- Checkboxes for multiple selection -->
                                        @foreach($group->modifiers as $modifier)
                                            <label class="flex items-center justify-between p-2 hover:bg-gray-50 rounded cursor-pointer">
                                                <div class="flex items-center">
                                                    <input type="checkbox" 
                                                           name="modifier_groups[{{ $group->id }}][]" 
                                                           value="{{ $modifier->id }}"
                                                           class="text-blue-600 focus:ring-blue-500">
                                                    <span class="ml-3 text-gray-900">{{ $modifier->name }}</span>
                                                </div>
                                                @if($modifier->price > 0)
                                                    <span class="text-gray-600">+${{ number_format($modifier->price, 2) }}</span>
                                                @endif
                                            </label>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <!-- Special Instructions -->
                    <div>
                        <label for="special_instructions" class="block text-sm font-medium text-gray-700 mb-2">
                            Special Instructions (Optional)
                        </label>
                        <textarea name="special_instructions" 
                                  id="special_instructions" 
                                  rows="3" 
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Any special requests or modifications..."></textarea>
                    </div>

                    <!-- Quantity and Add to Cart -->
                    <div class="flex items-center justify-between">
                        <!-- Quantity -->
                        <div class="flex items-center space-x-3">
                            <label class="text-sm font-medium text-gray-700">Quantity:</label>
                            <div class="flex items-center border rounded-lg">
                                <button type="button" onclick="decreaseQuantity()" class="px-3 py-1 text-gray-600 hover:text-gray-800">−</button>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" max="10" 
                                       class="w-16 text-center border-0 focus:ring-0" readonly>
                                <button type="button" onclick="increaseQuantity()" class="px-3 py-1 text-gray-600 hover:text-gray-800">+</button>
                            </div>
                        </div>

                        <!-- Add to Cart Button -->
                        <button type="submit" 
                                @if($item->is_86ed) disabled @endif
                                class="@if($item->is_86ed) bg-gray-400 cursor-not-allowed @else bg-blue-600 hover:bg-blue-700 @endif text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                            @if($item->is_86ed)
                                Unavailable
                            @else
                                Add to Cart
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function closeModal() {
    document.getElementById('item-modal').remove();
}

function increaseQuantity() {
    const input = document.getElementById('quantity');
    const current = parseInt(input.value);
    if (current < 10) {
        input.value = current + 1;
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    const current = parseInt(input.value);
    if (current > 1) {
        input.value = current - 1;
    }
}

function updateCartCount() {
    // This would update the cart count in the header
    // Implementation depends on your cart count display
}
</script>