<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Events\OrderStatusUpdated;

class OrderService
{
    public function updateStatus(Order $order, string $status): void
    {
        $oldStatus = $order->status;
        
        $order->update(['status' => $status]);
        
        // Set appropriate timestamps
        switch ($status) {
            case 'accepted':
                $order->update(['accepted_at' => now()]);
                break;
            case 'ready':
                $order->update(['ready_at' => now()]);
                break;
            case 'served':
                $order->update(['served_at' => now()]);
                break;
        }

        // Broadcast status change
        broadcast(new OrderStatusUpdated($order, $oldStatus))->toOthers();
        
        // Send notifications if needed
        if ($status === 'ready') {
            // Notify customer their order is ready
            $this->notifyCustomerOrderReady($order);
        }
    }

    public function updateItemStatus(OrderItem $orderItem, string $status): void
    {
        $orderItem->update(['status' => $status]);
        
        // Check if all items are ready to update order status
        if ($status === 'ready') {
            $allItemsReady = $orderItem->order->items()
                ->where('status', '!=', 'ready')
                ->doesntExist();
                
            if ($allItemsReady && $orderItem->order->status !== 'ready') {
                $this->updateStatus($orderItem->order, 'ready');
            }
        }
    }

    public function calculateRefund(Order $order, array $itemIds = []): float
    {
        if (empty($itemIds)) {
            return $order->total;
        }

        $refundAmount = 0;
        $orderItems = $order->items()->whereIn('id', $itemIds)->get();
        
        foreach ($orderItems as $item) {
            $refundAmount += $item->price * $item->quantity;
        }

        // Calculate proportional tax and fees
        $itemsSubtotal = $order->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
        
        if ($itemsSubtotal > 0) {
            $refundRatio = $refundAmount / $itemsSubtotal;
            $refundAmount += ($order->taxes * $refundRatio);
            $refundAmount += ($order->service_fees * $refundRatio);
        }

        return round($refundAmount, 2);
    }

    private function notifyCustomerOrderReady(Order $order): void
    {
        // Implementation for customer notifications
        // Could be SMS, push notification, or table display
    }
}
