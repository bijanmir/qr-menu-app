<?php

namespace App\Policies;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MenuPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['owner', 'manager', 'admin']);
    }

    public function view(User $user, Menu $menu): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        // Check if user belongs to same tenant
        if ($user->tenant_id !== $menu->tenant_id) {
            return false;
        }

        return $user->hasAnyRole(['owner', 'manager']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['owner', 'manager']);
    }

    public function update(User $user, Menu $menu): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->tenant_id !== $menu->tenant_id) {
            return false;
        }

        // Managers can only edit menus for restaurants they manage
        if ($user->hasRole('manager')) {
            return $user->restaurants()->where('id', $menu->restaurant_id)->exists();
        }

        return $user->hasRole('owner');
    }

    public function delete(User $user, Menu $menu): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->tenant_id !== $menu->tenant_id) {
            return false;
        }

        // Only owners can delete menus
        return $user->hasRole('owner');
    }
}
