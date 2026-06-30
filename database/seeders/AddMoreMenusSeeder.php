<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Ingredient;

class AddMoreMenusSeeder extends Seeder
{
    public function run()
    {
        // 1. Tambah Ingredients Baru
        $ingredientsData = [
            ['name' => 'Beef Sausage', 'stock_qty' => 3000, 'unit' => 'gram', 'minimum_stock_alert' => 500],
            ['name' => 'Smoked Beef', 'stock_qty' => 3000, 'unit' => 'gram', 'minimum_stock_alert' => 500],
            ['name' => 'Pineapple', 'stock_qty' => 2000, 'unit' => 'gram', 'minimum_stock_alert' => 300],
            ['name' => 'Chicken Breast', 'stock_qty' => 5000, 'unit' => 'gram', 'minimum_stock_alert' => 1000],
            ['name' => 'BBQ Sauce', 'stock_qty' => 2000, 'unit' => 'gram', 'minimum_stock_alert' => 500],
            ['name' => 'Gorgonzola Cheese', 'stock_qty' => 1000, 'unit' => 'gram', 'minimum_stock_alert' => 200],
            ['name' => 'Parmesan Cheese', 'stock_qty' => 1000, 'unit' => 'gram', 'minimum_stock_alert' => 200],
            ['name' => 'Spaghetti Pasta', 'stock_qty' => 5000, 'unit' => 'gram', 'minimum_stock_alert' => 1000],
            ['name' => 'Minced Beef', 'stock_qty' => 4000, 'unit' => 'gram', 'minimum_stock_alert' => 1000],
            ['name' => 'Cola Syrup', 'stock_qty' => 3000, 'unit' => 'ml', 'minimum_stock_alert' => 500],
            ['name' => 'Lemon Tea Powder', 'stock_qty' => 2000, 'unit' => 'gram', 'minimum_stock_alert' => 500],
            ['name' => 'Mineral Water Bottle', 'stock_qty' => 200, 'unit' => 'pcs', 'minimum_stock_alert' => 50],
            ['name' => 'Garlic Butter', 'stock_qty' => 1000, 'unit' => 'gram', 'minimum_stock_alert' => 200],
            ['name' => 'Baguette', 'stock_qty' => 100, 'unit' => 'pcs', 'minimum_stock_alert' => 20],
            ['name' => 'Chicken Wings Cut', 'stock_qty' => 500, 'unit' => 'pcs', 'minimum_stock_alert' => 100],
            ['name' => 'French Fries Cut', 'stock_qty' => 10000, 'unit' => 'gram', 'minimum_stock_alert' => 2000],
            // Already exist: Pizza Dough, Tomato Sauce, Mozzarella Cheese, Pepperoni
        ];

        $ingredientIds = [];
        foreach ($ingredientsData as $data) {
            $ing = Ingredient::firstOrCreate(['name' => $data['name']], $data);
            $ingredientIds[$data['name']] = $ing->id;
        }

        // Get existing ingredients
        $dough = Ingredient::where('name', 'Pizza Dough')->first()->id;
        $tomato = Ingredient::where('name', 'Tomato Sauce')->first()->id;
        $mozza = Ingredient::where('name', 'Mozzarella Cheese')->first()->id;
        $pepperoni = Ingredient::where('name', 'Pepperoni')->first()->id;

        // Categories
        $catPizza = Category::where('slug', 'pizza')->first()->id;
        $catBeverage = Category::where('slug', 'beverage')->first()->id;
        $catSnack = Category::where('slug', 'snack')->first()->id;
        $catPasta = Category::where('slug', 'pasta')->first()->id;

        // 2. Tambah Menus
        $menus = [
            // MAKANAN (5)
            [
                'name' => 'Meat Lovers Pizza',
                'description' => 'Pizza super daging dengan sosis sapi, smoked beef, dan pepperoni berlimpah.',
                'base_price' => 110000,
                'category_id' => $catPizza,
                'image_url' => asset('images/menu/meat_lovers.png'),
                'is_available' => true,
                'daily_stock' => 50,
                'ingredients' => [
                    $dough => 1,
                    $tomato => 50,
                    $mozza => 100,
                    $pepperoni => 30,
                    $ingredientIds['Beef Sausage'] => 50,
                    $ingredientIds['Smoked Beef'] => 50,
                ]
            ],
            [
                'name' => 'Hawaiian Pizza',
                'description' => 'Perpaduan manis gurih dari smoked beef dan potongan nanas segar.',
                'base_price' => 95000,
                'category_id' => $catPizza,
                'image_url' => asset('images/menu/hawaiian.png'),
                'is_available' => true,
                'daily_stock' => 40,
                'ingredients' => [
                    $dough => 1,
                    $tomato => 50,
                    $mozza => 80,
                    $ingredientIds['Smoked Beef'] => 50,
                    $ingredientIds['Pineapple'] => 60,
                ]
            ],
            [
                'name' => 'BBQ Chicken Pizza',
                'description' => 'Pizza dengan saus BBQ pekat, potongan dada ayam panggang, dan bawang bombay.',
                'base_price' => 105000,
                'category_id' => $catPizza,
                'image_url' => asset('images/menu/bbq_chicken.png'),
                'is_available' => true,
                'daily_stock' => 45,
                'ingredients' => [
                    $dough => 1,
                    $ingredientIds['BBQ Sauce'] => 60,
                    $mozza => 90,
                    $ingredientIds['Chicken Breast'] => 100,
                ]
            ],
            [
                'name' => 'Quattro Formaggi',
                'description' => 'Keju mania! Menggabungkan Mozzarella, Gorgonzola, Parmesan, dan Cheddar leleh.',
                'base_price' => 120000,
                'category_id' => $catPizza,
                'image_url' => asset('images/menu/quattro_formaggi.png'),
                'is_available' => true,
                'daily_stock' => 30,
                'ingredients' => [
                    $dough => 1,
                    $tomato => 40,
                    $mozza => 60,
                    $ingredientIds['Gorgonzola Cheese'] => 30,
                    $ingredientIds['Parmesan Cheese'] => 30,
                ]
            ],
            [
                'name' => 'Spaghetti Bolognese',
                'description' => 'Pasta klasik Italia dengan saus daging sapi cincang tomat yang kaya rasa.',
                'base_price' => 65000,
                'category_id' => $catPasta,
                'image_url' => asset('images/menu/spaghetti.png'),
                'is_available' => true,
                'daily_stock' => 40,
                'ingredients' => [
                    $ingredientIds['Spaghetti Pasta'] => 150,
                    $tomato => 80,
                    $ingredientIds['Minced Beef'] => 100,
                    $ingredientIds['Parmesan Cheese'] => 15,
                ]
            ],
            // MINUMAN (3)
            [
                'name' => 'Cola Float',
                'description' => 'Minuman soda cola dingin menyegarkan.',
                'base_price' => 20000,
                'category_id' => $catBeverage,
                'image_url' => asset('images/menu/cola.png'),
                'is_available' => true,
                'daily_stock' => 100,
                'ingredients' => [
                    $ingredientIds['Cola Syrup'] => 30,
                ]
            ],
            [
                'name' => 'Iced Lemon Tea',
                'description' => 'Es teh lemon segar dengan manis yang pas.',
                'base_price' => 18000,
                'category_id' => $catBeverage,
                'image_url' => asset('images/menu/lemon_tea.png'),
                'is_available' => true,
                'daily_stock' => 80,
                'ingredients' => [
                    $ingredientIds['Lemon Tea Powder'] => 25,
                ]
            ],
            [
                'name' => 'Mineral Water',
                'description' => 'Air mineral botol 600ml dingin.',
                'base_price' => 10000,
                'category_id' => $catBeverage,
                'image_url' => asset('images/menu/mineral_water.png'),
                'is_available' => true,
                'daily_stock' => 200,
                'ingredients' => [
                    $ingredientIds['Mineral Water Bottle'] => 1,
                ]
            ],
            // CEMILAN (3)
            [
                'name' => 'Garlic Bread',
                'description' => 'Roti baguette panggang renyah dengan olesan mentega bawang putih harum.',
                'base_price' => 25000,
                'category_id' => $catSnack,
                'image_url' => asset('images/menu/garlic_bread.png'),
                'is_available' => true,
                'daily_stock' => 50,
                'ingredients' => [
                    $ingredientIds['Baguette'] => 1,
                    $ingredientIds['Garlic Butter'] => 20,
                ]
            ],
            [
                'name' => 'Chicken Wings',
                'description' => 'Sayap ayam panggang bumbu BBQ (Isi 6 pcs).',
                'base_price' => 45000,
                'category_id' => $catSnack,
                'image_url' => asset('images/menu/chicken_wings.png'),
                'is_available' => true,
                'daily_stock' => 40,
                'ingredients' => [
                    $ingredientIds['Chicken Wings Cut'] => 6,
                    $ingredientIds['BBQ Sauce'] => 30,
                ]
            ],
            [
                'name' => 'French Fries',
                'description' => 'Kentang goreng renyah ukuran besar.',
                'base_price' => 28000,
                'category_id' => $catSnack,
                'image_url' => asset('images/menu/french_fries.png'),
                'is_available' => true,
                'daily_stock' => 60,
                'ingredients' => [
                    $ingredientIds['French Fries Cut'] => 150,
                ]
            ],
        ];

        foreach ($menus as $m) {
            $ingredients = $m['ingredients'];
            unset($m['ingredients']);
            
            $menu = Menu::firstOrCreate(['name' => $m['name']], $m);
            
            // Attach ingredients
            $syncData = [];
            foreach ($ingredients as $ingId => $qty) {
                $syncData[$ingId] = ['qty_needed' => $qty];
            }
            $menu->ingredients()->sync($syncData);
        }
    }
}
