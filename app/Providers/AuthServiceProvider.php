<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\Restaurant;
use App\Models\Item;
use App\Models\Order;
use App\Policies\MenuPolicy;
use App\Policies\RestaurantPolicy;
use App\Policies\ItemPolicy;
use App\Policies\OrderPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Menu::class => MenuPolicy::class,
        Restaurant::class => RestaurantPolicy::class,
        Item::class => ItemPolicy::class,
        Order::class => OrderPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for specific permissions
        Gate::define('impersonate-users', function ($user) {
            return $user->hasRole('admin');
        });

        Gate::define('view-all-tenants', function ($user) {
            return $user->hasRole('admin');
        });

        Gate::define('manage-feature-flags', function ($user) {
            return $user->hasRole('admin');
        });

        Gate::define('view-audit-logs', function ($user) {
            return $user->hasAnyRole(['admin', 'owner']);
        });
    }
}
