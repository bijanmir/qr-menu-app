{{-- Luxury Fine Dining Item Card --}}
<div class="group relative bg-white rounded-2xl shadow-lg border border-gray-200/60 overflow-hidden cursor-pointer transform transition-all duration-500 hover:scale-[1.01] hover:shadow-2xl hover:border-amber-200/80 luxury-card"
     hx-get="{{ route('customer.item.modal', $item->id) }}"
     hx-target="body"
     hx-swap="beforeend">
    
    <!-- Background Gradient Overlay (appears on hover) -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/0 to-purple-50/0 group-hover:from-blue-50/30 group-hover:to-purple-50/30 transition-all duration-500 pointer-events-none"></div>
    
    <!-- Popular/Featured Badge -->
    @if($item->is_popular || $item->is_featured)
        <div class="absolute top-4 left-4 z-10">
            <div class="flex items-center space-x-2">
                @if($item->is_popular)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-orange-400 to-red-500 text-white shadow-lg animate-pulse-slow">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        Popular
                    </span>
                @endif
                @if($item->is_featured)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-purple-500 to-pink-600 text-white shadow-lg">
                        ✨ Featured
                    </span>
                @endif
            </div>
        </div>
    @endif

    <!-- Availability Status -->
    @if($item->is_86ed)
        <div class="absolute top-4 right-4 z-10">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                Unavailable
            </span>
        </div>
    @endif

    <div class="relative flex h-full">
        <!-- Premium Image Section -->
        @if($item->image)
            <div class="w-32 h-32 flex-shrink-0 relative overflow-hidden">
                <img src="{{ asset('storage/' . $item->image) }}" 
                     alt="{{ $item->name }}" 
                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 {{ $item->is_86ed ? 'grayscale opacity-60' : '' }}">
                
                <!-- Image Overlay Gradient -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                
                <!-- Quick View Icon (appears on hover) -->
                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 transform scale-50 group-hover:scale-100">
                    <div class="bg-white/90 backdrop-blur-sm rounded-full p-3 shadow-xl">
                        <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        @else
            <!-- Premium Placeholder -->
            <div class="w-32 h-32 flex-shrink-0 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center relative overflow-hidden group-hover:from-blue-50 group-hover:to-purple-50 transition-all duration-300">
                <svg class="w-12 h-12 text-gray-400 group-hover:text-blue-400 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <!-- Animated background pattern -->
                <div class="absolute inset-0 bg-gradient-to-br from-transparent via-white/20 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
            </div>
        @endif
        
        <!-- Content Section -->
        <div class="flex-1 px-8 py-6 flex flex-col justify-between relative">
            <!-- Header Section -->
            <div class="mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1 pr-4">
                        <h3 class="font-serif text-xl font-medium text-gray-900 group-hover:text-amber-900 transition-colors duration-300 leading-tight tracking-wide {{ $item->is_86ed ? 'opacity-60' : '' }}">
                            {{ $item->name }}
                        </h3>
                    </div>
                    <div class="flex flex-col items-end space-y-1">
                        <div class="text-right">
                            <span class="font-light text-2xl text-amber-700 group-hover:text-amber-800 transition-all duration-300 tracking-wider">
                                {{ $item->formatted_price }}
                            </span>
                        </div>
                        @if($item->original_price && $item->original_price > $item->price)
                            <div class="text-right space-y-1">
                                <span class="text-sm text-gray-500 line-through font-light">
                                    ${{ number_format($item->original_price, 2) }}
                                </span>
                                <div class="text-xs font-medium text-red-700 bg-red-50 px-3 py-1 rounded-full border border-red-200">
                                    {{ round((($item->original_price - $item->price) / $item->original_price) * 100) }}% OFF
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                
                @if($item->description)
                    <div class="mb-4">
                        <p class="text-sm text-gray-700 leading-relaxed line-clamp-3 font-light italic tracking-wide {{ $item->is_86ed ? 'opacity-60' : '' }}">
                            {{ $item->description }}
                        </p>
                    </div>
                @endif
            </div>
            
            <!-- Dietary Information & Tags -->
            <div class="mb-5">
                <div class="flex flex-wrap items-center gap-3 mb-3">
                    <!-- Elegant Dietary Indicators -->
                    @if($item->is_vegetarian || $item->is_vegan || $item->is_gluten_free || $item->is_spicy)
                        <div class="flex items-center space-x-3">
                            @if($item->is_vegetarian)
                                <div class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm" title="Vegetarian">
                                    <span class="text-xs font-semibold tracking-wide">V</span>
                                </div>
                            @endif
                            @if($item->is_vegan)
                                <div class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm" title="Vegan">
                                    <span class="text-[10px] font-semibold tracking-wider">VG</span>
                                </div>
                            @endif
                            @if($item->is_gluten_free)
                                <div class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-sky-50 text-sky-700 border border-sky-200 shadow-sm" title="Gluten Free">
                                    <span class="text-[10px] font-semibold tracking-wider">GF</span>
                                </div>
                            @endif
                            @if($item->is_spicy)
                                <div class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-orange-50 text-orange-700 border border-orange-200 shadow-sm" title="Spicy">
                                    <span class="text-xs">🌶</span>
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Refined Custom Tags -->
                    @if($item->tags)
                        <div class="flex items-center space-x-2">
                            @foreach($item->tags as $tag)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-slate-50 to-gray-50 text-slate-700 border border-slate-200 shadow-sm hover:from-slate-100 hover:to-gray-100 transition-colors duration-200">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                    
                    <!-- Allergen Notices -->
                    @if($item->allergens)
                        <div class="flex items-center space-x-2">
                            @foreach($item->allergens as $allergen)
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $allergen }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Elegant Footer Section -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <!-- Refined Rating Display -->
                @if($item->totalRatings() > 0)
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center space-x-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3.5 h-3.5 {{ $i <= floor($item->averageRating()) ? 'text-amber-400' : 'text-gray-300' }} transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                        </div>
                        <div class="flex items-baseline space-x-1 text-xs">
                            <span class="font-medium text-gray-800">{{ number_format($item->averageRating(), 1) }}</span>
                            <span class="text-gray-500 font-light">({{ $item->totalRatings() }})</span>
                        </div>
                    </div>
                @else
                    <div class="flex items-center text-xs text-gray-500 font-light">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        New dish
                    </div>
                @endif
                
                <!-- Sophisticated Action Button -->
                <div class="flex items-center space-x-3">
                    <!-- Customization Indicator -->
                    @if($item->hasModifiers())
                        <div class="flex items-center text-xs text-slate-600 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-200 shadow-sm" title="Customizable Options Available">
                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                            </svg>
                            <span class="font-medium tracking-wide">Customize</span>
                        </div>
                    @endif
                    
                    <!-- Primary Action Button -->
                    @if(!$item->is_86ed && !$item->hasModifiers())
                        <button class="flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-medium rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                onclick="event.stopPropagation(); quickAddToCart({{ $item->id }}, this)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span class="tracking-wide">Add to Order</span>
                        </button>
                    @else
                        <div class="flex items-center text-xs text-amber-700 bg-amber-50 px-4 py-2 rounded-lg border border-amber-200 shadow-sm" title="Click to view details and options">
                            <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <span class="font-medium tracking-wide">View Details</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Hover Effect Border -->
    <div class="absolute inset-0 rounded-3xl border-2 border-transparent group-hover:border-gradient-to-r group-hover:from-blue-400 group-hover:to-purple-400 transition-all duration-300 pointer-events-none"></div>
</div>

<style>
/* Luxury card animations and effects */
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&display=swap');

@keyframes animate-pulse-slow {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.85; }
}

@keyframes luxury-glow {
    0%, 100% { box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); }
    50% { box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15); }
}

.animate-pulse-slow {
    animation: animate-pulse-slow 3s infinite;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Luxury card styling */
.luxury-card {
    backdrop-filter: blur(8px);
    background: linear-gradient(145deg, #ffffff 0%, #fefefe 100%);
    border: 1px solid rgba(0, 0, 0, 0.08);
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    position: relative;
    overflow: hidden;
}

.luxury-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, 
        rgba(251, 191, 36, 0.03) 0%, 
        rgba(245, 158, 11, 0.05) 25%, 
        rgba(239, 68, 68, 0.03) 50%, 
        rgba(139, 92, 246, 0.04) 75%, 
        rgba(59, 130, 246, 0.03) 100%);
    opacity: 0;
    transition: opacity 0.4s ease;
    pointer-events: none;
    border-radius: inherit;
}

.luxury-card:hover::before {
    opacity: 1;
}

.luxury-card:hover {
    transform: translateY(-2px) scale(1.005);
    box-shadow: 
        0 20px 40px -8px rgba(0, 0, 0, 0.15),
        0 8px 24px -4px rgba(251, 191, 36, 0.1),
        0 4px 16px -2px rgba(0, 0, 0, 0.08);
    border-color: rgba(251, 191, 36, 0.2);
}

/* Typography refinements */
.font-serif {
    font-family: 'Playfair Display', 'Georgia', serif;
    font-feature-settings: 'liga', 'kern';
}

/* Premium button styling */
.luxury-card button:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 24px -4px rgba(16, 185, 129, 0.4);
}

/* Refined backdrop blur effect */
@supports (backdrop-filter: blur()) {
    .luxury-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px) saturate(180%);
    }
}

/* Improved responsiveness */
@media (max-width: 768px) {
    .luxury-card {
        transform: none;
    }
    
    .luxury-card:hover {
        transform: none;
        box-shadow: 0 12px 32px -8px rgba(0, 0, 0, 0.12);
    }
}

/* Loading state animation */
.luxury-card.loading {
    animation: luxury-glow 2s ease-in-out infinite;
}

/* Focus states for accessibility */
.luxury-card:focus-within {
    outline: 2px solid rgba(251, 191, 36, 0.4);
    outline-offset: 2px;
}
</style>

<script>
function quickAddToCart(itemId, button) {
    // Disable button and show loading state
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = `
        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="m100 50c0 27.614-22.386 50-50 50s-50-22.386-50-50 22.386-50 50-50 50 22.386 50 50z"></path>
        </svg>
        <span class="ml-2">Adding...</span>
    `;
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        console.error('CSRF token not found');
        showToast('Security token missing. Please refresh the page.', 'error');
        button.disabled = false;
        button.innerHTML = originalText;
        return;
    }
    
    // Prepare form data
    const formData = new FormData();
    formData.append('item_id', itemId);
    formData.append('quantity', 1);
    formData.append('_token', csrfToken);
    
    fetch('/customer/cart/add', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success !== false) {
            showToast('Item added to your order!', 'success');
            updateCartCount();
            updateCartDrawer();
        } else {
            throw new Error(data.message || 'Failed to add item');
        }
    })
    .catch(error => {
        console.error('Error adding item to cart:', error);
        showToast(error.message || 'Failed to add item to cart', 'error');
    })
    .finally(() => {
        // Restore button state
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

function updateCartCount() {
    fetch('/customer/cart/count', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        const cartCountElement = document.getElementById('cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = data.count || 0;
            if (data.count > 0) {
                cartCountElement.classList.remove('hidden');
                cartCountElement.classList.add('flex');
            } else {
                cartCountElement.classList.add('hidden');
                cartCountElement.classList.remove('flex');
            }
        }
    })
    .catch(error => {
        console.error('Error updating cart count:', error);
    });
}

function updateCartDrawer() {
    // Fetch updated cart drawer content
    fetch('/customer/cart/drawer', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.text())
    .then(html => {
        const cartDrawer = document.getElementById('cart-drawer');
        if (cartDrawer) {
            cartDrawer.innerHTML = html;
        }
    })
    .catch(error => {
        console.error('Error updating cart drawer:', error);
    });
}

function showToast(message, type = 'success') {
    // Remove any existing toasts
    const existingToasts = document.querySelectorAll('.toast-notification');
    existingToasts.forEach(toast => toast.remove());
    
    // Create new toast
    const toast = document.createElement('div');
    toast.className = `toast-notification fixed top-4 right-4 z-50 flex items-center p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full max-w-sm ${
        type === 'success' 
            ? 'bg-green-500 text-white' 
            : 'bg-red-500 text-white'
    }`;
    
    const icon = type === 'success' 
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
    
    toast.innerHTML = `
        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            ${icon}
        </svg>
        <span class="text-sm font-medium">${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-4 text-white/80 hover:text-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Auto-remove after 4 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 300);
    }, 4000);
}
</script>
