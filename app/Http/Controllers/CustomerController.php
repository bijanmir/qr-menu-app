<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Menu;
use App\Models\Table;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CustomerController extends Controller
{
    public function restaurant(Restaurant $restaurant)
    {
        $activeMenus = $restaurant->getActiveMenus();
        
        if ($activeMenus->isEmpty()) {
            abort(404, 'No active menus found');
        }

        // Redirect to first active menu
        $menu = $activeMenus->first();
        return redirect()->route('customer.restaurant.menu', [$restaurant, $menu]);
    }

    public function table(Restaurant $restaurant, Table $table)
    {
        // Store table information in session for order tracking
        Session::put('current_table', [
            'restaurant_id' => $restaurant->id,
            'table_id' => $table->id,
            'table_code' => $table->code
        ]);

        return $this->restaurant($restaurant);
    }

    public function menu(Request $request, Restaurant $restaurant = null, Menu $menu = null)
    {
        // Handle subdomain routing
        if (!$restaurant) {
            $subdomain = $request->route('subdomain');
            $restaurant = Restaurant::where('subdomain', $subdomain)
                ->where('active', true)
                ->firstOrFail();
        }

        // Get active menu
        if (!$menu) {
            $menu = $restaurant->getActiveMenus()->first();
            if (!$menu) {
                abort(404, 'No active menus found');
            }
        }

        // Verify menu belongs to restaurant and is active
        if ($menu->restaurant_id !== $restaurant->id || !$menu->isActive()) {
            abort(404);
        }

        // Load categories with visible items
        $categories = $menu->categories()
            ->visible()
            ->with(['items' => function($query) {
                $query->visible()->available()->with('modifierGroups.modifiers');
            }])
            ->get();

        // Get current cart
        $cart = Session::get('cart', []);
        
        return view('customer.menu', compact(
            'restaurant', 
            'menu', 
            'categories', 
            'cart'
        ));
    }

    public function category(Restaurant $restaurant, Menu $menu, Category $category)
    {
        // Verify ownership chain
        if ($category->menu_id !== $menu->id || $menu->restaurant_id !== $restaurant->id) {
            abort(404);
        }

        $items = $category->items()
            ->visible()
            ->available()
            ->with('modifierGroups.modifiers')
            ->get();

        if (request()->headers->get('HX-Request')) {
            return view('partials.category-items', compact('category', 'items'));
        }

        return $this->menu(request(), $restaurant, $menu);
    }

    public function landing(Request $request)
    {
        $subdomain = $request->route('subdomain');
        $restaurant = Restaurant::where('subdomain', $subdomain)
            ->where('active', true)
            ->firstOrFail();

        return $this->restaurant($restaurant);
    }
}
