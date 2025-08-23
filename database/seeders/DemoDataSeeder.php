<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Restaurant;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Item;
use App\Models\Table;
use App\Models\ModifierGroup;
use App\Models\Modifier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo tenant
        $tenant = Tenant::firstOrCreate([
            'name' => 'Demo Restaurant Group'
        ], [
            'plan' => 'premium',
            'status' => 'active',
            'settings' => [
                'contact_email' => 'admin@demo-restaurants.com',
                'phone' => '+1 (555) 123-4567',
                'address' => '123 Demo Street, Demo City, DC 12345',
            ],
        ]);

        // Create demo users
        $adminUser = User::firstOrCreate([
            'email' => 'admin@qrmenu.app'
        ], [
            'name' => 'QR Menu Admin',
            'password' => Hash::make('admin123'),
            'tenant_id' => null, // Platform admin
            'email_verified_at' => now(),
        ]);
        $adminUser->assignRole('admin');

        $ownerUser = User::firstOrCreate([
            'email' => 'owner@demo.com'
        ], [
            'name' => 'Demo Restaurant Owner',
            'password' => Hash::make('owner123'),
            'tenant_id' => $tenant->id,
            'email_verified_at' => now(),
        ]);
        $ownerUser->assignRole('owner');

        $managerUser = User::firstOrCreate([
            'email' => 'manager@demo.com'
        ], [
            'name' => 'Demo Manager',
            'password' => Hash::make('manager123'),
            'tenant_id' => $tenant->id,
            'email_verified_at' => now(),
        ]);
        $managerUser->assignRole('manager');

        // Create demo restaurants
        $pizzaPlace = Restaurant::firstOrCreate([
            'subdomain' => 'demo-pizza'
        ], [
            'tenant_id' => $tenant->id,
            'name' => 'Demo Pizza Palace',
            'slug' => 'demo-pizza-palace',
            'address' => '456 Pizza Lane, Foodie District, FD 67890',
            'open_hours' => [
                'monday' => ['11:00', '22:00'],
                'tuesday' => ['11:00', '22:00'],
                'wednesday' => ['11:00', '22:00'],
                'thursday' => ['11:00', '22:00'],
                'friday' => ['11:00', '23:00'],
                'saturday' => ['11:00', '23:00'],
                'sunday' => ['12:00', '21:00'],
            ],
            'active' => true,
        ]);

        $cafeDemo = Restaurant::firstOrCreate([
            'subdomain' => 'demo-cafe'
        ], [
            'tenant_id' => $tenant->id,
            'name' => 'Demo Coffee & Bistro',
            'slug' => 'demo-coffee-bistro',
            'address' => '789 Coffee Ave, Brew Town, BT 54321',
            'open_hours' => [
                'monday' => ['07:00', '19:00'],
                'tuesday' => ['07:00', '19:00'],
                'wednesday' => ['07:00', '19:00'],
                'thursday' => ['07:00', '19:00'],
                'friday' => ['07:00', '20:00'],
                'saturday' => ['08:00', '20:00'],
                'sunday' => ['08:00', '18:00'],
            ],
            'active' => true,
        ]);

        // Create tables
        foreach (['A1', 'A2', 'A3', 'B1', 'B2', 'B3', 'C1', 'C2'] as $i => $tableCode) {
            Table::firstOrCreate([
                'restaurant_id' => $pizzaPlace->id,
                'code' => $tableCode
            ], [
                'seats' => rand(2, 6),
                'area' => 'main',
                'active' => true,
            ]);

            if ($i < 6) { // Create fewer tables for cafe
                Table::firstOrCreate([
                    'restaurant_id' => $cafeDemo->id,
                    'code' => $tableCode
                ], [
                    'seats' => rand(2, 4),
                    'area' => 'main',
                    'active' => true,
                ]);
            }
        }

        // Create Pizza Menu
        $pizzaMenu = Menu::firstOrCreate([
            'restaurant_id' => $pizzaPlace->id,
            'name' => 'Main Menu'
        ], [
            'tenant_id' => $tenant->id,
            'menu_scope' => 'RestaurantLocal',
            'description' => 'Our full menu of pizzas, appetizers, and desserts',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Pizza Categories and Items
        $this->createPizzaMenuItems($pizzaMenu);

        // Create Cafe Menu  
        $cafeMenu = Menu::firstOrCreate([
            'restaurant_id' => $cafeDemo->id,
            'name' => 'All Day Menu'
        ], [
            'tenant_id' => $tenant->id,
            'menu_scope' => 'RestaurantLocal',
            'description' => 'Coffee, pastries, and light meals served all day',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Cafe Categories and Items
        $this->createCafeMenuItems($cafeMenu);

        $this->command->info('Demo data created successfully!');
        $this->command->info('');
        $this->command->info('🔑 Login Credentials:');
        $this->command->info('Admin: admin@qrmenu.app / admin123');
        $this->command->info('Owner: owner@demo.com / owner123');  
        $this->command->info('Manager: manager@demo.com / manager123');
        $this->command->info('');
        $this->command->info('🍕 Example Restaurant URLs:');
        $this->command->info('Pizza Palace: http://demo-pizza.qrmenu.app (or http://127.0.0.1:8000/r/demo-pizza-palace)');
        $this->command->info('Coffee Bistro: http://demo-cafe.qrmenu.app (or http://127.0.0.1:8000/r/demo-coffee-bistro)');
    }

    private function createPizzaMenuItems(Menu $menu): void
    {
        // Appetizers
        $appetizers = Category::firstOrCreate([
            'menu_id' => $menu->id,
            'name' => 'Appetizers'
        ], [
            'icon' => '🥗',
            'sort_index' => 1,
            'visible' => true,
        ]);

        $appetizerItems = [
            ['name' => 'Garlic Bread', 'price' => 6.99, 'description' => 'Fresh baked bread with garlic butter and herbs'],
            ['name' => 'Mozzarella Sticks', 'price' => 8.99, 'description' => 'Golden fried mozzarella with marinara sauce'],
            ['name' => 'Buffalo Wings', 'price' => 12.99, 'description' => '8 pieces with your choice of sauce'],
            ['name' => 'Caesar Salad', 'price' => 9.99, 'description' => 'Crisp romaine, parmesan, croutons, caesar dressing'],
        ];

        foreach ($appetizerItems as $i => $itemData) {
            Item::firstOrCreate([
                'menu_id' => $menu->id,
                'category_id' => $appetizers->id,
                'name' => $itemData['name']
            ], array_merge($itemData, [
                'currency' => 'USD',
                'visible' => true,
                'available' => true,
                'sort_index' => $i + 1,
            ]));
        }

        // Pizzas
        $pizzas = Category::firstOrCreate([
            'menu_id' => $menu->id,
            'name' => 'Pizzas'
        ], [
            'icon' => '🍕',
            'sort_index' => 2,
            'visible' => true,
        ]);

        $pizzaItems = [
            ['name' => 'Margherita', 'price' => 14.99, 'description' => 'Fresh mozzarella, basil, tomato sauce'],
            ['name' => 'Pepperoni', 'price' => 16.99, 'description' => 'Classic pepperoni with mozzarella cheese'],
            ['name' => 'Supreme', 'price' => 19.99, 'description' => 'Pepperoni, sausage, peppers, onions, mushrooms'],
            ['name' => 'Hawaiian', 'price' => 17.99, 'description' => 'Ham, pineapple, mozzarella cheese'],
            ['name' => 'Meat Lovers', 'price' => 21.99, 'description' => 'Pepperoni, sausage, bacon, ham, ground beef'],
        ];

        foreach ($pizzaItems as $i => $itemData) {
            $pizza = Item::firstOrCreate([
                'menu_id' => $menu->id,
                'category_id' => $pizzas->id,
                'name' => $itemData['name']
            ], array_merge($itemData, [
                'currency' => 'USD',
                'visible' => true,
                'available' => true,
                'sort_index' => $i + 1,
            ]));

            // Add pizza size modifiers
            $sizeGroup = ModifierGroup::firstOrCreate([
                'item_id' => $pizza->id,
                'name' => 'Size'
            ], [
                'required' => true,
                'min_selection' => 1,
                'max_selection' => 1,
                'sort_index' => 1,
            ]);

            $sizes = [
                ['name' => 'Small (10")', 'price_adjustment' => -2.00],
                ['name' => 'Medium (12")', 'price_adjustment' => 0.00],
                ['name' => 'Large (14")', 'price_adjustment' => 3.00],
                ['name' => 'Extra Large (16")', 'price_adjustment' => 5.00],
            ];

            foreach ($sizes as $j => $sizeData) {
                Modifier::firstOrCreate([
                    'modifier_group_id' => $sizeGroup->id,
                    'name' => $sizeData['name']
                ], array_merge($sizeData, [
                    'available' => true,
                    'sort_index' => $j + 1,
                ]));
            }
        }

        // Drinks
        $drinks = Category::firstOrCreate([
            'menu_id' => $menu->id,
            'name' => 'Beverages'
        ], [
            'icon' => '🥤',
            'sort_index' => 3,
            'visible' => true,
        ]);

        $drinkItems = [
            ['name' => 'Coca-Cola', 'price' => 2.99, 'description' => 'Classic Coke'],
            ['name' => 'Sprite', 'price' => 2.99, 'description' => 'Lemon-lime soda'],
            ['name' => 'Orange Juice', 'price' => 3.49, 'description' => 'Fresh squeezed'],
            ['name' => 'Italian Soda', 'price' => 4.99, 'description' => 'Sparkling water with syrup'],
        ];

        foreach ($drinkItems as $i => $itemData) {
            Item::firstOrCreate([
                'menu_id' => $menu->id,
                'category_id' => $drinks->id,
                'name' => $itemData['name']
            ], array_merge($itemData, [
                'currency' => 'USD',
                'visible' => true,
                'available' => true,
                'sort_index' => $i + 1,
            ]));
        }
    }

    private function createCafeMenuItems(Menu $menu): void
    {
        // Coffee
        $coffee = Category::firstOrCreate([
            'menu_id' => $menu->id,
            'name' => 'Coffee'
        ], [
            'icon' => '☕',
            'sort_index' => 1,
            'visible' => true,
        ]);

        $coffeeItems = [
            ['name' => 'Espresso', 'price' => 3.50, 'description' => 'Double shot of our signature blend'],
            ['name' => 'Cappuccino', 'price' => 4.75, 'description' => 'Espresso with steamed milk and foam'],
            ['name' => 'Latte', 'price' => 5.25, 'description' => 'Espresso with steamed milk'],
            ['name' => 'Mocha', 'price' => 5.75, 'description' => 'Espresso with chocolate and steamed milk'],
            ['name' => 'Americano', 'price' => 4.25, 'description' => 'Espresso with hot water'],
        ];

        foreach ($coffeeItems as $i => $itemData) {
            $coffee_item = Item::firstOrCreate([
                'menu_id' => $menu->id,
                'category_id' => $coffee->id,
                'name' => $itemData['name']
            ], array_merge($itemData, [
                'currency' => 'USD',
                'visible' => true,
                'available' => true,
                'sort_index' => $i + 1,
            ]));

            // Add size modifiers for coffee
            $sizeGroup = ModifierGroup::firstOrCreate([
                'item_id' => $coffee_item->id,
                'name' => 'Size'
            ], [
                'required' => true,
                'min_selection' => 1,
                'max_selection' => 1,
                'sort_index' => 1,
            ]);

            $sizes = [
                ['name' => 'Small', 'price_adjustment' => -0.50],
                ['name' => 'Medium', 'price_adjustment' => 0.00],
                ['name' => 'Large', 'price_adjustment' => 0.75],
            ];

            foreach ($sizes as $j => $sizeData) {
                Modifier::firstOrCreate([
                    'modifier_group_id' => $sizeGroup->id,
                    'name' => $sizeData['name']
                ], array_merge($sizeData, [
                    'available' => true,
                    'sort_index' => $j + 1,
                ]));
            }
        }

        // Pastries
        $pastries = Category::firstOrCreate([
            'menu_id' => $menu->id,
            'name' => 'Pastries'
        ], [
            'icon' => '🥐',
            'sort_index' => 2,
            'visible' => true,
        ]);

        $pastryItems = [
            ['name' => 'Croissant', 'price' => 3.25, 'description' => 'Buttery, flaky French pastry'],
            ['name' => 'Blueberry Muffin', 'price' => 4.50, 'description' => 'Fresh blueberries in a tender muffin'],
            ['name' => 'Danish', 'price' => 4.25, 'description' => 'Fruit-filled pastry'],
            ['name' => 'Scone', 'price' => 3.75, 'description' => 'Traditional English scone with jam'],
        ];

        foreach ($pastryItems as $i => $itemData) {
            Item::firstOrCreate([
                'menu_id' => $menu->id,
                'category_id' => $pastries->id,
                'name' => $itemData['name']
            ], array_merge($itemData, [
                'currency' => 'USD',
                'visible' => true,
                'available' => true,
                'sort_index' => $i + 1,
            ]));
        }

        // Sandwiches
        $sandwiches = Category::firstOrCreate([
            'menu_id' => $menu->id,
            'name' => 'Sandwiches'
        ], [
            'icon' => '🥪',
            'sort_index' => 3,
            'visible' => true,
        ]);

        $sandwichItems = [
            ['name' => 'Club Sandwich', 'price' => 12.99, 'description' => 'Turkey, bacon, lettuce, tomato on sourdough'],
            ['name' => 'Grilled Cheese', 'price' => 8.99, 'description' => 'Melted cheese on buttered bread'],
            ['name' => 'BLT', 'price' => 10.50, 'description' => 'Bacon, lettuce, tomato with mayo'],
            ['name' => 'Veggie Wrap', 'price' => 11.25, 'description' => 'Fresh vegetables in a spinach tortilla'],
        ];

        foreach ($sandwichItems as $i => $itemData) {
            Item::firstOrCreate([
                'menu_id' => $menu->id,
                'category_id' => $sandwiches->id,
                'name' => $itemData['name']
            ], array_merge($itemData, [
                'currency' => 'USD',
                'visible' => true,
                'available' => true,
                'sort_index' => $i + 1,
            ]));
        }
    }
}
