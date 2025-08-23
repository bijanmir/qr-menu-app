<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['owner', 'manager', 'admin']);
    }

    public function view(User $user, Order $order): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->tenant_id !== $order->restaurant->tenant_id) {
            return false;
        }

        // Managers can only view orders for restaurants they manage
        if ($user->hasRole('manager')) {
            return $user->restaurants()->where('id', $order->restaurant_id)->exists();
        }

        return $user->hasRole('owner');
    }

    public function update(User $user, Order $order): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->tenant_id !== $order->restaurant->tenant_id) {
            return false;
        }

        // Managers can only update orders for restaurants they manage
        if ($user->hasRole('manager')) {
            return $user->restaurants()->where('id', $order->restaurant_id)->exists();
        }

        return $user->hasRole('owner');
    }

    public function refund(User $user, Order $order): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->tenant_id !== $order->restaurant->tenant_id) {
            return false;
        }

        // Only owners can process refunds
        return $user->hasRole('owner');
    }

    public function delete(User $user, Order $order): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->tenant_id !== $order->restaurant->tenant_id) {
            return false;
        }

        // Only owners can delete orders
        return $user->hasRole('owner');
    }
}
