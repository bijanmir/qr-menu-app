<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Item $item): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->tenant_id !== $item->menu->tenant_id) {
            return false;
        }

        return $user->hasAnyRole(['owner', 'manager']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['owner', 'manager']);
    }

    public function update(User $user, Item $item): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->tenant_id !== $item->menu->tenant_id) {
            return false;
        }

        // Managers can only edit items for restaurants they manage
        if ($user->hasRole('manager') && $item->menu->restaurant_id) {
            return $user->restaurants()->where('id', $item->menu->restaurant_id)->exists();
        }

        return $user->hasAnyRole(['owner', 'manager']);
    }

    public function delete(User $user, Item $item): bool
    {
        return $this->update($user, $item);
    }
}
