<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catPizza = \App\Models\Category::firstOrCreate(['name' => 'Pizza', 'slug' => 'pizza']);
        $catCalzone = \App\Models\Category::firstOrCreate(['name' => 'Calzone', 'slug' => 'calzone']);
        $catDrinks = \App\Models\Category::firstOrCreate(['name' => 'Drinks', 'slug' => 'drinks']);

        // ---- PIZZAS ----
        $pizzas = [
            [
                'name' => 'Pizza Carne e Parmigiana',
                'description' => 'Passata di podomoro. Mozzarella. Fresh meat. Formaggio parmigiano.',
                'base_price' => 112000,
                'category_id' => $catPizza->id,
                'image_url' => 'menus/carne_parmigiana.png',
                'is_available' => true, 'daily_stock' => 100,
            ],
            [
                'name' => 'Pizza Beef',
                'description' => 'Passata di podomoro. Mozzarella. Fresh meat.',
                'base_price' => 76000,
                'category_id' => $catPizza->id,
                'image_url' => 'menus/carne_parmigiana.png', // reusing meat pizza image
                'is_available' => true, 'daily_stock' => 100,
            ],
            [
                'name' => 'Pizza Con Pesto di Parmigiano',
                'description' => 'Mozarella. Pesto. Formaggio parmigiano.',
                'base_price' => 101000,
                'category_id' => $catPizza->id,
                'image_url' => 'menus/pesto_parmigiano.png',
                'is_available' => true, 'daily_stock' => 100,
            ],
            [
                'name' => 'Pizza Margherita',
                'description' => 'Passata di podomoro. Mozzarella. Parmesan cheese.',
                'base_price' => 75000,
                'category_id' => $catPizza->id,
                'image_url' => 'menus/margherita.png',
                'is_available' => true, 'daily_stock' => 100,
            ],
            [
                'name' => 'Pizza Bratwurst',
                'description' => 'Passata di podomoro. Mozzarella. Bratwurst sausage.',
                'base_price' => 81000,
                'category_id' => $catPizza->id,
                'image_url' => 'menus/pepperoni.png', // reusing pepperoni image for sausage
                'is_available' => true, 'daily_stock' => 100,
            ],
            [
                'name' => 'Pizza Enoki',
                'description' => 'Passata di podomoro. Mozzarella. Enoki mushroom.',
                'base_price' => 69000,
                'category_id' => $catPizza->id,
                'image_url' => 'menus/margherita.png', // placeholder
                'is_available' => true, 'daily_stock' => 100,
            ],
            [
                'name' => 'Pizza Beef Pepperoni',
                'description' => 'Passata di podomoro. Mozzarella. Beef pepperoni.',
                'base_price' => 81000,
                'category_id' => $catPizza->id,
                'image_url' => 'menus/pepperoni.png',
                'is_available' => true, 'daily_stock' => 100,
            ],
            [
                'name' => 'Pizza Marinara',
                'description' => 'Classic Neapolitan pizza. Garlic. Oregano. Basil. No cheese.',
                'base_price' => 54000,
                'category_id' => $catPizza->id,
                'image_url' => 'menus/margherita.png', // placeholder
                'is_available' => true, 'daily_stock' => 100,
            ],
            [
                'name' => 'Manzo Super Tenero',
                'description' => 'Napoletana pizza. Wagyu beef. Double Mozzarella. Formaggio parmigiano.',
                'base_price' => 157000,
                'category_id' => $catPizza->id,
                'image_url' => 'menus/carne_parmigiana.png', // reusing meat pizza
                'is_available' => true, 'daily_stock' => 100,
            ],
            [
                'name' => 'Fiore Bianco',
                'description' => 'Mozzarella. Cream cheese. Champignon. Formaggio parmigiano. Mushroom ketchup.',
                'base_price' => 131000,
                'category_id' => $catPizza->id,
                'image_url' => 'menus/margherita.png', // placeholder
                'is_available' => true, 'daily_stock' => 100,
            ],
            [
                'name' => 'Pizza Bianco',
                'description' => 'White pizza. Mozzarella. Parmesan. Black pepper.',
                'base_price' => 75000,
                'category_id' => $catPizza->id,
                'image_url' => 'menus/margherita.png', // placeholder
                'is_available' => true, 'daily_stock' => 100,
            ],
            [
                'name' => 'Fiore Rosso',
                'description' => 'Passata di podomoro. Mozzarella. Cream cheese. Champignon. Formaggio parmigiano.',
                'base_price' => 131000,
                'category_id' => $catPizza->id,
                'image_url' => 'menus/margherita.png', // placeholder
                'is_available' => true, 'daily_stock' => 100,
            ],
        ];

        $insertedPizzas = [];
        foreach ($pizzas as $p) {
            $insertedPizzas[] = \App\Models\Menu::create($p);
        }

        // ---- CALZONES ----
        $calzones = [
            ['name' => 'Beef Calzone', 'description' => 'Jumbo pizza pastel with Calzone sauce, filled with beef.', 'base_price' => 76000, 'category_id' => $catCalzone->id, 'image_url' => 'menus/calzone.png', 'is_available' => true, 'daily_stock' => 100],
            ['name' => 'Wagyu Calzone', 'description' => 'Jumbo pizza pastel with Calzone sauce, filled with wagyu beef.', 'base_price' => 154000, 'category_id' => $catCalzone->id, 'image_url' => 'menus/calzone.png', 'is_available' => true, 'daily_stock' => 100],
            ['name' => 'Bratwurst Calzone', 'description' => 'Jumbo pizza pastel with Calzone sauce, filled with bratwurst.', 'base_price' => 81000, 'category_id' => $catCalzone->id, 'image_url' => 'menus/calzone.png', 'is_available' => true, 'daily_stock' => 100],
            ['name' => 'Cheese Calzone', 'description' => 'Jumbo pizza pastel with Calzone sauce, filled with cheese.', 'base_price' => 75000, 'category_id' => $catCalzone->id, 'image_url' => 'menus/calzone.png', 'is_available' => true, 'daily_stock' => 100],
            ['name' => 'Luna Dolce Calzone', 'description' => 'Jumbo pizza pastel with sweet filling.', 'base_price' => 81000, 'category_id' => $catCalzone->id, 'image_url' => 'menus/calzone.png', 'is_available' => true, 'daily_stock' => 100],
        ];

        foreach ($calzones as $c) {
            \App\Models\Menu::create($c);
        }

        // ---- DRINKS ----
        $drinks = [
            ['name' => 'Iced Lychee Tea', 'description' => 'Cold beverage.', 'base_price' => 25000, 'category_id' => $catDrinks->id, 'image_url' => 'menus/iced_tea.png', 'is_available' => true, 'daily_stock' => 100],
            ['name' => 'Teh Manis (Panas)', 'description' => 'Teh manis panas.', 'base_price' => 10000, 'category_id' => $catDrinks->id, 'image_url' => 'menus/iced_tea.png', 'is_available' => true, 'daily_stock' => 100],
            ['name' => 'Teh Manis (Dingin)', 'description' => 'Teh manis dingin / es.', 'base_price' => 12000, 'category_id' => $catDrinks->id, 'image_url' => 'menus/iced_tea.png', 'is_available' => true, 'daily_stock' => 100],
            ['name' => 'Teh Tawar (Panas)', 'description' => 'Teh tawar panas.', 'base_price' => 8000, 'category_id' => $catDrinks->id, 'image_url' => 'menus/iced_tea.png', 'is_available' => true, 'daily_stock' => 100],
            ['name' => 'Teh Tawar (Dingin)', 'description' => 'Teh tawar dingin / es.', 'base_price' => 10000, 'category_id' => $catDrinks->id, 'image_url' => 'menus/iced_tea.png', 'is_available' => true, 'daily_stock' => 100],
            ['name' => 'Kopi Tubruk', 'description' => 'Traditional coffee.', 'base_price' => 12000, 'category_id' => $catDrinks->id, 'image_url' => 'menus/iced_tea.png', 'is_available' => true, 'daily_stock' => 100],
            ['name' => 'Arabika Tubruk', 'description' => 'Arabica coffee tubruk.', 'base_price' => 20000, 'category_id' => $catDrinks->id, 'image_url' => 'menus/iced_tea.png', 'is_available' => true, 'daily_stock' => 100],
            ['name' => 'Air Mineral 400ml', 'description' => 'Mineral water.', 'base_price' => 8000, 'category_id' => $catDrinks->id, 'image_url' => 'menus/iced_tea.png', 'is_available' => true, 'daily_stock' => 100],
            ['name' => 'Teh Soda 330ml', 'description' => 'Tea with soda.', 'base_price' => 12000, 'category_id' => $catDrinks->id, 'image_url' => 'menus/iced_tea.png', 'is_available' => true, 'daily_stock' => 100],
        ];

        foreach ($drinks as $d) {
            \App\Models\Menu::create($d);
        }

        // ---- EXTRA TOPPINGS (Customizations) ----
        $extraToppings = [
            ['type' => 'topping', 'name' => 'Mozzarella', 'additional_price' => 15000],
            ['type' => 'topping', 'name' => 'Beef', 'additional_price' => 15000],
            ['type' => 'topping', 'name' => 'Enoki', 'additional_price' => 5000],
            ['type' => 'topping', 'name' => 'Hot Honey Sauce', 'additional_price' => 10000],
        ];

        // Apply toppings to all pizzas and calzones
        $foodItems = \App\Models\Menu::whereIn('category_id', [$catPizza->id, $catCalzone->id])->get();
        foreach ($foodItems as $food) {
            foreach ($extraToppings as $topping) {
                \App\Models\Customization::create([
                    'menu_id' => $food->id,
                    'type' => $topping['type'],
                    'name' => $topping['name'],
                    'additional_price' => $topping['additional_price']
                ]);
            }
        }
    }
}
