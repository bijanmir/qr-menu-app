<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

// Restaurant orders channel for KDS
Broadcast::channel('orders.{restaurantId}', function ($user, $restaurantId) {
    // Check if user has permission to view orders for this restaurant
    return $user->hasPermissionTo("view-restaurant-orders-{$restaurantId}");
});

// Table-specific updates
Broadcast::channel('table.{restaurantId}.{tableId}', function ($user, $restaurantId, $tableId) {
    // Allow customers at the table or staff with restaurant access
    return true; // Implement proper authorization based on session/table token
});
