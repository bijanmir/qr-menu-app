{{-- resources/views/customer/menu.blade.php --}}
@extends('layouts.customer')

@section('main-content')
<div class="pb-20">
    <!-- Category Navigation -->
    @if($categories->isNotEmpty())
    <div class="sticky top-20 z-30 bg-white/90 backdrop-blur-sm border-b border-neutral-100">
        <div class="flex gap-2 overflow-x-auto py-4 px-4 mobile-scroll-snap custom-scrollbar">
            @foreach($categories as $category)
            <button 
                data-nav-category="{{ $category->id }}"
                hx-get="{{ route('customer.menu.category', [$restaurant->slug, $menu->id, $category->id]) }}"
                hx-target="#menu-items"
                hx-swap="innerHTML"
                onclick="window.menuNav.scrollToCategory({{ $category->id }})"
                class="chip whitespace-nowrap"
            >
                @if($category->icon)
                    <span class="mr-2">{{ $category->icon }}</span>
                @endif
                {{ $category->name }}
            </button>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Menu Items -->
    <div id="menu-items" class="px-4 py-6">
        @foreach($categories as $category)
            @include('partials.category-section', ['category' => $category])
        @endforeach
    </div>
</div>
@endsection
