{{-- resources/views/layouts/customer.blade.php --}}
@extends('layouts.customer-base')

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
                    <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 {{ (isset($cart['items']) && count($cart['items']) > 0) ? 'flex' : 'hidden' }} items-center justify-center">
                        {{ isset($cart['items']) ? array_sum(array_column($cart['items'], 'quantity')) : 0 }}
                    </span>
                </button>
            </div>
        </div>
    </header>

    <main>
        @yield('main-content')
    </main>

    <!-- Cart Drawer -->
    <div id="cart-drawer" class="fixed inset-y-0 right-0 w-full max-w-md bg-white shadow-xl transform translate-x-full transition-transform duration-300 z-50">
        @include('partials.cart-drawer', [
            'cartItems' => isset($cart['items']) ? $cart['items'] : [],
            'subtotal' => isset($cart['subtotal']) ? $cart['subtotal'] : 0,
            'tax' => isset($cart['tax']) ? $cart['tax'] : 0,
            'serviceFee' => isset($cart['service_fee']) ? $cart['service_fee'] : 0,
            'total' => isset($cart['total']) ? $cart['total'] : 0
        ])
    </div>

    <!-- Cart Overlay -->
    <div 
        id="cart-overlay" 
        class="fixed inset-0 bg-black/50 z-40 hidden"
        onclick="closeCartDrawer()"
    ></div>
</div>

<script>
function openCartDrawer() {
    const drawer = document.getElementById('cart-drawer');
    const overlay = document.getElementById('cart-overlay');
    
    drawer.classList.remove('translate-x-full');
    overlay.classList.remove('hidden');
}

function closeCartDrawer() {
    const drawer = document.getElementById('cart-drawer');
    const overlay = document.getElementById('cart-overlay');
    
    drawer.classList.add('translate-x-full');
    overlay.classList.add('hidden');
}

// Update cart button click handler
document.addEventListener('DOMContentLoaded', function() {
    const cartToggle = document.getElementById('cart-toggle');
    if (cartToggle) {
        cartToggle.onclick = function() {
            const drawer = document.getElementById('cart-drawer');
            const overlay = document.getElementById('cart-overlay');
            
            if (drawer.classList.contains('translate-x-full')) {
                openCartDrawer();
            } else {
                closeCartDrawer();
            }
        };
    }
});

// Cart Management Functions
function updateCartQuantity(index, quantity) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        showToast('Security token missing. Please refresh the page.', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('quantity', quantity);
    formData.append('_token', csrfToken);
    formData.append('_method', 'PATCH');
    
    fetch(`/customer/cart/line/${index}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'HX-Request': 'true', // Add HTMX header to get HTML response
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(html => {
        const cartDrawer = document.getElementById('cart-drawer');
        if (cartDrawer) {
            cartDrawer.innerHTML = html;
        }
        updateCartCount();
        showToast('Cart updated!', 'success');
    })
    .catch(error => {
        console.error('Error updating cart:', error);
        showToast('Failed to update cart', 'error');
    });
}

function removeCartItem(index, itemName) {
    // Create custom confirmation modal
    const confirmModal = createConfirmModal(
        'Remove Item',
        `Are you sure you want to remove "${itemName}" from your cart?`,
        'Remove',
        'Cancel'
    );
    
    document.body.appendChild(confirmModal);
    
    // Focus management
    const confirmButton = confirmModal.querySelector('.confirm-remove');
    const cancelButton = confirmModal.querySelector('.cancel-remove');
    
    confirmButton.onclick = function() {
        document.body.removeChild(confirmModal);
        performCartRemoval(index, itemName);
    };
    
    cancelButton.onclick = function() {
        document.body.removeChild(confirmModal);
    };
    
    // Close on overlay click
    confirmModal.onclick = function(e) {
        if (e.target === confirmModal) {
            document.body.removeChild(confirmModal);
        }
    };
    
    // Close on escape key
    document.addEventListener('keydown', function escapeHandler(e) {
        if (e.key === 'Escape') {
            document.removeEventListener('keydown', escapeHandler);
            if (document.body.contains(confirmModal)) {
                document.body.removeChild(confirmModal);
            }
        }
    });
}

function performCartRemoval(index, itemName) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        showToast('Security token missing. Please refresh the page.', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('_token', csrfToken);
    formData.append('_method', 'DELETE');
    
    fetch(`/customer/cart/line/${index}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'HX-Request': 'true', // Add HTMX header to get HTML response
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(html => {
        const cartDrawer = document.getElementById('cart-drawer');
        if (cartDrawer) {
            cartDrawer.innerHTML = html;
        }
        updateCartCount();
        showToast(`"${itemName}" removed from cart`, 'success');
    })
    .catch(error => {
        console.error('Error removing item:', error);
        showToast('Failed to remove item', 'error');
    });
}

function createConfirmModal(title, message, confirmText, cancelText) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50';
    modal.innerHTML = `
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 p-6 transform transition-all">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-lg font-serif font-medium text-gray-900 text-center mb-2">${title}</h3>
            <p class="text-sm text-gray-600 text-center mb-6">${message}</p>
            <div class="flex space-x-3">
                <button class="cancel-remove flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                    ${cancelText}
                </button>
                <button class="confirm-remove flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors">
                    ${confirmText}
                </button>
            </div>
        </div>
    `;
    return modal;
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
@endsection
