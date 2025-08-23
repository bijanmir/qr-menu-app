{{-- resources/views/layouts/customer.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white">
    <!-- Header -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-sm border-b border-neutral-200">
        <div class="px-4 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-neutral-900">{{ $restaurant->name }}</h1>
                    @if(isset($menu))
                        <p class="text-sm text-neutral-600">{{ $menu->name }}</p>
                    @endif
                </div>
                
                <!-- Cart Button -->
                <button 
                    id="cart-toggle"
                    class="relative p-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition-colors"
                    onclick="document.getElementById('cart-drawer').classList.toggle('translate-x-full')"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.8 9H19m-7-9v9"></path>
                    </svg>
                    <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                </button>
            </div>
        </div>
    </header>

    <main>
        @yield('main-content')
    </main>

    <!-- Cart Drawer -->
    <div id="cart-drawer" class="fixed inset-y-0 right-0 w-full max-w-md bg-white shadow-xl transform translate-x-full transition-transform duration-300 z-50">
        @include('partials.cart-drawer')
    </div>

    <!-- Cart Overlay -->
    <div 
        id="cart-overlay" 
        class="fixed inset-0 bg-black/50 z-40 hidden"
        onclick="document.getElementById('cart-drawer').classList.add('translate-x-full'); this.classList.add('hidden')"
    ></div>
</div>
@endsection
