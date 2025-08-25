<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Restaurant;
use App\Models\Menu;
use App\Models\Category;
use App\Models\ItemSharingRequest;
use App\Services\MenuSharingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class MenuSharingController extends Controller
{
    protected MenuSharingService $sharingService;

    public function __construct(MenuSharingService $sharingService)
    {
        $this->sharingService = $sharingService;
    }

    /**
     * Browse items available for sharing
     */
    public function browse(Restaurant $restaurant, Request $request)
    {
        Gate::authorize('update', $restaurant);

        $search = $request->get('search');
        $availableItems = $this->sharingService->getAvailableItemsForSharing($restaurant, $search);

        return view('admin.restaurants.sharing.browse', [
            'restaurant' => $restaurant,
            'items' => $availableItems,
            'search' => $search
        ]);
    }

    /**
     * Show sharing form for a specific item
     */
    public function show(Restaurant $restaurant, Item $item)
    {
        Gate::authorize('update', $restaurant);

        // Get target menus and categories
        $targetMenus = $restaurant->menus()->with('categories')->get();

        return view('admin.restaurants.sharing.item', [
            'restaurant' => $restaurant,
            'item' => $item->load(['category.menu.restaurant', 'modifierGroups']),
            'targetMenus' => $targetMenus
        ]);
    }

    /**
     * Duplicate an item
     */
    public function duplicate(Restaurant $restaurant, Request $request)
    {
        Gate::authorize('update', $restaurant);

        $validated = $request->validate([
            'source_item_id' => 'required|exists:items,id',
            'target_menu_id' => 'required|exists:menus,id',
            'target_category_id' => 'required|exists:categories,id',
            'overrides' => 'array',
            'overrides.name' => 'string|max:255',
            'overrides.description' => 'string',
            'overrides.price' => 'numeric|min:0',
            'overrides.image' => 'string',
        ]);

        $sourceItem = Item::findOrFail($validated['source_item_id']);
        $targetMenu = Menu::findOrFail($validated['target_menu_id']);
        $targetCategory = Category::findOrFail($validated['target_category_id']);

        // Verify the target menu belongs to the restaurant
        if ($targetMenu->restaurant_id !== $restaurant->id) {
            abort(403, 'Target menu does not belong to this restaurant');
        }

        // Verify the category belongs to the menu
        if ($targetCategory->menu_id !== $targetMenu->id) {
            abort(403, 'Target category does not belong to the specified menu');
        }

        // Check sharing permissions
        $sourceRestaurant = $sourceItem->category->menu->restaurant;
        if (!$sourceRestaurant->canShareWith($restaurant)) {
            abort(403, 'Sharing not allowed between these restaurants');
        }

        $duplicatedItem = $this->sharingService->duplicateItem(
            $sourceItem,
            $targetMenu,
            $targetCategory,
            Auth::user(),
            $validated['overrides'] ?? []
        );

        return response()->json([
            'success' => true,
            'message' => 'Item duplicated successfully',
            'item' => $duplicatedItem->load(['category', 'modifierGroups'])
        ]);
    }

    /**
     * Link an item
     */
    public function link(Restaurant $restaurant, Request $request)
    {
        Gate::authorize('update', $restaurant);

        $validated = $request->validate([
            'source_item_id' => 'required|exists:items,id',
            'target_menu_id' => 'required|exists:menus,id',
            'target_category_id' => 'required|exists:categories,id',
            'sync_settings' => 'array',
            'sync_settings.allow_price_override' => 'boolean',
            'sync_settings.allow_description_override' => 'boolean',
            'sync_settings.allow_image_override' => 'boolean',
            'sync_settings.additional_fields' => 'array',
        ]);

        $sourceItem = Item::findOrFail($validated['source_item_id']);
        $targetMenu = Menu::findOrFail($validated['target_menu_id']);
        $targetCategory = Category::findOrFail($validated['target_category_id']);

        // Verify the target menu belongs to the restaurant
        if ($targetMenu->restaurant_id !== $restaurant->id) {
            abort(403, 'Target menu does not belong to this restaurant');
        }

        // Verify the category belongs to the menu
        if ($targetCategory->menu_id !== $targetMenu->id) {
            abort(403, 'Target category does not belong to the specified menu');
        }

        // Check sharing permissions
        $sourceRestaurant = $sourceItem->category->menu->restaurant;
        if (!$sourceRestaurant->canShareWith($restaurant)) {
            abort(403, 'Sharing not allowed between these restaurants');
        }

        // Check if source restaurant allows linking
        if (!$sourceRestaurant->allowsIncomingLinks()) {
            abort(403, 'Source restaurant does not allow item linking');
        }

        $linkedItem = $this->sharingService->linkItem(
            $sourceItem,
            $targetMenu,
            $targetCategory,
            Auth::user(),
            $validated['sync_settings'] ?? []
        );

        return response()->json([
            'success' => true,
            'message' => 'Item linked successfully',
            'item' => $linkedItem->load(['category', 'sourceItem', 'sourceRestaurant'])
        ]);
    }

    /**
     * Create a sharing request
     */
    public function createRequest(Restaurant $restaurant, Request $request)
    {
        Gate::authorize('update', $restaurant);

        $validated = $request->validate([
            'source_item_id' => 'required|exists:items,id',
            'target_restaurant_id' => 'required|exists:restaurants,id',
            'sharing_type' => 'required|in:duplicate,link',
            'message' => 'string|max:500',
        ]);

        $sourceItem = Item::findOrFail($validated['source_item_id']);
        $targetRestaurant = Restaurant::findOrFail($validated['target_restaurant_id']);

        $sharingRequest = $this->sharingService->createSharingRequest(
            $sourceItem,
            $targetRestaurant,
            Auth::user(),
            $validated['sharing_type'],
            $validated['message'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Sharing request created successfully',
            'request' => $sharingRequest->load(['sourceItem', 'targetRestaurant'])
        ]);
    }

    /**
     * Sync a linked item
     */
    public function sync(Restaurant $restaurant, Item $item)
    {
        Gate::authorize('update', $restaurant);

        if (!$item->isLinked()) {
            return response()->json(['error' => 'Item is not linked'], 400);
        }

        $synced = $this->sharingService->syncLinkedItem($item, Auth::user());

        if ($synced) {
            return response()->json([
                'success' => true,
                'message' => 'Item synced successfully',
                'item' => $item->fresh()->load(['sourceItem', 'sourceRestaurant'])
            ]);
        }

        return response()->json(['error' => 'Failed to sync item'], 500);
    }

    /**
     * Unlink an item
     */
    public function unlink(Restaurant $restaurant, Item $item)
    {
        Gate::authorize('update', $restaurant);

        if (!$item->isLinked()) {
            return response()->json(['error' => 'Item is not linked'], 400);
        }

        $unlinked = $this->sharingService->unlinkItem($item, Auth::user());

        if ($unlinked) {
            return response()->json([
                'success' => true,
                'message' => 'Item unlinked successfully',
                'item' => $item->fresh()
            ]);
        }

        return response()->json(['error' => 'Failed to unlink item'], 500);
    }

    /**
     * View sharing requests for a restaurant
     */
    public function requests(Restaurant $restaurant)
    {
        Gate::authorize('update', $restaurant);

        $incomingRequests = $restaurant->incomingSharingRequests()
            ->with(['sourceItem.category.menu.restaurant', 'requester'])
            ->pending()
            ->latest()
            ->paginate(10, ['*'], 'incoming');

        $outgoingRequests = $restaurant->outgoingSharingRequests()
            ->with(['targetRestaurant', 'sourceItem'])
            ->latest()
            ->paginate(10, ['*'], 'outgoing');

        return view('admin.restaurants.sharing.requests', [
            'restaurant' => $restaurant,
            'incomingRequests' => $incomingRequests,
            'outgoingRequests' => $outgoingRequests
        ]);
    }

    /**
     * Approve a sharing request
     */
    public function approveRequest(Restaurant $restaurant, ItemSharingRequest $request)
    {
        Gate::authorize('update', $restaurant);

        if ($request->target_restaurant_id !== $restaurant->id) {
            abort(403, 'This request does not belong to your restaurant');
        }

        if (!$request->isPending()) {
            return response()->json(['error' => 'Request is no longer pending'], 400);
        }

        $approved = $request->approve(Auth::user());

        if ($approved) {
            return response()->json([
                'success' => true,
                'message' => 'Sharing request approved',
                'request' => $request->fresh()
            ]);
        }

        return response()->json(['error' => 'Failed to approve request'], 500);
    }

    /**
     * Reject a sharing request
     */
    public function rejectRequest(Restaurant $restaurant, ItemSharingRequest $sharingRequest, Request $request)
    {
        Gate::authorize('update', $restaurant);

        if ($sharingRequest->target_restaurant_id !== $restaurant->id) {
            abort(403, 'This request does not belong to your restaurant');
        }

        $validated = $request->validate([
            'reason' => 'string|max:500'
        ]);

        $rejected = $sharingRequest->reject(Auth::user(), $validated['reason'] ?? null);

        if ($rejected) {
            return response()->json([
                'success' => true,
                'message' => 'Sharing request rejected',
                'request' => $sharingRequest->fresh()
            ]);
        }

        return response()->json(['error' => 'Failed to reject request'], 500);
    }

    /**
     * View shared items management
     */
    public function manage(Restaurant $restaurant, Request $request)
    {
        Gate::authorize('update', $restaurant);

        $type = $request->get('type', 'all'); // all, duplicated, linked

        $query = Item::whereHas('category.menu', function ($query) use ($restaurant) {
            $query->where('restaurant_id', $restaurant->id);
        })->where('sharing_type', '!=', 'original');

        if ($type === 'duplicated') {
            $query->duplicated();
        } elseif ($type === 'linked') {
            $query->linked();
        }

        $sharedItems = $query->with([
            'category.menu',
            'sourceItem',
            'sourceRestaurant',
            'targetSharingHistory' => function ($query) {
                $query->latest()->limit(3);
            }
        ])->paginate(15);

        return view('admin.restaurants.sharing.manage', [
            'restaurant' => $restaurant,
            'sharedItems' => $sharedItems,
            'currentType' => $type
        ]);
    }
}
