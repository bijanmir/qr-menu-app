@extends('layouts.owner')

@section('title', 'Dashboard')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-600">Welcome back, {{ auth()->user()->name }}. Here's what's happening today.</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="text-right">
                <p class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</p>
                <p class="text-xs text-gray-400" id="current-time">{{ now()->format('g:i A') }}</p>
            </div>
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-lg shadow-lg">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        </div>
    </div>
@endsection

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .glass-morphism {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.18);
    }
    
    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .gradient-border {
        background: linear-gradient(white, white) padding-box, linear-gradient(45deg, #3B82F6, #8B5CF6) border-box;
        border: 2px solid transparent;
    }
    
    .animate-counter {
        animation: countUp 2s ease-out forwards;
    }
    
    @keyframes countUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    .pulse-dot {
        animation: pulse-dot 2s infinite;
    }
    
    @keyframes pulse-dot {
        0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
</style>
@endpush

@section('content')
    <!-- Real-time Status Bar -->
    <div class="mb-8 bg-gradient-to-r from-emerald-50 to-blue-50 rounded-xl p-4 border border-emerald-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full pulse-dot"></div>
                    <span class="text-sm font-medium text-gray-900">System Status: All systems operational</span>
                </div>
                <div class="h-4 w-px bg-gray-300"></div>
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm text-gray-600">Last sync: <span id="last-sync">just now</span></span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button class="text-sm text-blue-600 hover:text-blue-700 font-medium">View Details</button>
                <button onclick="refreshDashboard()" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-white/50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Revenue Today Card -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl shadow-sm border border-green-100 card-hover overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="flex items-center space-x-1 text-green-600 text-sm font-medium">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span>+12.5%</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <p class="text-sm font-medium text-gray-600">Revenue Today</p>
                    <p class="text-3xl font-bold text-gray-900 animate-counter" data-target="{{ $metrics['revenue_today'] ?? 0 }}">
                        ${{ number_format($metrics['revenue_today'] ?? 0, 2) }}
                    </p>
                    <p class="text-xs text-gray-500">vs ${{ number_format(($metrics['revenue_today'] ?? 0) * 0.875, 2) }} yesterday</p>
                </div>
            </div>
            <div class="px-6 py-3 bg-white/50">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-600">Goal: $2,500</span>
                    <span class="text-green-600 font-medium">{{ number_format((($metrics['revenue_today'] ?? 0) / 2500) * 100, 1) }}%</span>
                </div>
                <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-1.5 rounded-full transition-all duration-1000" style="width: {{ min((($metrics['revenue_today'] ?? 0) / 2500) * 100, 100) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Orders Today Card -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-sm border border-blue-100 card-hover overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="flex items-center space-x-1 text-blue-600 text-sm font-medium">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span>+8.2%</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <p class="text-sm font-medium text-gray-600">Orders Today</p>
                    <p class="text-3xl font-bold text-gray-900 animate-counter" data-target="{{ $metrics['total_orders_today'] ?? 0 }}">
                        {{ $metrics['total_orders_today'] ?? 0 }}
                    </p>
                    <p class="text-xs text-gray-500">{{ number_format((($metrics['total_orders_today'] ?? 0) / max(1, $metrics['total_orders_today'] ?? 1)) * 100, 1) }} orders/hour avg</p>
                </div>
            </div>
            <div class="px-6 py-3 bg-white/50">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600">Peak: 2-4 PM</span>
                    <div class="flex space-x-1">
                        <div class="w-1 h-4 bg-blue-300 rounded"></div>
                        <div class="w-1 h-6 bg-blue-400 rounded"></div>
                        <div class="w-1 h-8 bg-blue-500 rounded"></div>
                        <div class="w-1 h-6 bg-blue-400 rounded"></div>
                        <div class="w-1 h-4 bg-blue-300 rounded"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Satisfaction Card -->
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl shadow-sm border border-purple-100 card-hover overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <div class="flex items-center space-x-1 text-purple-600 text-sm font-medium">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span>+0.3</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <p class="text-sm font-medium text-gray-600">Customer Rating</p>
                    <div class="flex items-end space-x-2">
                        <p class="text-3xl font-bold text-gray-900 animate-counter">4.8</p>
                        <div class="flex space-x-0.5 pb-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= 4 ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Based on 247 reviews</p>
                </div>
            </div>
            <div class="px-6 py-3 bg-white/50">
                <div class="text-xs text-gray-600">Recent: "Amazing food!" - Sarah M.</div>
            </div>
        </div>

        <!-- Active Locations Card -->
        <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl shadow-sm border border-orange-100 card-hover overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="flex -space-x-1">
                            <div class="w-4 h-4 bg-green-500 rounded-full border-2 border-white"></div>
                            <div class="w-4 h-4 bg-green-500 rounded-full border-2 border-white"></div>
                        </div>
                    </div>
                </div>
                <div class="space-y-1">
                    <p class="text-sm font-medium text-gray-600">Active Locations</p>
                    <p class="text-3xl font-bold text-gray-900 animate-counter" data-target="{{ $metrics['total_restaurants'] ?? 0 }}">
                        {{ $metrics['total_restaurants'] ?? 0 }}
                    </p>
                    <p class="text-xs text-gray-500">All locations online</p>
                </div>
            </div>
            <div class="px-6 py-3 bg-white/50">
                <div class="text-xs text-gray-600">Next inspection: Dec 15</div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Revenue Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 card-hover">
            <div class="p-6 border-b border-gray-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Revenue Analytics</h3>
                        <p class="text-sm text-gray-600">Last 7 days performance</p>
                    </div>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg">7D</button>
                        <button class="px-3 py-1 text-xs font-medium text-gray-600 hover:text-blue-600 rounded-lg">30D</button>
                        <button class="px-3 py-1 text-xs font-medium text-gray-600 hover:text-blue-600 rounded-lg">90D</button>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <canvas id="revenueChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Quick Actions & Stats -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Today's Snapshot</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Avg. Order Value</span>
                        <span class="text-lg font-semibold text-gray-900">${{ number_format(($metrics['revenue_today'] ?? 0) / max(1, $metrics['total_orders_today'] ?? 1), 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Peak Hour</span>
                        <span class="text-lg font-semibold text-gray-900">2:00 PM</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Menu Views</span>
                        <span class="text-lg font-semibold text-gray-900">1,247</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Conversion Rate</span>
                        <span class="text-lg font-semibold text-green-600">18.3%</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <button class="w-full flex items-center justify-between p-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg hover:shadow-xl group">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span class="font-medium">Add New Menu Item</span>
                        </div>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    
                    <button class="w-full flex items-center justify-between p-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 transition-all shadow-lg hover:shadow-xl group">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span class="font-medium">View Live Orders</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="bg-white/20 text-xs px-2 py-1 rounded-full">{{ $metrics['total_orders_today'] ?? 0 }} active</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </button>
                    
                    <button class="w-full flex items-center justify-between p-4 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl hover:from-purple-600 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl group">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span class="font-medium">Analytics Report</span>
                        </div>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Restaurants Overview -->
    @if(isset($restaurants) && $restaurants->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-8 card-hover">
            <div class="p-6 border-b border-gray-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Your Restaurants</h3>
                        <p class="text-sm text-gray-600">Manage all your locations</p>
                    </div>
                    <button class="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Add Location</span>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($restaurants as $restaurant)
                        <div class="group relative bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 hover:from-blue-50 hover:to-indigo-50 transition-all duration-300 border border-gray-200 hover:border-blue-200 cursor-pointer">
                            <div class="flex justify-between items-start mb-4">
                                <div class="p-3 bg-white rounded-lg shadow-sm group-hover:shadow-md transition-shadow">
                                    <svg class="w-6 h-6 text-gray-700 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <span class="text-xs font-medium text-green-700 bg-green-100 px-2 py-1 rounded-full">Active</span>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 group-hover:text-blue-900 mb-2">{{ $restaurant->name }}</h4>
                                <p class="text-sm text-gray-600 mb-3">{{ $restaurant->address ?? 'No address set' }}</p>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Orders today</span>
                                    <span class="font-semibold text-gray-900">{{ rand(15, 45) }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm mt-1">
                                    <span class="text-gray-500">Revenue</span>
                                    <span class="font-semibold text-green-600">${{ number_format(rand(800, 2400), 2) }}</span>
                                </div>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/0 to-purple-500/0 group-hover:from-blue-500/5 group-hover:to-purple-500/5 rounded-xl transition-all duration-300"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <!-- Empty State with Call to Action -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-8 text-center border-2 border-dashed border-blue-200 mb-8">
            <div class="max-w-md mx-auto">
                <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Welcome to Your QR Menu Dashboard!</h3>
                <p class="text-gray-600 mb-6">Ready to revolutionize your restaurant experience? Let's get your first location set up with our cutting-edge QR menu system.</p>
                <button class="inline-flex items-center space-x-3 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Create Your First Restaurant</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Recent Orders -->
    @if(isset($recentOrders) && $recentOrders->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 card-hover">
            <div class="p-6 border-b border-gray-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                        <p class="text-sm text-gray-600">Latest customer orders across all locations</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center space-x-1 text-green-600 text-sm">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span>Live updates</span>
                        </div>
                        <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restaurant</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($recentOrders as $order)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                                <span class="text-blue-700 font-semibold text-sm">#{{ substr($order->id, -3) }}</span>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">#{{ $order->id }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $order->customer_name ?? 'Guest' }}</div>
                                            <div class="text-sm text-gray-500">{{ $order->customer_phone ?? 'N/A' }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $order->restaurant->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'confirmed' => 'bg-blue-100 text-blue-800',
                                                'preparing' => 'bg-orange-100 text-orange-800',
                                                'ready' => 'bg-green-100 text-green-800',
                                                'completed' => 'bg-gray-100 text-gray-800',
                                            ];
                                            $statusClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        ${{ number_format($order->total, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $order->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button class="text-blue-600 hover:text-blue-800 font-medium">View</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update time
    function updateTime() {
        const now = new Date();
        document.getElementById('current-time').textContent = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    }
    setInterval(updateTime, 60000);

    // Revenue Chart
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Revenue',
                data: [1200, 1900, 1500, 2100, 1800, 2400, 2200],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return ' + value;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            elements: {
                point: {
                    hoverBackgroundColor: 'rgb(59, 130, 246)',
                }
            }
        }
    });

    // Counter animation
    const counters = document.querySelectorAll('.animate-counter');
    counters.forEach(counter => {
        const target = parseFloat(counter.getAttribute('data-target')) || parseFloat(counter.textContent.replace(/[^0-9.]/g, ''));
        const increment = target / 200;
        let current = 0;
        
        const updateCounter = () => {
            if (current < target) {
                current += increment;
                if (counter.textContent.includes(')) {
                    counter.textContent = ' + Math.ceil(current).toLocaleString();
                } else {
                    counter.textContent = Math.ceil(current);
                }
                requestAnimationFrame(updateCounter);
            } else {
                if (counter.textContent.includes(')) {
                    counter.textContent = ' + target.toLocaleString();
                } else {
                    counter.textContent = target;
                }
            }
        };
        
        setTimeout(updateCounter, Math.random() * 500);
    });
});

function refreshDashboard() {
    // Simulate refresh
    document.getElementById('last-sync').textContent = 'just now';
    
    // You would typically make an AJAX call here to refresh the data
    // For demo purposes, we'll just show a brief loading state
    const button = event.target.closest('button');
    const originalHTML = button.innerHTML;
    button.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';
    
    setTimeout(() => {
        button.innerHTML = originalHTML;
    }, 1000);
}
</script>
@endpush