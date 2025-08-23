<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RestaurantPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['owner', 'manager', 'admin']);
    }

    public function view(User $user, Restaurant $restaurant): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->tenant_id !== $restaurant->tenant_id) {
            return false;
        }

        return $user->hasAnyRole(['owner', 'manager']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('owner');
    }

    public function update(User $user, Restaurant $restaurant): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->tenant_id !== $restaurant->tenant_id) {
            return false;
        }

        // Managers can only edit restaurants they manage
        if ($user->hasRole('manager')) {
            return $user->restaurants()->where('id', $restaurant->id)->exists();
        }

        return $user->hasRole('owner');
    }

    public function delete(User $user, Restaurant $restaurant): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->tenant_id !== $restaurant->tenant_id) {
            return false;
        }

        // Only owners can delete restaurants
        return $user->hasRole('owner');
    }
}
