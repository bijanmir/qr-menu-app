@extends('layouts.owner')

@section('title', 'Manage Shared Items')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manage Shared Items</h1>
            <p class="mt-1 text-sm text-gray-600">View and manage items you've copied or linked from other restaurants.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('owner.sharing.browse', $restaurant) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Browse Items</span>
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Filter Tabs -->
    <div class="mb-6">
        <nav class="flex space-x-8" aria-label="Tabs">
            <a href="{{ route('owner.sharing.manage', ['restaurant' => $restaurant, 'type' => 'all']) }}" 
               class="@if($currentType === 'all') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                All Shared Items
                <span class="@if($currentType === 'all') bg-blue-100 text-blue-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs">{{ $sharedItems->total() }}</span>
            </a>
            <a href="{{ route('owner.sharing.manage', ['restaurant' => $restaurant, 'type' => 'duplicated']) }}" 
               class="@if($currentType === 'duplicated') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Duplicated
                <span class="@if($currentType === 'duplicated') bg-blue-100 text-blue-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs">
                    {{ App\Models\Item::whereHas('category.menu', fn($q) => $q->where('restaurant_id', $restaurant->id))->duplicated()->count() }}
                </span>
            </a>
            <a href="{{ route('owner.sharing.manage', ['restaurant' => $restaurant, 'type' => 'linked']) }}" 
               class="@if($currentType === 'linked') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Linked
                <span class="@if($currentType === 'linked') bg-green-100 text-green-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs">
                    {{ App\Models\Item::whereHas('category.menu', fn($q) => $q->where('restaurant_id', $restaurant->id))->linked()->count() }}
                </span>
            </a>
        </nav>
    </div>

    @if($sharedItems->count() > 0)
        <!-- Items List -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <ul class="divide-y divide-gray-200">
                @foreach($sharedItems as $item)
                    <li class="p-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center flex-1">
                                <!-- Item Image -->
                                @if($item->image)
                                    <img src="{{ $item->image }}" alt="{{ $item->name }}" class="w-16 h-16 rounded-lg object-cover mr-4">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif

                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <h3 class="text-lg font-medium text-gray-900">{{ $item->name }}</h3>
                                        @if($item->isLinked())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                                </svg>
                                                Linked
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                                Duplicated
                                            </span>
                                        @endif
                                        @if($item->isLinked() && $item->needsSync())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.966-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                                Needs Sync
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-sm text-gray-600 mb-2">{{ $item->category->name }} • {{ $item->category->menu->name }}</p>
                                    
                                    @if($item->sourceItem)
                                        <div class="flex items-center text-sm text-gray-500 mb-2">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Originally from {{ $item->sourceRestaurant->name }}
                                        </div>
                                    @endif

                                    @if($item->targetSharingHistory->count() > 0)
                                        <div class="text-xs text-gray-500">
                                            Last activity: {{ $item->targetSharingHistory->first()->created_at->diffForHumans() }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Item Actions -->
                            <div class="flex items-center space-x-2 ml-4">
                                <div class="text-right mr-4">
                                    <div class="text-lg font-semibold text-gray-900">${{ number_format($item->price, 2) }}</div>
                                    @if($item->isLinked())
                                        <div class="text-xs text-gray-500">
                                            @if($item->canOverrideField('price'))
                                                <span class="text-green-600">Price override allowed</span>
                                            @else
                                                <span class="text-gray-600">Price synced</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                @if($item->isLinked())
                                    @if($item->needsSync())
                                        <button 
                                            onclick="syncItem({{ $item->id }})"
                                            class="bg-yellow-600 text-white px-3 py-1 rounded text-sm hover:bg-yellow-700 flex items-center space-x-1"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            <span>Sync</span>
                                        </button>
                                    @else
                                        <span class="text-xs text-green-600 px-2 py-1 bg-green-50 rounded">
                                            ✓ Up to date
                                        </span>
                                    @endif

                                    <button 
                                        onclick="unlinkItem({{ $item->id }})"
                                        class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 flex items-center space-x-1"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                        </svg>
                                        <span>Unlink</span>
                                    </button>
                                @endif

                                <!-- Edit Button -->
                                <a 
                                    href="#" 
                                    class="bg-gray-100 text-gray-700 px-3 py-1 rounded text-sm hover:bg-gray-200 flex items-center space-x-1"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span>Edit</span>
                                </a>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex justify-center">
            {{ $sharedItems->appends(request()->query())->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <svg class="mx-auto h-24 w-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            <h3 class="mt-6 text-xl font-medium text-gray-900">
                @if($currentType === 'duplicated')
                    No duplicated items
                @elseif($currentType === 'linked')
                    No linked items
                @else
                    No shared items
                @endif
            </h3>
            <p class="mt-2 text-gray-500">
                @if($currentType === 'duplicated')
                    You haven't duplicated any items from other restaurants yet.
                @elseif($currentType === 'linked')
                    You don't have any linked items that sync with other restaurants.
                @else
                    You haven't shared any items from other restaurants yet.
                @endif
            </p>
            <div class="mt-6">
                <a href="{{ route('owner.sharing.browse', $restaurant) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Browse Items to Share
                </a>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
function syncItem(itemId) {
    if (!confirm('Are you sure you want to sync this item? This will update it with any changes from the original.')) {
        return;
    }

    fetch(`{{ route('owner.sharing.sync', ['restaurant' => $restaurant, 'item' => '__ITEM_ID__']) }}`.replace('__ITEM_ID__', itemId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Error: ' + (data.error || 'Something went wrong'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while syncing the item');
    });
}

function unlinkItem(itemId) {
    if (!confirm('Are you sure you want to unlink this item? It will become an independent item and will no longer sync with the original.')) {
        return;
    }

    fetch(`{{ route('owner.sharing.unlink', ['restaurant' => $restaurant, 'item' => '__ITEM_ID__']) }}`.replace('__ITEM_ID__', itemId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Error: ' + (data.error || 'Something went wrong'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while unlinking the item');
    });
}
</script>
@endpush
