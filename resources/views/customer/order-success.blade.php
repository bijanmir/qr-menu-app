<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta http-equiv="refresh" content="30;url={{ route('customer.menu', ['menu' => request()->route('menu')]) }}">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            <!-- Success Icon -->
            <div class="text-center mb-8">
                <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Order Confirmed!</h1>
                <p class="text-gray-600">Thank you for your order. We'll get started on it right away.</p>
            </div>

            <!-- Order Details Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Order Details</h2>
                        <span class="text-sm text-gray-500">Order #{{ $order->id }}</span>
                    </div>
                    
                    <!-- Restaurant Info -->
                    <div class="mb-4 pb-4 border-b">
                        <h3 class="font-medium text-gray-900">{{ $order->restaurant->name }}</h3>
                        @if($order->restaurant->address)
                            <p class="text-sm text-gray-600">{{ $order->restaurant->address }}</p>
                        @endif
                        @if($order->restaurant->phone)
                            <p class="text-sm text-gray-600">{{ $order->restaurant->phone }}</p>
                        @endif
                    </div>

                    <!-- Customer Info -->
                    <div class="mb-4 pb-4 border-b">
                        <h3 class="font-medium text-gray-900 mb-2">Customer Information</h3>
                        <p class="text-sm text-gray-600">{{ $order->customer_name }}</p>
                        <p class="text-sm text-gray-600">{{ $order->customer_phone }}</p>
                        @if($order->customer_email)
                            <p class="text-sm text-gray-600">{{ $order->customer_email }}</p>
                        @endif
                    </div>

                    <!-- Service Type & Table -->
                    <div class="mb-4 pb-4 border-b">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700">Service Type:</span>
                            <span class="text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $order->service_type) }}</span>
                        </div>
                        @if($order->table_id)
                            <div class="flex justify-between mt-1">
                                <span class="text-sm font-medium text-gray-700">Table:</span>
                                <span class="text-sm text-gray-900">{{ $order->table->name ?? 'Table ' . $order->table_id }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Order Items -->
                    <div class="mb-4">
                        <h3 class="font-medium text-gray-900 mb-3">Order Items</h3>
                        <div class="space-y-3">
                            @foreach($order->items as $item)
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <span class="text-sm bg-gray-100 rounded-full w-6 h-6 flex items-center justify-center mr-3">{{ $item->quantity }}</span>
                                            <span class="font-medium text-gray-900">{{ $item->name }}</span>
                                        </div>
                                        
                                        @if($item->modifiers && count($item->modifiers) > 0)
                                            <div class="ml-9 mt-1 space-y-1">
                                                @foreach($item->modifiers as $modifier)
                                                    <div class="text-sm text-gray-600">+ {{ $modifier['name'] }}</div>
                                                @endforeach
                                            </div>
                                        @endif
                                        
                                        @if($item->special_instructions)
                                            <div class="ml-9 mt-1 text-sm text-gray-500 italic">
                                                Note: {{ $item->special_instructions }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="font-medium text-gray-900">${{ number_format($item->total, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Order Totals -->
                    <div class="border-t pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="text-gray-900">${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        @if($order->tax > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tax</span>
                                <span class="text-gray-900">${{ number_format($order->tax, 2) }}</span>
                            </div>
                        @endif
                        @if($order->tip > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tip</span>
                                <span class="text-gray-900">${{ number_format($order->tip, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between font-bold text-lg border-t pt-2">
                            <span class="text-gray-900">Total</span>
                            <span class="text-gray-900">${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Order Status -->
                <div class="mb-6">
                    <div class="flex items-center justify-center space-x-2 p-3 bg-yellow-50 rounded-lg">
                        <div class="w-3 h-3 bg-yellow-400 rounded-full animate-pulse"></div>
                        <span class="text-yellow-800 font-medium">Order Status: {{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                    </div>
                </div>

                <!-- Estimated Time -->
                @if(isset($estimated_time))
                    <div class="text-center mb-6">
                        <p class="text-sm text-gray-600">Estimated preparation time</p>
                        <p class="text-xl font-bold text-gray-900">{{ $estimated_time }} minutes</p>
                    </div>
                @endif

                <!-- Special Instructions -->
                @if($order->notes)
                    <div class="mb-6 p-3 bg-gray-50 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-1">Special Instructions</h4>
                        <p class="text-sm text-gray-600">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                @if($order->payment_method === 'card' && $order->payment_status !== 'paid')
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-blue-800 text-sm">Payment processing... Please wait for confirmation.</span>
                        </div>
                    </div>
                @elseif($order->payment_method === 'cash')
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-green-800 text-sm">Please pay at the restaurant when you pick up your order.</span>
                        </div>
                    </div>
                @endif

                <!-- Order Tracking (if available) -->
                @if(isset($track_order_url))
                    <a href="{{ $track_order_url }}" 
                       class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center font-semibold py-3 px-4 rounded-lg transition-colors">
                        Track Your Order
                    </a>
                @endif

                <!-- Back to Menu -->
                <a href="{{ route('customer.menu', ['menu' => $order->menu_id ?? request()->route('menu')]) }}" 
                   class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-800 text-center font-semibold py-3 px-4 rounded-lg transition-colors">
                    Back to Menu
                </a>

                <!-- Contact Restaurant -->
                @if($order->restaurant->phone)
                    <a href="tel:{{ $order->restaurant->phone }}" 
                       class="block w-full bg-white hover:bg-gray-50 text-gray-600 text-center font-semibold py-3 px-4 rounded-lg border border-gray-300 transition-colors">
                        Contact Restaurant
                    </a>
                @endif
            </div>

            <!-- Auto-refresh Notice -->
            <div class="text-center mt-6">
                <p class="text-xs text-gray-500">This page will automatically return to the menu in <span id="countdown">30</span> seconds</p>
            </div>
        </div>
    </div>

    <script>
        // Countdown timer
        let seconds = 30;
        const countdownElement = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(timer);
            }
        }, 1000);

        // Optional: Real-time order status updates via polling or WebSocket
        // This would update the order status without page refresh
        function updateOrderStatus() {
            fetch(`/api/orders/{{ $order->id }}/status`)
                .then(response => response.json())
                .then(data => {
                    // Update status display
                    console.log('Order status:', data.status);
                })
                .catch(error => console.error('Error:', error));
        }

        // Poll for status updates every 30 seconds
        setInterval(updateOrderStatus, 30000);
    </script>
</body>
</html>