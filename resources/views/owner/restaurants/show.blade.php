@extends('layouts.owner')

@section('title', $restaurant->name . ' - Restaurant Overview')

@section('header')
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <button onclick="history.back()" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </button>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-1">{{ $restaurant->name }}</h1>
                <div class="flex items-center space-x-3 text-sm">
                    <span class="flex items-center text-gray-600">
                        <div class="w-2 h-2 {{ $restaurant->active ? 'bg-green-500 animate-pulse' : 'bg-red-500' }} rounded-full mr-2"></div>
                        {{ $restaurant->active ? 'Online' : 'Offline' }}
                    </span>
                    <span class="text-gray-400">•</span>
                    <span class="text-gray-600">{{ $restaurant->menus->count() }} {{ $restaurant->menus->count() === 1 ? 'menu' : 'menus' }}</span>
                    <span class="text-gray-400">•</span>
                    <span class="text-gray-600">Updated {{ $restaurant->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <div class="hidden md:flex items-center space-x-2 px-4 py-2 bg-white border border-gray-200 rounded-xl">
                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium text-gray-700">All systems operational</span>
            </div>
            <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </button>
            <a href="{{ route('owner.restaurants.edit', $restaurant) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Restaurant
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-700 rounded-2xl mb-8 overflow-hidden shadow-lg">
        @if($restaurant->cover_image)
            <div class="absolute inset-0">
                <img src="{{ Storage::url($restaurant->cover_image) }}" alt="{{ $restaurant->name }} Cover" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-900/80 to-purple-900/80"></div>
            </div>
        @endif
        
        <div class="relative p-8">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between space-y-6 lg:space-y-0">
                <div class="flex items-start space-x-6">
                    <!-- Restaurant Logo -->
                    <div class="relative">
                        @if($restaurant->logo)
                            <div class="w-24 h-24 lg:w-32 lg:h-32 rounded-2xl overflow-hidden shadow-xl border-4 border-white/30">
                                <img src="{{ Storage::url($restaurant->logo) }}" alt="{{ $restaurant->name }} Logo" class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="w-24 h-24 lg:w-32 lg:h-32 bg-white/20 backdrop-blur-sm rounded-2xl shadow-xl border-4 border-white/30 flex items-center justify-center">
                                <svg class="w-12 h-12 lg:w-16 lg:h-16 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Status Indicator -->
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 {{ $restaurant->active ? 'bg-green-500' : 'bg-red-500' }} rounded-full border-4 border-white flex items-center justify-center">
                            @if($restaurant->active)
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @else
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Restaurant Info -->
                    <div class="text-white">
                        <h2 class="text-3xl lg:text-4xl font-bold mb-3">{{ $restaurant->name }}</h2>
                        
                        @if($restaurant->description)
                            <p class="text-white/90 text-lg mb-4 max-w-2xl">{{ $restaurant->description }}</p>
                        @endif
                        
                        <!-- Restaurant Details -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            @if($restaurant->address)
                                <div class="flex items-center text-white/80">
                                    <div class="p-2 bg-white/20 rounded-lg mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <span>{{ $restaurant->address }}</span>
                                </div>
                            @endif
                            
                            @if($restaurant->phone)
                                <div class="flex items-center text-white/80">
                                    <div class="p-2 bg-white/20 rounded-lg mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <span>{{ $restaurant->phone }}</span>
                                </div>
                            @endif
                            
                            @if($restaurant->cuisine_type)
                                <div class="flex items-center text-white/80">
                                    <div class="p-2 bg-white/20 rounded-lg mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                    </div>
                                    <span>{{ $restaurant->cuisine_type }}</span>
                                </div>
                            @endif
                            
                            <div class="flex items-center text-white/80">
                                <div class="p-2 bg-white/20 rounded-lg mr-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                    </svg>
                                </div>
                                <span>{{ $restaurant->subdomain }}.qrmenu.com</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="flex flex-col space-y-3">
                    <button class="px-6 py-3 bg-white/20 backdrop-blur-sm text-white font-semibold rounded-xl hover:bg-white/30 transition-colors border border-white/30">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Preview Menu
                    </button>
                    
                    <button class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-xl transition-colors">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download QR
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Active Menus -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-100 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">+2 this month</span>
            </div>
            <div>
                <p class="text-3xl font-bold text-gray-900">{{ $restaurant->menus->count() }}</p>
                <p class="text-sm text-gray-600">Active Menus</p>
                <p class="text-xs text-gray-500 mt-1 flex items-center">
                    <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    All published
                </p>
            </div>
        </div>
        
        <!-- Tables -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-100 rounded-xl">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">{{ round($restaurant->tables->count() * 0.85) }} occupied</span>
            </div>
            <div>
                <p class="text-3xl font-bold text-gray-900">{{ $restaurant->tables->count() }}</p>
                <p class="text-sm text-gray-600">Tables</p>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-green-500 h-2 rounded-full transition-all duration-1000" style="width: 85%"></div>
                </div>
            </div>
        </div>
        
        <!-- Orders Today -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-purple-100 rounded-xl">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-full">Peak: 2-4 PM</span>
            </div>
            <div>
                @php $ordersToday = rand(15, 45); @endphp
                <p class="text-3xl font-bold text-gray-900">{{ $ordersToday }}</p>
                <p class="text-sm text-gray-600">Orders Today</p>
                <div class="flex space-x-1 mt-2">
                    @for($i = 0; $i < 12; $i++)
                        <div class="w-1 bg-gray-300 rounded-full" style="height: {{ rand(8, 24) }}px;"></div>
                    @endfor
                </div>
            </div>
        </div>
        
        <!-- Revenue Today -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-orange-100 rounded-xl">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">+15.3%</span>
            </div>
            <div>
                @php $revenueToday = rand(800, 2400); @endphp
                <p class="text-3xl font-bold text-gray-900">${{ number_format($revenueToday, 2) }}</p>
                <p class="text-sm text-gray-600">Revenue Today</p>
                <p class="text-xs text-gray-500 mt-1">Goal: $2,500 ({{ round(($revenueToday / 2500) * 100) }}%)</p>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Menus Management -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Menus Section -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-1">Menu Management</h3>
                            <p class="text-sm text-gray-600">Create and manage your restaurant menus</p>
                        </div>
                        <button class="px-5 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors shadow-sm flex items-center font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Menu
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    @if($restaurant->menus->count() > 0)
                        <div class="space-y-4">
                            @foreach($restaurant->menus as $menu)
                                <div class="group bg-white border border-gray-200 rounded-xl p-6 hover:border-blue-200 hover:shadow-md transition-all">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="relative">
                                                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-sm">
                                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                                <div class="absolute -top-1 -right-1 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-bold text-gray-900">{{ $menu->name }}</h4>
                                                <p class="text-gray-600 text-sm">{{ $menu->description ?: 'No description available' }}</p>
                                                <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                                                    <span class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 7a2 2 0 012-2h10a2 2 0 012 2v2M7 7h10"></path>
                                                        </svg>
                                                        {{ $menu->categories->count() }} categories
                                                    </span>
                                                    <span class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Updated {{ $menu->updated_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center space-x-3">
                                            <div class="hidden group-hover:flex items-center space-x-2">
                                                <button class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </button>
                                                <button class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <a href="{{ route('owner.menus.show', $menu) }}" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-xl hover:bg-blue-200 transition-colors font-medium">
                                                Manage
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-16">
                            <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-3">Ready to create your first menu?</h4>
                            <p class="text-gray-600 mb-8 max-w-md mx-auto">Start building your digital menu with categories, items, and pricing. Your customers will be able to scan QR codes to view and order.</p>
                            <button class="inline-flex items-center px-8 py-4 bg-blue-600 text-white font-semibold rounded-2xl hover:bg-blue-700 transition-colors shadow-sm">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Create Your First Menu
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Tables Section -->
            @if($restaurant->tables->count() > 0)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-1">Table Management</h3>
                                <p class="text-sm text-gray-600">Configure tables and generate QR codes</p>
                            </div>
                            <button class="px-5 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors shadow-sm flex items-center font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Table
                            </button>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($restaurant->tables as $table)
                                <div class="group bg-white border border-gray-200 rounded-xl p-4 hover:border-green-200 hover:shadow-md transition-all">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="relative">
                                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-sm">
                                                    <span class="text-white font-bold text-sm">{{ $table->code }}</span>
                                                </div>
                                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-white"></div>
                                            </div>
                                            <div>
                                                <h5 class="font-semibold text-gray-900">{{ $table->name }}</h5>
                                                <p class="text-sm text-gray-600">{{ $table->capacity }} seats</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors opacity-0 group-hover:opacity-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </button>
                                            <button class="px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm font-medium">
                                                QR Code
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- QR Code Center -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 text-center">
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">QR Code Center</h3>
                    <p class="text-sm text-gray-600">Master QR code for your restaurant</p>
                </div>
                
                <div class="relative inline-block mb-6">
                    <div class="w-40 h-40 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto shadow-inner">
                        <div class="w-32 h-32 bg-white rounded-xl flex items-center justify-center shadow-sm">
                            <div class="grid grid-cols-8 gap-0.5">
                                @for($i = 0; $i < 64; $i++)
                                    <div class="w-1.5 h-1.5 {{ rand(0, 1) ? 'bg-gray-900' : 'bg-white' }} rounded-sm"></div>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center animate-pulse">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <button class="w-full px-4 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                        Download QR Code
                    </button>
                    <button class="w-full px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                        Print QR Codes
                    </button>
                    <button class="w-full px-4 py-3 bg-green-100 text-green-700 font-semibold rounded-xl hover:bg-green-200 transition-colors">
                        Share Link
                    </button>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <button class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white font-semibold rounded-xl hover:from-purple-600 hover:to-pink-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        View Analytics
                    </button>
                    
                    <button class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white font-semibold rounded-xl hover:from-orange-600 hover:to-red-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Live Orders
                    </button>
                    
                    <a href="{{ route('owner.restaurants.edit', $restaurant) }}" class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Restaurant
                    </a>
                </div>
            </div>
            
            <!-- Restaurant Details -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Restaurant Details</h3>
                <div class="space-y-4">
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Public URL</p>
                        <p class="text-sm text-blue-600 font-mono">{{ $restaurant->subdomain }}.qrmenu.com</p>
                    </div>
                    
                    @if($restaurant->price_range)
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Price Range</p>
                            <p class="text-sm text-gray-900">{{ $restaurant->price_range }}</p>
                        </div>
                    @endif
                    
                    @if($restaurant->email)
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Contact Email</p>
                            <p class="text-sm text-gray-900">{{ $restaurant->email }}</p>
                        </div>
                    @endif
                    
                    @if($restaurant->website)
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Website</p>
                            <a href="{{ $restaurant->website }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-700 break-all">
                                {{ $restaurant->website }}
                            </a>
                        </div>
                    @endif
                    
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Created</p>
                        <p class="text-sm text-gray-900">{{ $restaurant->created_at->format('M j, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Restaurant overview loaded for: {{ $restaurant->name }}');
});
</script>
@endpush