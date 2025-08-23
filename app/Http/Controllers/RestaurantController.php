<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Restaurant::class);

        $restaurants = Restaurant::where('tenant_id', auth()->user()->tenant_id)
            ->withCount(['menus', 'tables'])
            ->latest()
            ->paginate(15);

        return view('owner.restaurants.index', compact('restaurants'));
    }

    public function show(Restaurant $restaurant)
    {
        $this->authorize('view', $restaurant);

        $restaurant->load(['menus', 'tables']);

        return view('owner.restaurants.show', compact('restaurant'));
    }

    public function create()
    {
        $this->authorize('create', Restaurant::class);

        return view('owner.restaurants.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Restaurant::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
            'cover_image' => 'nullable|image|max:4096',
            'subdomain' => 'required|string|max:50|unique:restaurants,subdomain',
            'cuisine_type' => 'nullable|string|max:100',
            'price_range' => 'nullable|in:$,$$$,$$$$',
            'hours' => 'nullable|array',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['slug'] = Str::slug($validated['name']);

        // Handle file uploads
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('restaurants/logos', 'public');
        }

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('restaurants/covers', 'public');
        }

        $restaurant = Restaurant::create($validated);

        return redirect()->route('owner.restaurants.show', $restaurant)
            ->with('success', 'Restaurant created successfully');
    }

    public function edit(Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);

        return view('owner.restaurants.edit', compact('restaurant'));
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string|max:1000',
            'address' => 'sometimes|string|max:500',
            'phone' => 'sometimes|nullable|string|max:20',
            'email' => 'sometimes|nullable|email|max:255',
            'website' => 'sometimes|nullable|url|max:255',
            'logo' => 'sometimes|nullable|image|max:2048',
            'cover_image' => 'sometimes|nullable|image|max:4096',
            'subdomain' => 'sometimes|string|max:50|unique:restaurants,subdomain,' . $restaurant->id,
            'cuisine_type' => 'sometimes|nullable|string|max:100',
            'price_range' => 'sometimes|nullable|in:$,$$$,$$$$',
            'hours' => 'sometimes|nullable|array',
            'active' => 'sometimes|boolean',
        ]);

        // Handle file uploads
        if ($request->hasFile('logo')) {
            if ($restaurant->logo) {
                \Storage::disk('public')->delete($restaurant->logo);
            }
            $validated['logo'] = $request->file('logo')->store('restaurants/logos', 'public');
        }

        if ($request->hasFile('cover_image')) {
            if ($restaurant->cover_image) {
                \Storage::disk('public')->delete($restaurant->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('restaurants/covers', 'public');
        }

        $restaurant->update($validated);

        if (request()->headers->get('HX-Request')) {
            return response('', 200);
        }

        return redirect()->route('owner.restaurants.show', $restaurant)
            ->with('success', 'Restaurant updated successfully');
    }

    public function destroy(Restaurant $restaurant)
    {
        $this->authorize('delete', $restaurant);

        // Delete associated files
        if ($restaurant->logo) {
            \Storage::disk('public')->delete($restaurant->logo);
        }

        if ($restaurant->cover_image) {
            \Storage::disk('public')->delete($restaurant->cover_image);
        }

        $restaurant->delete();

        return redirect()->route('owner.restaurants.index')
            ->with('success', 'Restaurant deleted successfully');
    }

    public function createTable(Request $request, Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:tables,code',
            'capacity' => 'required|integer|min:1|max:20',
        ]);

        $validated['restaurant_id'] = $restaurant->id;

        $table = Table::create($validated);

        if (request()->headers->get('HX-Request')) {
            return view('partials.table-row', compact('table'));
        }

        return redirect()->back()->with('success', 'Table created successfully');
    }

    public function destroyTable(Restaurant $restaurant, Table $table)
    {
        $this->authorize('update', $restaurant);

        $table->delete();

        if (request()->headers->get('HX-Request')) {
            return response('', 200);
        }

        return redirect()->back()->with('success', 'Table deleted successfully');
    }
}
