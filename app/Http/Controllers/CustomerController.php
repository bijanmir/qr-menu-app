<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Restaurant;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    public function menu(Restaurant $restaurant, Menu $menu): View
    {
        // Verify the menu belongs to this restaurant
        if ($menu->restaurant_id !== $restaurant->id) {
            abort(404, 'Menu not found for this restaurant');
        }

        // Verify menu is published and active
        if ($menu->status !== 'published') {
            abort(404, 'Menu is not available');
        }

        // Load categories with their visible and available items
        $categories = $menu->categories()
            ->where('visible', true)
            ->with([
                'items' => function ($query) {
                    $query->where('visible', true)
                          ->where('available', true)
                          ->orderBy('sort_index')
                          ->with([
                              'modifierGroups' => function ($modifierQuery) {
                                  $modifierQuery->orderBy('sort_index')
                                               ->with(['modifiers' => function ($modQuery) {
                                                   $modQuery->orderBy('sort_index');
                                               }]);
                              }
                          ]);
                }
            ])
            ->orderBy('sort_index')
            ->get();

        // Filter out categories that have no available items
        $categories = $categories->filter(function ($category) {
            return $category->items->isNotEmpty();
        });

        // Get some additional data for the view
        $totalItems = $categories->sum(function ($category) {
            return $category->items->count();
        });

        $featuredItems = collect();
        $popularItems = collect();

        foreach ($categories as $category) {
            $featuredItems = $featuredItems->merge(
                $category->items->where('is_featured', true)
            );
            $popularItems = $popularItems->merge(
                $category->items->where('is_popular', true)
            );
        }

        // Get current cart data
        $cart = $this->cartService->getCart();

        return view('customer.menu', compact(
            'restaurant',
            'menu',
            'categories',
            'totalItems',
            'featuredItems',
            'popularItems',
            'cart'
        ));
    }

    public function restaurant(Restaurant $restaurant): View
    {
        // Get the default/primary menu for this restaurant
        $menu = $restaurant->menus()
            ->where('status', 'published')
            ->orderBy('created_at')
            ->first();

        if (!$menu) {
            abort(404, 'No menu available for this restaurant');
        }

        // Redirect to the menu route
        return redirect()->route('customer.restaurant.menu', [
            'restaurant' => $restaurant->slug,
            'menu' => $menu->id
        ]);
    }

    public function table(Restaurant $restaurant, $tableCode): View
    {
        // Find the table by code
        $table = $restaurant->tables()
            ->where('code', $tableCode)
            ->where('active', true)
            ->first();

        if (!$table) {
            abort(404, 'Table not found');
        }

        // Store table information in session
        session([
            'current_table' => [
                'table_id' => $table->id,
                'restaurant_id' => $restaurant->id,
                'table_code' => $tableCode,
                'table_name' => $table->name ?? "Table {$tableCode}",
            ]
        ]);

        // Get the default menu and redirect to it
        $menu = $restaurant->menus()
            ->where('status', 'published')
            ->orderBy('created_at')
            ->first();

        if (!$menu) {
            abort(404, 'No menu available for this restaurant');
        }

        return redirect()->route('customer.restaurant.menu', [
            'restaurant' => $restaurant->slug,
            'menu' => $menu->id
        ]);
    }

    public function category(Menu $menu, $categoryId): View
    {
        $category = $menu->categories()
            ->where('id', $categoryId)
            ->where('visible', true)
            ->with([
                'items' => function ($query) {
                    $query->where('visible', true)
                          ->where('available', true)
                          ->orderBy('sort_index')
                          ->with(['modifierGroups.modifiers']);
                }
            ])
            ->first();

        if (!$category) {
            abort(404, 'Category not found');
        }

        return view('customer.category', compact('menu', 'category'));
    }

    public function landing(Request $request): View
    {
        $subdomain = $request->route('subdomain');
        
        $restaurant = Restaurant::where('subdomain', $subdomain)
            ->where('active', true)
            ->first();

        if (!$restaurant) {
            abort(404, 'Restaurant not found');
        }

        return view('customer.landing', compact('restaurant'));
    }
}