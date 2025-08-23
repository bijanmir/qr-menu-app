<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function dashboard()
    {
        Gate::authorize('view-all-tenants');

        $metrics = [
            'total_tenants' => Tenant::count(),
            'total_restaurants' => Restaurant::count(),
            'total_users' => User::count(),
            'total_orders_today' => Order::whereDate('created_at', today())->count(),
            'revenue_today' => Order::whereDate('created_at', today())->sum('total'),
        ];

        // Recent activity
        $recentTenants = Tenant::latest()->take(5)->get();
        $recentOrders = Order::with(['restaurant', 'table'])->latest()->take(10)->get();

        return view('admin.dashboard', compact('metrics', 'recentTenants', 'recentOrders'));
    }

    public function tenants(Request $request)
    {
        Gate::authorize('view-all-tenants');

        $query = Tenant::withCount(['users', 'restaurants']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $tenants = $query->latest()->paginate(20);

        return view('admin.tenants.index', compact('tenants'));
    }

    public function showTenant(Tenant $tenant)
    {
        Gate::authorize('view-all-tenants');

        $tenant->load(['users', 'restaurants.menus', 'restaurants.orders']);

        $metrics = [
            'total_users' => $tenant->users()->count(),
            'total_restaurants' => $tenant->restaurants()->count(),
            'total_orders' => Order::whereHas('restaurant', function($query) use ($tenant) {
                $query->where('tenant_id', $tenant->id);
            })->count(),
            'total_revenue' => Order::whereHas('restaurant', function($query) use ($tenant) {
                $query->where('tenant_id', $tenant->id);
            })->sum('total'),
        ];

        return view('admin.tenants.show', compact('tenant', 'metrics'));
    }

    public function impersonate(Tenant $tenant, User $user)
    {
        Gate::authorize('impersonate-users');

        if ($user->tenant_id !== $tenant->id) {
            abort(404);
        }

        session(['impersonate_user_id' => $user->id]);
        auth()->login($user);

        return redirect()->route('owner.dashboard')
            ->with('success', 'Now impersonating ' . $user->name);
    }

    public function audits(Request $request)
    {
        Gate::authorize('view-audit-logs');

        // Implementation depends on audit log package
        // Example with spatie/laravel-activitylog:
        /*
        $audits = \Spatie\Activitylog\Models\Activity::with(['subject', 'causer'])
            ->latest()
            ->paginate(50);
        */

        $audits = collect(); // Placeholder

        return view('admin.audits.index', compact('audits'));
    }

    public function featureFlags()
    {
        Gate::authorize('manage-feature-flags');

        // Feature flags implementation
        $features = [
            'online_ordering' => config('features.online_ordering', true),
            'qr_menu_customization' => config('features.qr_menu_customization', true),
            'analytics_dashboard' => config('features.analytics_dashboard', true),
            'multi_location_support' => config('features.multi_location_support', false),
        ];

        return view('admin.feature-flags.index', compact('features'));
    }
}
