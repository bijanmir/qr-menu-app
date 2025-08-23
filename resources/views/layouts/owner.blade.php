{{-- resources/views/layouts/owner.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-neutral-100">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform">
        <div class="flex items-center justify-center h-16 border-b border-neutral-200">
            <h1 class="text-xl font-bold text-neutral-900">QR Menu</h1>
        </div>
        
        <nav class="mt-8">
            <div class="px-4 space-y-2">
                <a href="{{ route('owner.dashboard') }}" class="flex items-center px-4 py-2 text-neutral-700 rounded-lg hover:bg-neutral-100">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    </svg>
                    Dashboard
                </a>
                
                <a href="{{ route('owner.restaurants.index') }}" class="flex items-center px-4 py-2 text-neutral-700 rounded-lg hover:bg-neutral-100">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Restaurants
                </a>
                
                <a href="{{ route('owner.menus.index') }}" class="flex items-center px-4 py-2 text-neutral-700 rounded-lg hover:bg-neutral-100">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Menus
                </a>
                
                <a href="{{ route('owner.orders.index') }}" class="flex items-center px-4 py-2 text-neutral-700 rounded-lg hover:bg-neutral-100">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Orders
                </a>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="lg:ml-64">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-neutral-200">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-neutral-900">@yield('page-title')</h2>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-neutral-700 hover:text-neutral-900">
                                <span>{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-neutral-200">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-6">
            @yield('main-content')
        </main>
    </div>
</div>
@endsection
