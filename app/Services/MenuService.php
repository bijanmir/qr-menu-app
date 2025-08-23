<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\MenuLink;
use Illuminate\Support\Facades\DB;

class MenuService
{
    public function duplicate(Menu $sourceMenu): Menu
    {
        return DB::transaction(function () use ($sourceMenu) {
            // Create new menu
            $newMenu = Menu::create([
                'tenant_id' => $sourceMenu->tenant_id,
                'restaurant_id' => $sourceMenu->restaurant_id,
                'menu_scope' => $sourceMenu->menu_scope,
                'name' => $sourceMenu->name . ' (Copy)',
                'description' => $sourceMenu->description,
                'status' => 'draft',
                'theme' => $sourceMenu->theme,
            ]);

            // Copy categories and items
            foreach ($sourceMenu->categories as $category) {
                $newCategory = $newMenu->categories()->create([
                    'name' => $category->name,
                    'sort_index' => $category->sort_index,
                    'icon' => $category->icon,
                    'visible' => $category->visible,
                ]);

                // Copy items
                foreach ($category->items as $item) {
                    $newItem = $newCategory->items()->create([
                        'menu_id' => $newMenu->id,
                        'name' => $item->name,
                        'description' => $item->description,
                        'price' => $item->price,
                        'currency' => $item->currency,
                        'sku' => $item->sku,
                        'image' => $item->image, // Note: Should copy image file too
                        'allergens' => $item->allergens,
                        'tags' => $item->tags,
                        'calories' => $item->calories,
                        'visible' => $item->visible,
                        'available' => $item->available,
                        'tax_code' => $item->tax_code,
                        'prep_station' => $item->prep_station,
                        'sort_index' => $item->sort_index,
                    ]);

                    // Copy modifier groups and modifiers
                    foreach ($item->modifierGroups as $modifierGroup) {
                        $newModifierGroup = $newItem->modifierGroups()->create([
                            'name' => $modifierGroup->name,
                            'required' => $modifierGroup->required,
                            'min_selection' => $modifierGroup->min_selection,
                            'max_selection' => $modifierGroup->max_selection,
                            'sort_index' => $modifierGroup->sort_index,
                        ]);

                        foreach ($modifierGroup->modifiers as $modifier) {
                            $newModifierGroup->modifiers()->create([
                                'name' => $modifier->name,
                                'price_adjustment' => $modifier->price_adjustment,
                                'available' => $modifier->available,
                                'sort_index' => $modifier->sort_index,
                            ]);
                        }
                    }
                }
            }

            return $newMenu;
        });
    }

    public function createLinkedCopy(Menu $sourceMenu, string $name, int $restaurantId, string $propagationMode = 'manual'): Menu
    {
        return DB::transaction(function () use ($sourceMenu, $name, $restaurantId, $propagationMode) {
            // Create linked menu (initially empty)
            $linkedMenu = Menu::create([
                'tenant_id' => $sourceMenu->tenant_id,
                'restaurant_id' => $restaurantId,
                'menu_scope' => 'RestaurantLocal',
                'name' => $name,
                'description' => $sourceMenu->description,
                'status' => 'draft',
                'theme' => $sourceMenu->theme,
            ]);

            // Create menu link relationship
            MenuLink::create([
                'linked_menu_id' => $linkedMenu->id,
                'source_menu_id' => $sourceMenu->id,
                'propagation_mode' => $propagationMode,
                'override_fields' => [],
            ]);

            // Initial sync if immediate mode
            if ($propagationMode === 'immediate') {
                $this->syncLinkedMenu($linkedMenu);
            }

            return $linkedMenu;
        });
    }

    public function syncLinkedMenu(Menu $linkedMenu): void
    {
        $menuLink = $linkedMenu->linkedFrom;
        if (!$menuLink) {
            throw new \InvalidArgumentException('Menu is not a linked copy');
        }

        $sourceMenu = $menuLink->sourceMenu;
        $overrides = $menuLink->override_fields ?? [];

        DB::transaction(function () use ($linkedMenu, $sourceMenu, $overrides) {
            // Clear existing content (except overridden items)
            $this->clearLinkedMenuContent($linkedMenu, $overrides);

            // Sync categories and items from source
            foreach ($sourceMenu->categories as $sourceCategory) {
                $linkedCategory = $linkedMenu->categories()
                    ->where('name', $sourceCategory->name)
                    ->first();

                if (!$linkedCategory) {
                    $linkedCategory = $linkedMenu->categories()->create([
                        'name' => $sourceCategory->name,
                        'sort_index' => $sourceCategory->sort_index,
                        'icon' => $sourceCategory->icon,
                        'visible' => $sourceCategory->visible,
                    ]);
                }

                foreach ($sourceCategory->items as $sourceItem) {
                    $itemOverrideKey = "item_{$sourceItem->id}";
                    
                    // Skip if item is completely overridden
                    if (isset($overrides[$itemOverrideKey]['override_completely'])) {
                        continue;
                    }

                    $linkedItem = $linkedCategory->items()
                        ->where('name', $sourceItem->name)
                        ->first();

                    $itemData = [
                        'menu_id' => $linkedMenu->id,
                        'name' => $overrides[$itemOverrideKey]['name'] ?? $sourceItem->name,
                        'description' => $overrides[$itemOverrideKey]['description'] ?? $sourceItem->description,
                        'price' => $overrides[$itemOverrideKey]['price'] ?? $sourceItem->price,
                        'currency' => $sourceItem->currency,
                        'sku' => $sourceItem->sku,
                        'image' => $sourceItem->image,
                        'allergens' => $sourceItem->allergens,
                        'tags' => $sourceItem->tags,
                        'calories' => $sourceItem->calories,
                        'visible' => $overrides[$itemOverrideKey]['visible'] ?? $sourceItem->visible,
                        'available' => $overrides[$itemOverrideKey]['available'] ?? $sourceItem->available,
                        'tax_code' => $sourceItem->tax_code,
                        'prep_station' => $sourceItem->prep_station,
                        'sort_index' => $sourceItem->sort_index,
                    ];

                    if ($linkedItem) {
                        $linkedItem->update($itemData);
                    } else {
                        $linkedItem = $linkedCategory->items()->create($itemData);
                    }

                    // Sync modifier groups (unless overridden)
                    if (!isset($overrides[$itemOverrideKey]['modifiers_override'])) {
                        $this->syncModifiers($sourceItem, $linkedItem);
                    }
                }
            }
        });

        // Update last synced timestamp
        $menuLink->update(['last_synced_at' => now()]);
    }

    private function clearLinkedMenuContent(Menu $linkedMenu, array $overrides): void
    {
        // Remove items that are not overridden
        foreach ($linkedMenu->items as $item) {
            $overrideKey = "item_{$item->id}";
            if (!isset($overrides[$overrideKey]['override_completely'])) {
                $item->delete();
            }
        }

        // Remove empty categories
        $linkedMenu->categories()->doesntHave('items')->delete();
    }

    private function syncModifiers($sourceItem, $linkedItem): void
    {
        // Clear existing modifier groups
        $linkedItem->modifierGroups()->delete();

        // Copy from source
        foreach ($sourceItem->modifierGroups as $sourceGroup) {
            $linkedGroup = $linkedItem->modifierGroups()->create([
                'name' => $sourceGroup->name,
                'required' => $sourceGroup->required,
                'min_selection' => $sourceGroup->min_selection,
                'max_selection' => $sourceGroup->max_selection,
                'sort_index' => $sourceGroup->sort_index,
            ]);

            foreach ($sourceGroup->modifiers as $sourceModifier) {
                $linkedGroup->modifiers()->create([
                    'name' => $sourceModifier->name,
                    'price_adjustment' => $sourceModifier->price_adjustment,
                    'available' => $sourceModifier->available,
                    'sort_index' => $sourceModifier->sort_index,
                ]);
            }
        }
    }
}
