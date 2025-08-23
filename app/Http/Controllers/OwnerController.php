<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Order;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OwnerController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        // Get user's restaurants
        $restaurants = Restaurant::where('tenant_id', $user->tenant_id)->get();
        
        // Calculate dashboard metrics
        $metrics = [
            'total_restaurants' => $restaurants->count(),
            'total_menus' => Menu::where('tenant_id', $user->tenant_id)->count(),
            'active_menus' => Menu::where('tenant_id', $user->tenant_id)->where('status', 'published')->count(),
            'total_orders_today' => Order::whereHas('restaurant', function($query) use ($user) {
                $query->where('tenant_id', $user->tenant_id);
            })->whereDate('created_at', today())->count(),
            'revenue_today' => Order::whereHas('restaurant', function($query) use ($user) {
                $query->where('tenant_id', $user->tenant_id);
            })->whereDate('created_at', today())->sum('total'),
        ];
        
        // Recent orders
        $recentOrders = Order::whereHas('restaurant', function($query) use ($user) {
            $query->where('tenant_id', $user->tenant_id);
        })->with(['restaurant', 'table'])->latest()->take(10)->get();
        
        // Popular items
        $popularItems = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('restaurants', 'orders.restaurant_id', '=', 'restaurants.id')
            ->where('restaurants.tenant_id', $user->tenant_id)
            ->where('orders.created_at', '>=', now()->subDays(7))
            ->select('order_items.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->groupBy('order_items.name')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();
        
        return view('owner.dashboard', compact('restaurants', 'metrics', 'recentOrders', 'popularItems'));
    }
    
    public function analytics()
    {
        $user = auth()->user();
        
        // Revenue analytics
        $revenueData = Order::whereHas('restaurant', function($query) use ($user) {
            $query->where('tenant_id', $user->tenant_id);
        })
        ->where('created_at', '>=', now()->subDays(30))
        ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as orders')
        ->groupBy('date')
        ->orderBy('date')
        ->get();
        
        return view('owner.analytics', compact('revenueData'));
    }
    
    public function reports()
    {
        return view('owner.reports');
    }
}
