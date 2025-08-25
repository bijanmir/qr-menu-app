{{-- Luxury Cart Item --}}
<div class="group relative bg-white rounded-xl border border-gray-200 p-4 transition-all duration-200 hover:shadow-md hover:border-gray-300">
    <div class="flex items-start space-x-4">
        <!-- Item Info -->
        <div class="flex-1 min-w-0">
            <h4 class="font-serif text-lg font-medium text-gray-900 leading-tight mb-1">{{ $item['name'] }}</h4>
            
            <!-- Modifiers -->
            @if(isset($item['modifiers']) && !empty($item['modifiers']))
            <div class="text-sm text-gray-600 mb-3 space-y-1">
                @foreach($item['modifiers'] as $groupId => $group)
                    @if(isset($group['modifiers']) && !empty($group['modifiers']))
                        <div class="flex flex-wrap items-center gap-1">
                            <span class="font-medium text-gray-700">{{ $group['group_name'] ?? 'Options' }}:</span>
                            @foreach($group['modifiers'] as $modifier)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-50 text-gray-700 text-xs">
                                    {{ $modifier['name'] }}
                                    @if($modifier['price_adjustment'] != 0) 
                                        <span class="ml-1 text-green-600 font-medium">
                                            (+${{ number_format($modifier['price_adjustment'], 2) }})
                                        </span>
                                    @endif
                                </span>
                                @if(!$loop->last) @endif
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
            @endif
            
            <!-- Special Notes -->
            @if(isset($item['notes']) && $item['notes'])
            <div class="mb-3">
                <p class="text-sm text-gray-600 italic bg-amber-50 px-3 py-2 rounded-lg border border-amber-200">
                    <svg class="w-4 h-4 inline mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    "{{ $item['notes'] }}"
                </p>
            </div>
            @endif
            
            <!-- Quantity Controls & Price -->
            <div class="flex items-center justify-between">
                <!-- Elegant Quantity Stepper -->
                <div class="flex items-center bg-gray-50 rounded-lg border border-gray-200 p-1">
                    <button onclick="updateCartQuantity({{ $index }}, {{ max(1, $item['quantity'] - 1) }})"
                            @if($item['quantity'] <= 1) disabled @endif
                            class="w-8 h-8 rounded-md bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-150 shadow-sm">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path>
                        </svg>
                    </button>
                    
                    <span class="w-10 text-center font-medium text-gray-900 text-sm">{{ $item['quantity'] }}</span>
                    
                    <button onclick="updateCartQuantity({{ $index }}, {{ min(99, $item['quantity'] + 1) }})"
                            class="w-8 h-8 rounded-md bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition-all duration-150 shadow-sm">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Price Display -->
                <div class="text-right">
                    <span class="font-serif text-lg font-medium text-amber-700">
                        ${{ number_format($item['price'] * $item['quantity'], 2) }}
                    </span>
                    @if($item['quantity'] > 1)
                        <div class="text-xs text-gray-500">
                            ${{ number_format($item['price'], 2) }} each
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Remove Button -->
        <div class="flex-shrink-0">
            <button onclick="removeCartItem({{ $index }}, '{{ $item['name'] }}')"
                    class="w-9 h-9 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all duration-200 group/remove">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
    </div>
</div>
