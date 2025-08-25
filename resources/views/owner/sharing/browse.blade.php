@extends('layouts.owner')

@section('title', 'Browse Shared Items')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Browse Shared Items</h1>
            <p class="mt-1 text-sm text-gray-600">Discover and import menu items from other restaurants in your network.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('owner.sharing.manage', $restaurant) }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Manage Shared Items</span>
            </a>
            <a href="{{ route('owner.sharing.requests', $restaurant) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m-2 0v9a2 2 0 002 2h2M2 13h2m10-8v4m0 0v4m0-4h4m-4 0H8"></path>
                </svg>
                <span>Sharing Requests</span>
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Search Bar -->
    <div class="mb-8">
        <form method="GET" class="flex space-x-4">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}"
                    placeholder="Search for menu items, dishes, or ingredients..." 
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 flex items-center space-x-2">
                <span>Search</span>
            </button>
        </form>
    </div>

    @if($search)
        <div class="mb-6">
            <div class="flex items-center space-x-2 text-sm text-gray-600">
                <span>Search results for</span>
                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full font-medium">"{{ $search }}"</span>
                <span>•</span>
                <span>{{ $items->total() }} items found</span>
                @if($search)
                    <a href="{{ route('owner.sharing.browse', $restaurant) }}" class="text-blue-600 hover:text-blue-700 ml-4">Clear search</a>
                @endif
            </div>
        </div>
    @endif

    <!-- Items Grid -->
    @if($items->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($items as $item)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Item Image -->
                    @if($item->image)
                        <div class="aspect-w-16 aspect-h-9 bg-gray-200">
                            <img src="{{ $item->image }}" alt="{{ $item->name }}" class="w-full h-48 object-cover">
                        </div>
                    @else
                        <div class="h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <!-- Item Header -->
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">{{ $item->name }}</h3>
                            <div class="text-right ml-4">
                                <div class="text-lg font-bold text-gray-900">${{ number_format($item->price, 2) }}</div>
                                @if($item->is_on_sale)
                                    <div class="text-sm text-gray-500 line-through">${{ number_format($item->original_price, 2) }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- Item Description -->
                        @if($item->description)
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $item->description }}</p>
                        @endif

                        <!-- Restaurant Info -->
                        <div class="flex items-center space-x-2 mb-4 p-3 bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                {{ strtoupper(substr($item->category->menu->restaurant->name, 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $item->category->menu->restaurant->name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->category->name }} • {{ $item->category->menu->name }}</p>
                            </div>
                        </div>

                        <!-- Tags and Dietary Info -->
                        <div class="flex flex-wrap gap-1 mb-4">
                            @if($item->is_popular)
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Popular</span>
                            @endif
                            @if($item->is_vegetarian)
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Vegetarian</span>
                            @endif
                            @if($item->is_vegan)
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Vegan</span>
                            @endif
                            @if($item->is_gluten_free)
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Gluten Free</span>
                            @endif
                            @if($item->is_spicy)
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">🌶️ Spicy</span>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2">
                            <button 
                                onclick="showSharingModal({{ $item->id }}, 'duplicate')"
                                class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium flex items-center justify-center space-x-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <span>Duplicate</span>
                            </button>
                            <button 
                                onclick="showSharingModal({{ $item->id }}, 'link')"
                                class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm font-medium flex items-center justify-center space-x-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                                <span>Link</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $items->appends(request()->query())->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <svg class="mx-auto h-24 w-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            @if($search)
                <h3 class="mt-6 text-xl font-medium text-gray-900">No items found</h3>
                <p class="mt-2 text-gray-500">We couldn't find any items matching "{{ $search }}". Try a different search term or browse all available items.</p>
                <div class="mt-6">
                    <a href="{{ route('owner.sharing.browse', $restaurant) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Browse All Items
                    </a>
                </div>
            @else
                <h3 class="mt-6 text-xl font-medium text-gray-900">No shared items available</h3>
                <p class="mt-2 text-gray-500">There are currently no items available for sharing from other restaurants in your network.</p>
            @endif
        </div>
    @endif

    <!-- Sharing Modal -->
    <div id="sharingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Share Item</h3>
                
                <!-- Menu Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Menu</label>
                    <select id="targetMenu" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @foreach($restaurant->menus as $menu)
                            <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Category</label>
                    <select id="targetCategory" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <!-- Will be populated by JavaScript -->
                    </select>
                </div>

                <!-- Sync Settings (for links only) -->
                <div id="syncSettings" class="mb-4 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sync Settings</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" id="allowPriceOverride" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Allow price override</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="allowDescriptionOverride" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Allow description override</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="allowImageOverride" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Allow image override</span>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3">
                    <button 
                        onclick="hideModal()"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300"
                    >
                        Cancel
                    </button>
                    <button 
                        onclick="executeSharing()"
                        id="confirmButton"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        Duplicate
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
let currentItemId = null;
let currentSharingType = null;
let menuCategories = @json($restaurant->menus->mapWithKeys(function($menu) {
    return [$menu->id => $menu->categories];
}));

function showSharingModal(itemId, type) {
    currentItemId = itemId;
    currentSharingType = type;
    
    const modal = document.getElementById('sharingModal');
    const title = document.getElementById('modalTitle');
    const syncSettings = document.getElementById('syncSettings');
    const confirmButton = document.getElementById('confirmButton');
    
    if (type === 'duplicate') {
        title.textContent = 'Duplicate Item';
        syncSettings.classList.add('hidden');
        confirmButton.textContent = 'Duplicate';
        confirmButton.className = 'flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700';
    } else {
        title.textContent = 'Link Item';
        syncSettings.classList.remove('hidden');
        confirmButton.textContent = 'Link';
        confirmButton.className = 'flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700';
    }
    
    // Populate categories for first menu
    const menuSelect = document.getElementById('targetMenu');
    updateCategories(menuSelect.value);
    
    modal.classList.remove('hidden');
}

function hideModal() {
    document.getElementById('sharingModal').classList.add('hidden');
    currentItemId = null;
    currentSharingType = null;
}

function updateCategories(menuId) {
    const categorySelect = document.getElementById('targetCategory');
    categorySelect.innerHTML = '';
    
    if (menuCategories[menuId]) {
        menuCategories[menuId].forEach(category => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            categorySelect.appendChild(option);
        });
    }
}

// Update categories when menu changes
document.getElementById('targetMenu').addEventListener('change', function(e) {
    updateCategories(e.target.value);
});

function executeSharing() {
    if (!currentItemId || !currentSharingType) return;
    
    const menuId = document.getElementById('targetMenu').value;
    const categoryId = document.getElementById('targetCategory').value;
    
    const url = currentSharingType === 'duplicate' 
        ? `{{ route('owner.sharing.duplicate', $restaurant) }}`
        : `{{ route('owner.sharing.link', $restaurant) }}`;
    
    const data = {
        source_item_id: currentItemId,
        target_menu_id: menuId,
        target_category_id: categoryId,
        _token: '{{ csrf_token() }}'
    };
    
    if (currentSharingType === 'link') {
        data.sync_settings = {
            allow_price_override: document.getElementById('allowPriceOverride').checked,
            allow_description_override: document.getElementById('allowDescriptionOverride').checked,
            allow_image_override: document.getElementById('allowImageOverride').checked
        };
    }
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideModal();
            // Show success message
            alert(data.message);
            // Optionally reload or redirect
        } else {
            alert('Error: ' + (data.message || 'Something went wrong'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while sharing the item');
    });
}

// Close modal when clicking outside
document.getElementById('sharingModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideModal();
    }
});
</script>
@endpush
