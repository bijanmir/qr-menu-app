<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\ModifierGroup;
use App\Models\Modifier;
use App\Events\ItemAvailabilityUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function modal(Item $item)
    {
        $item->load('modifierGroups.modifiers', 'ratings.user');
        
        // Check if current user can rate this item
        $canRate = false;
        if (auth()->check()) {
            $canRate = auth()->user()->canRateItem($item->id);
        }

        return view('modals.item-details', compact('item', 'canRate'));
    }

    public function store(Request $request, Category $category)
    {
        $this->authorize('create', Item::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'allergens' => 'array',
            'tags' => 'array',
            'calories' => 'nullable|integer|min:0',
            'prep_station' => 'nullable|string|max:100',
        ]);

        $validated['menu_id'] = $category->menu_id;
        $validated['category_id'] = $category->id;
        $validated['sort_index'] = $category->items()->max('sort_index') + 1;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('items', 'public');
        }

        $item = Item::create($validated);

        if (request()->headers->get('HX-Request')) {
            return view('partials.item-card', compact('item'));
        }

        return redirect()->back()->with('success', 'Item created successfully');
    }

    public function update(Request $request, Item $item)
    {
        $this->authorize('update', $item);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string|max:1000',
            'price' => 'sometimes|numeric|min:0',
            'image' => 'sometimes|nullable|image|max:2048',
            'visible' => 'sometimes|boolean',
            'available' => 'sometimes|boolean',
            'allergens' => 'sometimes|array',
            'tags' => 'sometimes|array',
            'calories' => 'sometimes|nullable|integer|min:0',
            'prep_station' => 'sometimes|nullable|string|max:100',
        ]);

        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $validated['image'] = $request->file('image')->store('items', 'public');
        }

        $item->update($validated);

        if (request()->headers->get('HX-Request')) {
            // Return updated field or full item card
            $field = $request->input('field');
            if ($field) {
                return view('partials.inline-edit-success', [
                    'value' => $item->{$field},
                    'formatted' => $field === 'price' ? $item->formatted_price : $item->{$field}
                ]);
            }
            return view('partials.item-card', compact('item'));
        }

        return redirect()->back()->with('success', 'Item updated successfully');
    }

    public function destroy(Item $item)
    {
        $this->authorize('delete', $item);

        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();

        if (request()->headers->get('HX-Request')) {
            return response('', 200);
        }

        return redirect()->back()->with('success', 'Item deleted successfully');
    }

    public function toggle86(Item $item)
    {
        $this->authorize('update', $item);

        $item->update(['available' => !$item->available]);

        // Broadcast update to customers
        broadcast(new ItemAvailabilityUpdated($item))->toOthers();

        if (request()->headers->get('HX-Request')) {
            return view('partials.86-toggle', compact('item'));
        }

        return response()->json(['success' => true, 'available' => $item->available]);
    }

    public function reorder(Request $request, Item $item)
    {
        $this->authorize('update', $item);

        $validated = $request->validate([
            'sort_index' => 'required|integer|min:0'
        ]);

        $item->update($validated);

        return response()->json(['success' => true]);
    }

    // Modifier Group Management
    public function createModifierGroup(Request $request, Item $item)
    {
        $this->authorize('update', $item);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'required' => 'boolean',
            'min_selection' => 'integer|min:0',
            'max_selection' => 'nullable|integer|min:1',
        ]);

        $validated['item_id'] = $item->id;
        $validated['sort_index'] = $item->modifierGroups()->max('sort_index') + 1;

        $modifierGroup = ModifierGroup::create($validated);

        if (request()->headers->get('HX-Request')) {
            return view('partials.modifier-group', compact('modifierGroup'));
        }

        return redirect()->back()->with('success', 'Modifier group created successfully');
    }

    public function createModifier(Request $request, ModifierGroup $modifierGroup)
    {
        $this->authorize('update', $modifierGroup->item);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_adjustment' => 'numeric',
        ]);

        $validated['modifier_group_id'] = $modifierGroup->id;
        $validated['sort_index'] = $modifierGroup->modifiers()->max('sort_index') + 1;

        $modifier = Modifier::create($validated);

        if (request()->headers->get('HX-Request')) {
            return view('partials.modifier', compact('modifier'));
        }

        return redirect()->back()->with('success', 'Modifier created successfully');
    }
}
