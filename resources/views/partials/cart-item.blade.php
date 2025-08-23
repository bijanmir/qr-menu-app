{{-- resources/views/partials/cart-item.blade.php --}}
<div class="flex items-start space-x-3 p-3 rounded-lg border border-neutral-200">
    <div class="flex-1">
        <h4 class="font-medium text-neutral-900">{{ $item['name'] }}</h4>
        
        @if(isset($item['modifiers']) && !empty($item['modifiers']))
        <div class="text-sm text-neutral-600 mt-1">
            @foreach($item['modifiers'] as $modifier)
                <div>{{ $modifier['name'] }} 
                    @if($modifier['price'] > 0)
                        (+${{ number_format($modifier['price'], 2) }})
                    @endif
                </div>
            @endforeach
        </div>
        @endif
        
        @if(isset($item['notes']) && $item['notes'])
        <p class="text-sm text-neutral-600 mt-1 italic">{{ $item['notes'] }}</p>
        @endif
        
        <div class="flex items-center justify-between mt-3">
            <!-- Quantity Stepper -->
            <div class="flex items-center space-x-2" 
                 x-data="quantityStepper({{ $item['quantity'] ?? 1 }}, 1, 10)"
                 @quantity-changed="window.cartManager.updateQuantity({{ $index }}, $event.detail.quantity)">
                <button @click="decrement()" 
                        class="w-8 h-8 rounded-full bg-neutral-200 flex items-center justify-center text-neutral-600 hover:bg-neutral-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </button>
                
                <span x-text="quantity" class="w-8 text-center font-medium"></span>
                
                <button @click="increment()" 
                        class="w-8 h-8 rounded-full bg-neutral-200 flex items-center justify-center text-neutral-600 hover:bg-neutral-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Price -->
            <span class="font-semibold text-neutral-900">${{ number_format($item['total'] ?? ($item['price'] * $item['quantity']), 2) }}</span>
        </div>
    </div>
    
    <!-- Remove Button -->
    <button onclick="window.cartManager.removeItem({{ $index }})" 
            class="p-1 text-neutral-400 hover:text-red-500 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
        </svg>
    </button>
</div>
