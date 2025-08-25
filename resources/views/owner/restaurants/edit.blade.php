@extends('layouts.owner')

@section('title', 'Edit ' . $restaurant->name)

@section('header')
    <!-- Clean, minimal header -->
    <div class="bg-white border-b border-gray-200 -mx-8 -mt-8 mb-8 px-8 py-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-center space-x-4">
                <!-- Simple restaurant avatar -->
                <div class="relative">
                    @if($restaurant->logo)
                        <div class="w-16 h-16 rounded-xl overflow-hidden shadow-sm ring-1 ring-gray-200">
                            <img src="{{ Storage::url($restaurant->logo) }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="w-16 h-16 bg-gray-100 rounded-xl shadow-sm ring-1 ring-gray-200 flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    @endif
                    <!-- Simple status dot -->
                    <div class="absolute -bottom-1 -right-1 w-5 h-5 {{ $restaurant->active ? 'bg-green-500' : 'bg-gray-400' }} rounded-full border-2 border-white shadow-sm"></div>
                </div>
                
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit {{ $restaurant->name }}</h1>
                    <div class="flex items-center space-x-4 text-sm text-gray-500 mt-1">
                        @if($restaurant->cuisine_type)
                            <span>{{ $restaurant->cuisine_type }}</span>
                        @endif
                        @if($restaurant->price_range)
                            <span>{{ $restaurant->price_range }}</span>
                        @endif
                        <span class="inline-flex items-center">
                            <div class="w-2 h-2 {{ $restaurant->active ? 'bg-green-400' : 'bg-gray-400' }} rounded-full mr-1"></div>
                            {{ $restaurant->active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('owner.restaurants.show', $restaurant) }}" 
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back
                </a>
                <button type="button" onclick="confirmDelete()" 
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete
                </button>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="fixed top-5 right-5 z-50 max-w-sm bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-lg flex items-center space-x-3" id="success-notification">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-5 right-5 z-50 max-w-sm bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg shadow-lg flex items-center space-x-3" id="error-notification">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('owner.restaurants.update', $restaurant) }}" method="POST" enctype="multipart/form-data" id="restaurant-form">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="bg-white border border-gray-200 rounded-xl mb-6 overflow-hidden shadow-sm">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
                            <p class="text-sm text-gray-500">Essential details about your restaurant</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Restaurant Name *</label>
                            <input type="text" id="name" name="name" 
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                   value="{{ old('name', $restaurant->name) }}" required>
                            @error('name')
                                <div class="mt-1 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="subdomain" class="block text-sm font-medium text-gray-700 mb-2">Subdomain *</label>
                            <div class="flex">
                                <input type="text" id="subdomain" name="subdomain" 
                                       class="flex-1 block w-full px-3 py-2 border border-gray-300 rounded-l-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                       value="{{ old('subdomain', $restaurant->subdomain) }}" required>
                                <span class="inline-flex items-center px-3 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-r-lg">
                                    .qrmenu.com
                                </span>
                            </div>
                            @error('subdomain')
                                <div class="mt-1 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none" 
                                  placeholder="Tell customers what makes your restaurant special">{{ old('description', $restaurant->description) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Help customers understand what makes your restaurant unique</p>
                        @error('description')
                            <div class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="cuisine_type" class="block text-sm font-medium text-gray-700 mb-2">Cuisine Type</label>
                            <input type="text" id="cuisine_type" name="cuisine_type" 
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                   value="{{ old('cuisine_type', $restaurant->cuisine_type) }}"
                                   placeholder="e.g. Italian, Mexican">
                            @error('cuisine_type')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="price_range" class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                            <select id="price_range" name="price_range" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Select price range</option>
                                <option value="$" {{ old('price_range', $restaurant->price_range) == '$' ? 'selected' : '' }}>$ - Budget Friendly</option>
                                <option value="$$" {{ old('price_range', $restaurant->price_range) == '$$' ? 'selected' : '' }}>$$ - Moderate</option>
                                <option value="$$$" {{ old('price_range', $restaurant->price_range) == '$$$' ? 'selected' : '' }}>$$$ - Upscale</option>
                                <option value="$$$$" {{ old('price_range', $restaurant->price_range) == '$$$$' ? 'selected' : '' }}>$$$$ - Fine Dining</option>
                            </select>
                            @error('price_range')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Restaurant Status</label>
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <button type="button" onclick="toggleActive()" id="active-toggle"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $restaurant->active ? 'bg-blue-600' : 'bg-gray-200' }}">
                                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $restaurant->active ? 'translate-x-5' : 'translate-x-0' }}" id="toggle-dot"></span>
                                </button>
                                <input type="hidden" name="active" id="active-input" value="{{ $restaurant->active ? '1' : '0' }}">
                                <div>
                                    <div class="text-sm font-medium text-gray-900" id="status-text">
                                        {{ $restaurant->active ? 'Active' : 'Inactive' }}
                                    </div>
                                    <div class="text-xs text-gray-500" id="status-description">
                                        {{ $restaurant->active ? 'Visible to customers' : 'Hidden from customers' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact & Location -->
            <div class="bg-white border border-gray-200 rounded-xl mb-6 overflow-hidden shadow-sm">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Contact & Location</h2>
                            <p class="text-sm text-gray-500">How customers can reach and find you</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 space-y-6">
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Full Address *</label>
                        <textarea id="address" name="address" rows="2" required 
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none"
                                  placeholder="Street address, city, state, ZIP code">{{ old('address', $restaurant->address) }}</textarea>
                        @error('address')
                            <div class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone" 
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                   value="{{ old('phone', $restaurant->phone) }}"
                                   placeholder="+1 (555) 123-4567">
                            @error('phone')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" 
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                   value="{{ old('email', $restaurant->email) }}"
                                   placeholder="info@restaurant.com">
                            @error('email')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="website" class="block text-sm font-medium text-gray-700 mb-2">Website URL</label>
                            <input type="url" id="website" name="website" 
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                   value="{{ old('website', $restaurant->website) }}"
                                   placeholder="https://www.restaurant.com">
                            @error('website')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Visual Identity -->
            <div class="bg-white border border-gray-200 rounded-xl mb-6 overflow-hidden shadow-sm">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Visual Identity</h2>
                            <p class="text-sm text-gray-500">Upload beautiful images that represent your brand</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Restaurant Logo -->
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Restaurant Logo</h3>
                            
                            @if($restaurant->logo)
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 mb-2">Current logo:</p>
                                    <div class="relative inline-block rounded-lg overflow-hidden">
                                        <img src="{{ Storage::url($restaurant->logo) }}" alt="Current logo" class="w-48 h-32 object-cover">
                                        <button type="button" onclick="removeCurrentImage('logo')"
                                                class="absolute top-2 right-2 bg-red-600 bg-opacity-80 text-white rounded-full p-1 hover:bg-opacity-100 transition-all">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endif
                            
                            <div onclick="document.getElementById('logo').click()" 
                                 class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-purple-400 hover:bg-purple-50 transition-all">
                                <input type="file" id="logo" name="logo" class="hidden" accept="image/*" onchange="previewImage(this, 'logo-preview')">
                                <div class="w-12 h-12 mx-auto mb-3 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-900 mb-1">
                                    {{ $restaurant->logo ? 'Change Logo' : 'Upload Logo' }}
                                </h4>
                                <p class="text-xs text-gray-500">PNG, JPG or GIF up to 2MB<br>Square format recommended</p>
                            </div>
                            
                            <div id="logo-preview" class="hidden mt-4">
                                <p class="text-sm text-gray-600 mb-2">New logo preview:</p>
                                <div class="relative inline-block rounded-lg overflow-hidden">
                                    <img alt="Logo preview" class="w-48 h-32 object-cover">
                                    <button type="button" onclick="clearPreview('logo')"
                                            class="absolute top-2 right-2 bg-red-600 bg-opacity-80 text-white rounded-full p-1 hover:bg-opacity-100 transition-all">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            @error('logo')
                                <div class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <!-- Cover Image -->
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Cover Image</h3>
                            
                            @if($restaurant->cover_image)
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 mb-2">Current cover image:</p>
                                    <div class="relative inline-block rounded-lg overflow-hidden">
                                        <img src="{{ Storage::url($restaurant->cover_image) }}" alt="Current cover" class="w-48 h-32 object-cover">
                                        <button type="button" onclick="removeCurrentImage('cover_image')"
                                                class="absolute top-2 right-2 bg-red-600 bg-opacity-80 text-white rounded-full p-1 hover:bg-opacity-100 transition-all">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endif
                            
                            <div onclick="document.getElementById('cover_image').click()" 
                                 class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all">
                                <input type="file" id="cover_image" name="cover_image" class="hidden" accept="image/*" onchange="previewImage(this, 'cover-preview')">
                                <div class="w-12 h-12 mx-auto mb-3 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-900 mb-1">
                                    {{ $restaurant->cover_image ? 'Change Cover' : 'Upload Cover Image' }}
                                </h4>
                                <p class="text-xs text-gray-500">PNG, JPG or GIF up to 4MB<br>Landscape format recommended</p>
                            </div>
                            
                            <div id="cover-preview" class="hidden mt-4">
                                <p class="text-sm text-gray-600 mb-2">New cover image preview:</p>
                                <div class="relative inline-block rounded-lg overflow-hidden">
                                    <img alt="Cover image preview" class="w-48 h-32 object-cover">
                                    <button type="button" onclick="clearPreview('cover_image')"
                                            class="absolute top-2 right-2 bg-red-600 bg-opacity-80 text-white rounded-full p-1 hover:bg-opacity-100 transition-all">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            @error('cover_image')
                                <div class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Operating Schedule -->
            <div class="bg-white border border-gray-200 rounded-xl mb-6 overflow-hidden shadow-sm">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Operating Schedule</h2>
                                <p class="text-sm text-gray-500">Set your restaurant's opening hours</p>
                            </div>
                        </div>
                        <button type="button" onclick="copyHours()" class="text-sm text-blue-600 hover:text-blue-700 font-medium px-3 py-1 rounded hover:bg-blue-50 transition-colors">
                            Copy Hours
                        </button>
                    </div>
                </div>
                
                <div class="p-6 space-y-4">
                    @php
                        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                        $dayLabels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        $currentHours = $restaurant->hours ?? [];
                    @endphp
                    
                    @foreach($days as $index => $day)
                        @php
                            $dayData = $currentHours[$day] ?? [];
                            $isClosed = isset($dayData['closed']) && $dayData['closed'];
                            $openTime = old('hours.' . $day . '.open', $dayData['open'] ?? '');
                            $closeTime = old('hours.' . $day . '.close', $dayData['close'] ?? '');
                            $closedChecked = old('hours.' . $day . '.closed', $isClosed);
                        @endphp
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 {{ $closedChecked ? 'opacity-60' : '' }}" data-day="{{ $day }}">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-semibold text-gray-900">{{ $dayLabels[$index] }}</h4>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="hours[{{ $day }}][closed]" 
                                           class="sr-only peer" {{ $closedChecked ? 'checked' : '' }} 
                                           onchange="toggleDayHours(this)">
                                    <div class="relative w-8 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-red-500"></div>
                                    <span class="ml-2 text-sm font-medium {{ $closedChecked ? 'text-red-600' : 'text-gray-700' }}" data-open-text="Open" data-closed-text="Closed">
                                        {{ $closedChecked ? 'Closed' : 'Open' }}
                                    </span>
                                </label>
                            </div>
                            
                            <div class="time-inputs {{ $closedChecked ? 'hidden' : '' }}">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Opening Time</label>
                                        <input type="time" name="hours[{{ $day }}][open]" 
                                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" 
                                               value="{{ $openTime }}" {{ $closedChecked ? 'disabled' : '' }}>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Closing Time</label>
                                        <input type="time" name="hours[{{ $day }}][close]" 
                                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" 
                                               value="{{ $closeTime }}" {{ $closedChecked ? 'disabled' : '' }}>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="closed-message {{ $closedChecked ? '' : 'hidden' }} text-center py-4 text-gray-500">
                                <p class="text-sm">Closed on {{ $dayLabels[$index] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Footer Actions -->
            <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 mt-8 -mx-6 -mb-6">
                <div class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0">
                    <div class="text-sm text-gray-500">
                        Last updated: {{ $restaurant->updated_at->format('M j, Y \a\t g:i A') }}
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('owner.restaurants.show', $restaurant) }}" 
                           class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update Restaurant
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4 shadow-xl">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Delete Restaurant</h3>
                    <p class="text-sm text-gray-500">This action cannot be undone</p>
                </div>
            </div>
            <p class="text-sm text-gray-700 mb-6">
                Are you sure you want to delete <strong>"{{ $restaurant->name }}"</strong>? 
                This will permanently remove all associated data including menus, orders, and tables.
            </p>
            <div class="flex space-x-3">
                <button type="button" onclick="closeDeleteModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <form method="POST" action="{{ route('owner.restaurants.destroy', $restaurant) }}" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
// Auto-hide notifications
setTimeout(() => {
    const notifications = document.querySelectorAll('[id$="-notification"]');
    notifications.forEach(notification => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100px)';
        setTimeout(() => notification.remove(), 300);
    });
}, 4000);

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const img = preview.querySelector('img');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file size
        const maxSize = input.name === 'logo' ? 2 * 1024 * 1024 : 4 * 1024 * 1024;
        if (file.size > maxSize) {
            alert(`File size too large. Maximum size is ${maxSize / 1024 / 1024}MB.`);
            input.value = '';
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Invalid file type. Please select a JPG, PNG, or GIF image.');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
}

function clearPreview(inputName) {
    const input = document.getElementById(inputName);
    const preview = document.getElementById(inputName + '-preview');
    
    input.value = '';
    preview.classList.add('hidden');
}

function removeCurrentImage(inputName) {
    const currentImageContainer = document.querySelector(`input[name="${inputName}"]`)
        .closest('div').querySelector('img').closest('div');
    currentImageContainer.style.display = 'none';
    
    // Add hidden input to mark for removal
    const form = document.getElementById('restaurant-form');
    const removeInput = document.createElement('input');
    removeInput.type = 'hidden';
    removeInput.name = `remove_${inputName}`;
    removeInput.value = '1';
    form.appendChild(removeInput);
}

function toggleActive() {
    const toggle = document.getElementById('active-toggle');
    const input = document.getElementById('active-input');
    const dot = document.getElementById('toggle-dot');
    const text = document.getElementById('status-text');
    const description = document.getElementById('status-description');
    
    const isActive = input.value === '1';
    
    if (isActive) {
        input.value = '0';
        toggle.classList.remove('bg-blue-600');
        toggle.classList.add('bg-gray-200');
        dot.classList.remove('translate-x-5');
        dot.classList.add('translate-x-0');
        text.textContent = 'Inactive';
        description.textContent = 'Hidden from customers';
    } else {
        input.value = '1';
        toggle.classList.remove('bg-gray-200');
        toggle.classList.add('bg-blue-600');
        dot.classList.remove('translate-x-0');
        dot.classList.add('translate-x-5');
        text.textContent = 'Active';
        description.textContent = 'Visible to customers';
    }
}

function toggleDayHours(checkbox) {
    const dayContainer = checkbox.closest('[data-day]');
    const timeInputs = dayContainer.querySelector('.time-inputs');
    const closedMessage = dayContainer.querySelector('.closed-message');
    const label = checkbox.parentElement.querySelector('span');
    const inputs = dayContainer.querySelectorAll('input[type="time"]');
    
    if (checkbox.checked) {
        timeInputs.classList.add('hidden');
        closedMessage.classList.remove('hidden');
        dayContainer.classList.add('opacity-60');
        label.textContent = 'Closed';
        label.classList.remove('text-gray-700');
        label.classList.add('text-red-600');
        inputs.forEach(input => {
            input.disabled = true;
            input.value = '';
        });
    } else {
        timeInputs.classList.remove('hidden');
        closedMessage.classList.add('hidden');
        dayContainer.classList.remove('opacity-60');
        label.textContent = 'Open';
        label.classList.remove('text-red-600');
        label.classList.add('text-gray-700');
        inputs.forEach(input => {
            input.disabled = false;
        });
    }
}

function copyHours() {
    const sourceDay = prompt('Which day would you like to copy from? (monday, tuesday, etc.)');
    if (!sourceDay) return;
    
    const sourceDayContainer = document.querySelector(`[data-day="${sourceDay.toLowerCase()}"]`);
    if (!sourceDayContainer) {
        alert('Invalid day specified');
        return;
    }
    
    const sourceOpen = sourceDayContainer.querySelector('input[name*="[open]"]').value;
    const sourceClose = sourceDayContainer.querySelector('input[name*="[close]"]').value;
    const sourceClosed = sourceDayContainer.querySelector('input[name*="[closed]"]').checked;
    
    const targetDays = prompt('Which days would you like to copy to? (separate with commas, or "all" for all days)');
    if (!targetDays) return;
    
    let daysToUpdate = [];
    if (targetDays.toLowerCase() === 'all') {
        daysToUpdate = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    } else {
        daysToUpdate = targetDays.split(',').map(d => d.trim().toLowerCase());
    }
    
    daysToUpdate.forEach(day => {
        const dayContainer = document.querySelector(`[data-day="${day}"]`);
        if (dayContainer && day !== sourceDay.toLowerCase()) {
            const openInput = dayContainer.querySelector('input[name*="[open]"]');
            const closeInput = dayContainer.querySelector('input[name*="[close]"]');
            const closedCheckbox = dayContainer.querySelector('input[name*="[closed]"]');
            
            openInput.value = sourceOpen;
            closeInput.value = sourceClose;
            closedCheckbox.checked = sourceClosed;
            
            toggleDayHours(closedCheckbox);
        }
    });
}

function confirmDelete() {
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
}

// Auto-generate subdomain from restaurant name
document.getElementById('name').addEventListener('input', function() {
    const currentSubdomain = document.getElementById('subdomain').value;
    const originalSubdomain = '{{ $restaurant->subdomain }}';
    
    if (currentSubdomain === originalSubdomain || currentSubdomain === '') {
        const name = this.value;
        const subdomain = name
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '')
            .substring(0, 50);
        document.getElementById('subdomain').value = subdomain;
    }
});

// Initialize day hours on page load
document.addEventListener('DOMContentLoaded', function() {
    const dayCheckboxes = document.querySelectorAll('input[name*="[closed]"]');
    dayCheckboxes.forEach(checkbox => {
        toggleDayHours(checkbox);
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('delete-modal');
            if (!modal.classList.contains('hidden')) {
                closeDeleteModal();
            }
        }
    });
});
</script>
@endpush