<?php
// routes/web.php
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Customer routes (QR code access)
Route::domain('{subdomain}.qrmenu.app')->group(function () {
    Route::get('/', [CustomerController::class, 'landing'])->name('customer.landing');
    Route::get('/menu/{menu?}', [CustomerController::class, 'menu'])->name('customer.menu');
    Route::get('/menu/{menu}/category/{category}', [CustomerController::class, 'category'])->name('customer.menu.category');
    Route::get('/table/{table}', [CustomerController::class, 'table'])->name('customer.table');
});

// QR code direct access
Route::get('/r/{restaurant:slug}', [CustomerController::class, 'restaurant'])->name('customer.restaurant');
Route::get('/r/{restaurant:slug}/t/{table:code}', [CustomerController::class, 'table'])->name('customer.restaurant.table');
Route::get('/r/{restaurant:slug}/m/{menu}', [CustomerController::class, 'menu'])->name('customer.restaurant.menu');

// Customer API routes (HTMX)
Route::prefix('customer')->name('customer.')->group(function () {
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/line/{index}', [CartController::class, 'updateLine'])->name('cart.update');
    Route::delete('/cart/line/{index}', [CartController::class, 'removeLine'])->name('cart.remove');
    Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
    Route::get('/items/{item}/modal', [ItemController::class, 'modal'])->name('item.modal');
    Route::post('/ratings', [RatingController::class, 'store'])->name('rating.store');
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard routes
    Route::get('/dashboard', function () {
        return redirect()->route('owner.dashboard');
    })->name('dashboard');

    // Owner/Manager routes
    Route::prefix('owner')->name('owner.')->middleware(['role:owner|manager|admin'])->group(function () {
        Route::get('/', [OwnerController::class, 'dashboard'])->name('dashboard');
        
        // Restaurant management
        Route::resource('restaurants', RestaurantController::class);
        Route::post('/restaurants/{restaurant}/tables', [RestaurantController::class, 'createTable'])->name('restaurants.tables.store');
        Route::delete('/restaurants/{restaurant}/tables/{table}', [RestaurantController::class, 'destroyTable'])->name('restaurants.tables.destroy');
        
        // Menu management
        Route::resource('menus', MenuController::class);
        Route::post('/menus/{menu}/duplicate', [MenuController::class, 'duplicate'])->name('menus.duplicate');
        Route::post('/menus/{menu}/linked-copy', [MenuController::class, 'createLinkedCopy'])->name('menus.linked-copy');
        Route::post('/menus/{menu}/publish', [MenuController::class, 'publish'])->name('menus.publish');
        Route::patch('/menus/{menu}/schedule', [MenuController::class, 'schedule'])->name('menus.schedule');
        
        // Category management (HTMX)
        Route::post('/menus/{menu}/categories', [MenuController::class, 'createCategory'])->name('menus.categories.store');
        Route::patch('/categories/{category}', [MenuController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [MenuController::class, 'destroyCategory'])->name('categories.destroy');
        Route::patch('/categories/{category}/reorder', [MenuController::class, 'reorderCategory'])->name('categories.reorder');
        
        // Item management (HTMX)
        Route::post('/categories/{category}/items', [ItemController::class, 'store'])->name('items.store');
        Route::patch('/items/{item}', [ItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
        Route::patch('/items/{item}/reorder', [ItemController::class, 'reorder'])->name('items.reorder');
        Route::post('/items/{item}/86', [ItemController::class, 'toggle86'])->name('items.86');
        
        // Modifier management
        Route::post('/items/{item}/modifier-groups', [ItemController::class, 'createModifierGroup'])->name('items.modifier-groups.store');
        Route::patch('/modifier-groups/{modifierGroup}', [ItemController::class, 'updateModifierGroup'])->name('modifier-groups.update');
        Route::delete('/modifier-groups/{modifierGroup}', [ItemController::class, 'destroyModifierGroup'])->name('modifier-groups.destroy');
        
        Route::post('/modifier-groups/{modifierGroup}/modifiers', [ItemController::class, 'createModifier'])->name('modifiers.store');
        Route::patch('/modifiers/{modifier}', [ItemController::class, 'updateModifier'])->name('modifiers.update');
        Route::delete('/modifiers/{modifier}', [ItemController::class, 'destroyModifier'])->name('modifiers.destroy');
        
        // Order management
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
        Route::post('/orders/{order}/refund', [OrderController::class, 'refund'])->name('orders.refund');
        
        // QR Code management
        Route::get('/restaurants/{restaurant}/qr-codes', [QrCodeController::class, 'index'])->name('restaurants.qr-codes.index');
        Route::post('/restaurants/{restaurant}/qr-codes/generate', [QrCodeController::class, 'generate'])->name('restaurants.qr-codes.generate');
        Route::get('/restaurants/{restaurant}/qr-codes/{qrCode}/download', [QrCodeController::class, 'download'])->name('restaurants.qr-codes.download');
        
        // Analytics & Reports
        Route::get('/analytics', [OwnerController::class, 'analytics'])->name('analytics');
        Route::get('/reports', [OwnerController::class, 'reports'])->name('reports');
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/tenants', [AdminController::class, 'tenants'])->name('tenants.index');
        Route::get('/tenants/{tenant}', [AdminController::class, 'showTenant'])->name('tenants.show');
        Route::post('/tenants/{tenant}/impersonate/{user}', [AdminController::class, 'impersonate'])->name('tenants.impersonate');
        Route::get('/audits', [AdminController::class, 'audits'])->name('audits.index');
        Route::get('/feature-flags', [AdminController::class, 'featureFlags'])->name('feature-flags.index');
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Webhook routes
Route::post('/webhooks/stripe', [WebhookController::class, 'stripe'])->name('webhooks.stripe');

// API routes for mobile/integrations
Route::prefix('api/v1')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/restaurants', [\App\Http\Controllers\Api\RestaurantController::class, 'index']);
    Route::get('/restaurants/{restaurant}/menus', [\App\Http\Controllers\Api\MenuController::class, 'index']);
    Route::get('/menus/{menu}/items', [\App\Http\Controllers\Api\ItemController::class, 'index']);
    Route::post('/orders', [\App\Http\Controllers\Api\OrderController::class, 'store']);
    Route::get('/orders/{order}', [\App\Http\Controllers\Api\OrderController::class, 'show']);
});

require __DIR__.'/auth.php';
