{{-- resources/views/owner/menus/show.blade.php --}}
@extends('layouts.owner')

@section('page-title', $menu->name)

@section('main-content')
<div class="max-w-7xl mx-auto">
    <!-- Menu Header -->
    <div class="bg-white rounded-2xl shadow-soft mb-6">
        <div class="p-6 border-b border-neutral-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-neutral-900">{{ $menu->name }}</h1>
                    <p class="text-neutral-600 mt-1">{{ $menu->description }}</p>
                    <div class="flex items-center gap-4 mt-2">
                        <span class="chip @if($menu->status === 'published') chip-primary @endif">
                            {{ ucfirst($menu->status) }}
                        </span>
                        @if($menu->restaurant)
                        <span class="text-sm text-neutral-600">{{ $menu->restaurant->name }}</span>
                        @endif
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <!-- Preview Button -->
                    <a href="{{ route('customer.restaurant.menu', [$menu->restaurant->slug, $menu->id]) }}" 
                       target="_blank" 
                       class="btn-secondary btn-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Preview
                    </a>
                    
                    <!-- Publish Button -->
                    @if($menu->status !== 'published')
                    <form hx-post="{{ route('owner.menus.publish', $menu) }}" hx-swap="none">
                        <button type="submit" class="btn-primary btn-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Publish
                        </button>
                    </form>
                    @endif
                    
                    <!-- Menu Actions Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="btn-secondary btn-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-neutral-200 z-50">
                            <div class="py-1">
                                <form hx-post="{{ route('owner.menus.duplicate', $menu) }}" hx-swap="none">
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100">
                                        Duplicate Menu
                                    </button>
                                </form>
                                
                                <button 
                                    onclick="document.getElementById('linked-copy-modal').classList.remove('hidden')"
                                    class="block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100"
                                >
                                    Create Linked Copy
                                </button>
                                
                                <a href="{{ route('owner.menus.edit', $menu) }}" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100">
                                    Edit Menu Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- WYSIWYG Menu Builder -->
    <div class="grid grid-cols-12 gap-6">
        <!-- Menu Builder -->
        <div class="col-span-8">
            <div class="bg-white rounded-2xl shadow-soft">
                <!-- Builder Header -->
                <div class="p-6 border-b border-neutral-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-neutral-900">Menu Builder</h2>
                        <div class="flex items-center gap-3">
                            <!-- Add Category Button -->
                            <button 
                                onclick="document.getElementById('add-category-modal').classList.remove('hidden')"
                                class="btn-primary btn-sm"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Category
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Categories and Items -->
                <div id="menu-builder" class="p-6">
                    @forelse($menu->categories as $category)
                        @include('owner.partials.category-builder', ['category' => $category])
                    @empty
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-neutral-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-neutral-900 mb-2">No categories yet</h3>
                            <p class="text-neutral-600 mb-4">Start building your menu by adding categories</p>
                            <button 
                                onclick="document.getElementById('add-category-modal').classList.remove('hidden')"
                                class="btn-primary"
                            >
                                Add Your First Category
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-span-4">
            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-soft mb-6">
                <div class="p-6">
                    <h3 class="font-semibold text-neutral-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <button class="w-full btn-secondary btn-sm justify-start">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            Import from PDF
                        </button>
                        
                        <button class="w-full btn-secondary btn-sm justify-start">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17v4a2 2 0 002 2h4M13 13h4a2 2 0 012 2v4a2 2 0 01-2 2H9"></path>
                            </svg>
                            Bulk Edit Prices
                        </button>
                    </div>
                </div>
            </div>

            <!-- Menu Stats -->
            <div class="bg-white rounded-2xl shadow-soft">
                <div class="p-6">
                    <h3 class="font-semibold text-neutral-900 mb-4">Menu Statistics</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-neutral-600">Categories</span>
                            <span class="font-medium">{{ $menu->categories->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-600">Items</span>
                            <span class="font-medium">{{ $menu->items->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-600">Available</span>
                            <span class="font-medium text-green-600">{{ $menu->items->where('available', true)->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-600">86'd Items</span>
                            <span class="font-medium text-red-600">{{ $menu->items->where('available', false)->count() }}</span>
                        </div>
                        <div class="flex justify-between border-t border-neutral-200 pt-4">
                            <span class="text-neutral-600">Avg Price</span>
                            <span class="font-medium">${{ number_format($menu->items->avg('price'), 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div id="add-category-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-neutral-900 mb-4">Add Category</h3>
        
        <form hx-post="{{ route('owner.menus.categories.store', $menu) }}" 
              hx-target="#menu-builder" 
              hx-swap="beforeend"
              hx-on::after-request="document.getElementById('add-category-modal').classList.add('hidden')">
            <div class="space-y-4">
                <div>
                    <label class="label">Category Name</label>
                    <input type="text" name="name" class="input" required>
                </div>
                
                <div>
                    <label class="label">Icon (Emoji)</label>
                    <input type="text" name="icon" class="input" placeholder="🍕">
                </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('add-category-modal').classList.add('hidden')" class="btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn-primary">
                    Add Category
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Linked Copy Modal -->
<div id="linked-copy-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-neutral-900 mb-4">Create Linked Copy</h3>
        
        <form hx-post="{{ route('owner.menus.linked-copy', $menu) }}" hx-swap="none">
            <div class="space-y-4">
                <div>
                    <label class="label">Menu Name</label>
                    <input type="text" name="name" class="input" value="{{ $menu->name }} - Copy" required>
                </div>
                
                <div>
                    <label class="label">Restaurant</label>
                    <select name="restaurant_id" class="input" required>
                        <option value="">Select Restaurant</option>
                        @foreach(auth()->user()->tenant->restaurants as $restaurant)
                        <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="label">Propagation Mode</label>
                    <select name="propagation_mode" class="input" required>
                        <option value="manual">Manual Sync</option>
                        <option value="immediate">Immediate Sync</option>
                        <option value="scheduled">Scheduled Sync</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('linked-copy-modal').classList.add('hidden')" class="btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn-primary">
                    Create Linked Copy
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
