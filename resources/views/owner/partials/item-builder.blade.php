{{-- resources/views/owner/partials/item-builder.blade.php --}}
<div class="border border-neutral-200 rounded-lg p-4 mb-3 hover:shadow-sm transition-shadow" id="item-{{ $item->id }}">
    <div class="flex items-center gap-4">
        <!-- Drag Handle -->
        <button class="p-1 rounded hover:bg-neutral-200 cursor-move">
            <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
            </svg>
        </button>
        
        <!-- Item Image -->
        @if($item->image)
        <div class="w-16 h-16 rounded-lg overflow-hidden bg-neutral-200 flex-shrink-0">
            <img src="{{ asset('storage/' . $item->image) }}" 
                 alt="{{ $item->name }}" 
                 class="w-full h-full object-cover">
        </div>
        @else
        <div class="w-16 h-16 rounded-lg bg-neutral-200 flex-shrink-0 flex items-center justify-center">
            <svg class="w-6 h-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        @endif
        
        <!-- Item Details -->
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h4 
                        class="font-medium text-neutral-900 cursor-pointer hover:text-primary-600" 
                        hx-get="{{ route('owner.items.edit-inline', [$item->id, 'name']) }}"
                        hx-target="#item-name-{{ $item->id }}"
                        hx-trigger="dblclick"
                    >
                        <span id="item-name-{{ $item->id }}">{{ $item->name }}</span>
                    </h4>
                    
                    @if($item->description)
                    <p 
                        class="text-sm text-neutral-600 mt-1 cursor-pointer hover:text-neutral-700" 
                        hx-get="{{ route('owner.items.edit-inline', [$item->id, 'description']) }}"
                        hx-target="#item-description-{{ $item->id }}"
                        hx-trigger="dblclick"
                    >
                        <span id="item-description-{{ $item->id }}">{{ Str::limit($item->description, 100) }}</span>
                    </p>
                    @endif
                    
                    <!-- Tags and Allergens -->
                    <div class="flex flex-wrap gap-1 mt-2">
                        @if($item->tags)
                            @foreach($item->tags as $tag)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-700">
                                {{ $tag }}
                            </span>
                            @endforeach
                        @endif
                        
                        @if($item->allergens)
                            @foreach($item->allergens as $allergen)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                {{ $allergen }}
                            </span>
                            @endforeach
                        @endif
                    </div>
                </div>
                
                <!-- Price -->
                <div class="text-right ml-4">
                    <span 
                        class="text-lg font-semibold text-neutral-900 cursor-pointer hover:text-primary-600" 
                        hx-get="{{ route('owner.items.edit-inline', [$item->id, 'price']) }}"
                        hx-target="#item-price-{{ $item->id }}"
                        hx-trigger="dblclick"
                    >
                        <span id="item-price-{{ $item->id }}">${{ number_format($item->price, 2) }}</span>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex items-center gap-2 ml-4">
            <!-- 86 Toggle -->
            <button 
                hx-post="{{ route('owner.items.86', $item->id) }}"
                hx-target="#item-86-{{ $item->id }}"
                hx-swap="outerHTML"
                class="btn-sm {{ $item->available ? 'btn-secondary' : 'btn-danger' }}"
                id="item-86-{{ $item->id }}"
            >
                @if($item->available)
                    86
                @else
                    Un-86
                @endif
            </button>
            
            <!-- Visibility Toggle -->
            <label class="flex items-center">
                <input type="checkbox" 
                       class="sr-only" 
                       {{ $item->visible ? 'checked' : '' }}
                       hx-patch="{{ route('owner.items.update', $item->id) }}"
                       hx-vals='{"visible": {{ $item->visible ? 'false' : 'true' }}}'
                       hx-swap="none">
                <div class="relative">
                    <div class="block bg-neutral-300 w-8 h-5 rounded-full {{ $item->visible ? 'bg-primary-600' : '' }}"></div>
                    <div class="absolute left-0.5 top-0.5 bg-white w-4 h-4 rounded-full transition {{ $item->visible ? 'transform translate-x-3' : '' }}"></div>
                </div>
            </label>
            
            <!-- Item Menu -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="p-2 rounded hover:bg-neutral-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                    </svg>
                </button>
                
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-neutral-200 z-10">
                    <div class="py-1">
                        <button class="block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100">
                            Edit Item
                        </button>
                        <button class="block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100">
                            Manage Modifiers
                        </button>
                        <button class="block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100">
                            Duplicate Item
                        </button>
                        <hr class="my-1">
                        <button 
                            hx-delete="{{ route('owner.items.destroy', $item->id) }}"
                            hx-confirm="Are you sure you want to delete this item?"
                            hx-target="#item-{{ $item->id }}"
                            hx-swap="outerHTML"
                            class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50"
                        >
                            Delete Item
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showAddItemModal(categoryId) {
    // Implementation for add item modal
    console.log('Add item to category:', categoryId);
}
</script>
