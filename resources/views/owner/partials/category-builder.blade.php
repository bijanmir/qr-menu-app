{{-- resources/views/owner/partials/category-builder.blade.php --}}
<div class="border border-neutral-200 rounded-2xl mb-6" id="category-{{ $category->id }}">
    <!-- Category Header -->
    <div class="p-4 border-b border-neutral-200 bg-neutral-50 rounded-t-2xl">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex items-center gap-2 mr-4">
                    <button class="p-1 rounded hover:bg-neutral-200 cursor-move">
                        <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </button>
                    
                    @if($category->icon)
                    <span class="text-xl">{{ $category->icon }}</span>
                    @endif
                </div>
                
                <div class="flex-1">
                    <h3 
                        class="text-lg font-semibold text-neutral-900 cursor-pointer hover:text-primary-600" 
                        hx-get="{{ route('owner.categories.edit-inline', $category->id) }}"
                        hx-target="#category-name-{{ $category->id }}"
                        hx-trigger="dblclick"
                    >
                        <span id="category-name-{{ $category->id }}">{{ $category->name }}</span>
                    </h3>
                    <p class="text-sm text-neutral-600">{{ $category->items->count() }} items</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- Visibility Toggle -->
                <label class="flex items-center">
                    <input type="checkbox" 
                           class="sr-only" 
                           {{ $category->visible ? 'checked' : '' }}
                           hx-patch="{{ route('owner.categories.update', $category->id) }}"
                           hx-vals='{"visible": {{ $category->visible ? 'false' : 'true' }}}'
                           hx-swap="none">
                    <div class="relative">
                        <div class="block bg-neutral-300 w-10 h-6 rounded-full {{ $category->visible ? 'bg-primary-600' : '' }}"></div>
                        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition {{ $category->visible ? 'transform translate-x-4' : '' }}"></div>
                    </div>
                </label>
                
                <!-- Add Item Button -->
                <button 
                    onclick="showAddItemModal({{ $category->id }})"
                    class="btn-primary btn-sm"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Item
                </button>
                
                <!-- Category Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 rounded hover:bg-neutral-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-neutral-200 z-10">
                        <div class="py-1">
                            <button class="block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100">
                                Edit Category
                            </button>
                            <button 
                                hx-delete="{{ route('owner.categories.destroy', $category->id) }}"
                                hx-confirm="Are you sure you want to delete this category and all its items?"
                                hx-target="#category-{{ $category->id }}"
                                hx-swap="outerHTML"
                                class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50"
                            >
                                Delete Category
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items -->
    <div class="p-4" id="category-items-{{ $category->id }}">
        @forelse($category->items as $item)
            @include('owner.partials.item-builder', ['item' => $item])
        @empty
            <div class="text-center py-8 text-neutral-500">
                <p>No items in this category yet.</p>
                <button 
                    onclick="showAddItemModal({{ $category->id }})"
                    class="btn-primary btn-sm mt-2"
                >
                    Add First Item
                </button>
            </div>
        @endforelse
    </div>
</div>
