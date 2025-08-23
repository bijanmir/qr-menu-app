<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Restaurant;
use App\Models\Category;
use App\Services\MenuService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    protected $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Menu::class);

        $query = Menu::with(['restaurant', 'categories'])
            ->where('tenant_id', auth()->user()->tenant_id);

        if ($request->filled('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $menus = $query->latest()->paginate(15);
        $restaurants = Restaurant::where('tenant_id', auth()->user()->tenant_id)->get();

        return view('owner.menus.index', compact('menus', 'restaurants'));
    }

    public function show(Menu $menu)
    {
        $this->authorize('view', $menu);

        $menu->load(['categories.items.modifierGroups.modifiers', 'restaurant']);

        return view('owner.menus.show', compact('menu'));
    }

    public function create()
    {
        $this->authorize('create', Menu::class);

        $restaurants = Restaurant::where('tenant_id', auth()->user()->tenant_id)->get();

        return view('owner.menus.create', compact('restaurants'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Menu::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'restaurant_id' => 'nullable|exists:restaurants,id',
            'menu_scope' => 'required|in:RestaurantLocal,GlobalTemplate',
            'theme' => 'nullable|array',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['status'] = 'draft';

        $menu = Menu::create($validated);

        return redirect()->route('owner.menus.show', $menu)
            ->with('success', 'Menu created successfully');
    }

    public function edit(Menu $menu)
    {
        $this->authorize('update', $menu);

        $restaurants = Restaurant::where('tenant_id', auth()->user()->tenant_id)->get();

        return view('owner.menus.edit', compact('menu', 'restaurants'));
    }

    public function update(Request $request, Menu $menu)
    {
        $this->authorize('update', $menu);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string|max:1000',
            'restaurant_id' => 'sometimes|nullable|exists:restaurants,id',
            'theme' => 'sometimes|nullable|array',
            'schedule' => 'sometimes|nullable|array',
        ]);

        $menu->update($validated);

        if (request()->headers->get('HX-Request')) {
            return response('', 200);
        }

        return redirect()->route('owner.menus.show', $menu)
            ->with('success', 'Menu updated successfully');
    }

    public function duplicate(Menu $menu)
    {
        $this->authorize('create', Menu::class);

        $duplicatedMenu = $this->menuService->duplicate($menu);

        return redirect()->route('owner.menus.show', $duplicatedMenu)
            ->with('success', 'Menu duplicated successfully');
    }

    public function createLinkedCopy(Request $request, Menu $menu)
    {
        $this->authorize('create', Menu::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'restaurant_id' => 'required|exists:restaurants,id',
            'propagation_mode' => 'required|in:immediate,manual,scheduled'
        ]);

        $linkedMenu = $this->menuService->createLinkedCopy(
            $menu, 
            $validated['name'],
            $validated['restaurant_id'],
            $validated['propagation_mode']
        );

        return redirect()->route('owner.menus.show', $linkedMenu)
            ->with('success', 'Linked menu copy created successfully');
    }

    public function publish(Menu $menu)
    {
        $this->authorize('update', $menu);

        $menu->update([
            'status' => 'published',
            'published_at' => now()
        ]);

        return redirect()->back()->with('success', 'Menu published successfully');
    }

    public function createCategory(Request $request, Menu $menu)
    {
        $this->authorize('update', $menu);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:10',
        ]);

        $validated['menu_id'] = $menu->id;
        $validated['sort_index'] = $menu->categories()->max('sort_index') + 1;

        $category = Category::create($validated);

        if (request()->headers->get('HX-Request')) {
            return view('partials.category-builder', compact('category'));
        }

        return redirect()->back()->with('success', 'Category created successfully');
    }
}
