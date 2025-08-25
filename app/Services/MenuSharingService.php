<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Restaurant;
use App\Models\Menu;
use App\Models\Category;
use App\Models\ItemSharingHistory;
use App\Models\ItemSharingRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MenuSharingService
{
    public function duplicateItem(
        Item $sourceItem, 
        Menu $targetMenu, 
        Category $targetCategory,
        User $user,
        array $overrides = []
    ): Item {
        return DB::transaction(function () use ($sourceItem, $targetMenu, $targetCategory, $user, $overrides) {
            // Create the duplicated item
            $duplicatedItem = new Item([
                'category_id' => $targetCategory->id,
                'sharing_type' => 'duplicated',
                'source_item_id' => $sourceItem->id,
                'source_restaurant_id' => $sourceItem->category->menu->restaurant_id,
                'allow_price_override' => true,
                'allow_description_override' => true,
                'allow_image_override' => true,
            ]);

            // Copy all fields from source item
            $copyableFields = [
                'name', 'description', 'price', 'currency', 'sku', 'image',
                'allergens', 'tags', 'calories', 'visible', 'available',
                'tax_code', 'prep_station', 'sort_index'
            ];

            foreach ($copyableFields as $field) {
                $duplicatedItem->{$field} = $sourceItem->{$field};
            }

            // Apply any overrides
            foreach ($overrides as $field => $value) {
                if ($duplicatedItem->canOverrideField($field)) {
                    $duplicatedItem->{$field} = $value;
                }
            }

            $duplicatedItem->save();

            // Copy modifier groups
            $this->copyModifierGroups($sourceItem, $duplicatedItem);

            // Record the sharing action
            ItemSharingHistory::create([
                'source_item_id' => $sourceItem->id,
                'target_item_id' => $duplicatedItem->id,
                'source_restaurant_id' => $sourceItem->category->menu->restaurant_id,
                'target_restaurant_id' => $targetMenu->restaurant_id,
                'action_type' => 'duplicated',
                'user_id' => $user->id,
            ]);

            return $duplicatedItem;
        });
    }

    public function linkItem(
        Item $sourceItem, 
        Menu $targetMenu, 
        Category $targetCategory,
        User $user,
        array $syncSettings = []
    ): Item {
        return DB::transaction(function () use ($sourceItem, $targetMenu, $targetCategory, $user, $syncSettings) {
            // Create the linked item
            $linkedItem = new Item([
                'category_id' => $targetCategory->id,
                'sharing_type' => 'linked',
                'source_item_id' => $sourceItem->id,
                'source_restaurant_id' => $sourceItem->category->menu->restaurant_id,
                'allow_price_override' => $syncSettings['allow_price_override'] ?? false,
                'allow_description_override' => $syncSettings['allow_description_override'] ?? false,
                'allow_image_override' => $syncSettings['allow_image_override'] ?? false,
                'sync_settings' => $syncSettings,
                'last_synced_at' => now(),
            ]);

            // Copy all synced fields from source item
            $syncableFields = $linkedItem->getSyncableFields();
            
            foreach ($syncableFields as $field) {
                if (isset($sourceItem->{$field})) {
                    $linkedItem->{$field} = $sourceItem->{$field};
                }
            }

            $linkedItem->save();

            // Copy modifier groups (these are always synced for linked items)
            $this->copyModifierGroups($sourceItem, $linkedItem);

            // Record the sharing action
            ItemSharingHistory::create([
                'source_item_id' => $sourceItem->id,
                'target_item_id' => $linkedItem->id,
                'source_restaurant_id' => $sourceItem->category->menu->restaurant_id,
                'target_restaurant_id' => $targetMenu->restaurant_id,
                'action_type' => 'linked',
                'user_id' => $user->id,
            ]);

            return $linkedItem;
        });
    }

    public function syncLinkedItem(Item $linkedItem, User $user = null): bool
    {
        if (!$linkedItem->isLinked() || !$linkedItem->sourceItem) {
            return false;
        }

        if (!$linkedItem->needsSync()) {
            return true; // Already up to date
        }

        return DB::transaction(function () use ($linkedItem, $user) {
            $sourceItem = $linkedItem->sourceItem;
            $changedFields = [];

            // Sync all syncable fields
            foreach ($linkedItem->getSyncableFields() as $field) {
                if (isset($sourceItem->{$field}) && $linkedItem->{$field} !== $sourceItem->{$field}) {
                    $changedFields[$field] = [
                        'old' => $linkedItem->{$field},
                        'new' => $sourceItem->{$field}
                    ];
                    $linkedItem->{$field} = $sourceItem->{$field};
                }
            }

            if (!empty($changedFields)) {
                $linkedItem->last_synced_at = now();
                $linkedItem->save();

                // Re-sync modifier groups
                $linkedItem->modifierGroups()->delete();
                $this->copyModifierGroups($sourceItem, $linkedItem);

                // Record the sync action
                ItemSharingHistory::create([
                    'source_item_id' => $sourceItem->id,
                    'target_item_id' => $linkedItem->id,
                    'source_restaurant_id' => $sourceItem->category->menu->restaurant_id,
                    'target_restaurant_id' => $linkedItem->category->menu->restaurant_id,
                    'action_type' => 'synced',
                    'changed_fields' => $changedFields,
                    'user_id' => $user?->id,
                ]);
            }

            return true;
        });
    }

    public function createSharingRequest(
        Item $sourceItem,
        Restaurant $targetRestaurant,
        User $requester,
        string $sharingType,
        string $message = null
    ): ItemSharingRequest {
        return ItemSharingRequest::create([
            'source_item_id' => $sourceItem->id,
            'source_restaurant_id' => $sourceItem->category->menu->restaurant_id,
            'target_restaurant_id' => $targetRestaurant->id,
            'requester_id' => $requester->id,
            'sharing_type' => $sharingType,
            'message' => $message,
        ]);
    }

    public function unlinkItem(Item $linkedItem, User $user): bool
    {
        if (!$linkedItem->isLinked()) {
            return false;
        }

        return DB::transaction(function () use ($linkedItem, $user) {
            // Record the unlinking action
            ItemSharingHistory::create([
                'source_item_id' => $linkedItem->source_item_id,
                'target_item_id' => $linkedItem->id,
                'source_restaurant_id' => $linkedItem->source_restaurant_id,
                'target_restaurant_id' => $linkedItem->category->menu->restaurant_id,
                'action_type' => 'unlinked',
                'user_id' => $user->id,
            ]);

            // Convert to original item
            $linkedItem->update([
                'sharing_type' => 'original',
                'source_item_id' => null,
                'source_restaurant_id' => null,
                'allow_price_override' => true,
                'allow_description_override' => true,
                'allow_image_override' => true,
                'sync_settings' => null,
                'last_synced_at' => null,
            ]);

            return true;
        });
    }

    public function syncAllLinkedItems(): int
    {
        $syncedCount = 0;
        
        $linkedItems = Item::linked()
            ->with('sourceItem')
            ->whereHas('sourceItem')
            ->get();

        foreach ($linkedItems as $linkedItem) {
            if ($linkedItem->needsSync()) {
                $this->syncLinkedItem($linkedItem);
                $syncedCount++;
            }
        }

        return $syncedCount;
    }

    public function getAvailableItemsForSharing(Restaurant $targetRestaurant, $searchTerm = null)
    {
        $query = Item::original()
            ->with(['category.menu.restaurant', 'modifierGroups'])
            ->whereHas('category.menu.restaurant', function ($query) use ($targetRestaurant) {
                $query->where('id', '!=', $targetRestaurant->id)
                      ->where(function ($query) use ($targetRestaurant) {
                          // Same tenant restaurants
                          $query->where('tenant_id', $targetRestaurant->tenant_id)
                                ->whereJsonContains('sharing_settings->sharing_permissions', 'same_tenant')
                                // Or restaurants that allow public sharing
                                ->orWhereJsonContains('sharing_settings->sharing_permissions', 'public');
                      })
                      ->whereJsonExtract('sharing_settings', '$.allow_outgoing_sharing')->where('allow_outgoing_sharing', true);
            });

        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%")
                      ->orWhereJsonContains('tags', $searchTerm);
            });
        }

        return $query->paginate(20);
    }

    private function copyModifierGroups(Item $sourceItem, Item $targetItem): void
    {
        foreach ($sourceItem->modifierGroups as $sourceGroup) {
            $targetGroup = $targetItem->modifierGroups()->create([
                'name' => $sourceGroup->name,
                'min_selections' => $sourceGroup->min_selections,
                'max_selections' => $sourceGroup->max_selections,
                'required' => $sourceGroup->required,
                'sort_index' => $sourceGroup->sort_index,
            ]);

            // Copy modifiers
            foreach ($sourceGroup->modifiers as $sourceModifier) {
                $targetGroup->modifiers()->create([
                    'name' => $sourceModifier->name,
                    'price' => $sourceModifier->price,
                    'available' => $sourceModifier->available,
                    'sort_index' => $sourceModifier->sort_index,
                ]);
            }
        }
    }
}
