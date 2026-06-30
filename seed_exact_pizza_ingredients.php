<?php

use Illuminate\Support\Facades\DB;
use App\Models\Ingredient;

// Add missing ingredients
$missingIngredients = [
    ['name' => 'Fresh Meat / Daging Cincang', 'category' => 'menu_base', 'stock_qty' => 5000, 'unit' => 'g', 'minimum_stock_alert' => 1000],
    ['name' => 'Formaggio Parmigiano', 'category' => 'topping', 'stock_qty' => 3000, 'unit' => 'g', 'minimum_stock_alert' => 500],
    ['name' => 'Pesto Sauce', 'category' => 'menu_base', 'stock_qty' => 2000, 'unit' => 'g', 'minimum_stock_alert' => 500],
    ['name' => 'Bratwurst Sausage', 'category' => 'topping', 'stock_qty' => 4000, 'unit' => 'g', 'minimum_stock_alert' => 1000],
    ['name' => 'Enoki Mushroom', 'category' => 'topping', 'stock_qty' => 2000, 'unit' => 'g', 'minimum_stock_alert' => 500],
    ['name' => 'Garlic (Bawang Putih)', 'category' => 'menu_base', 'stock_qty' => 2000, 'unit' => 'g', 'minimum_stock_alert' => 300],
    ['name' => 'Oregano', 'category' => 'menu_base', 'stock_qty' => 500, 'unit' => 'g', 'minimum_stock_alert' => 100],
    ['name' => 'Basil Leaves', 'category' => 'menu_base', 'stock_qty' => 500, 'unit' => 'g', 'minimum_stock_alert' => 100],
    ['name' => 'Wagyu Beef', 'category' => 'topping', 'stock_qty' => 3000, 'unit' => 'g', 'minimum_stock_alert' => 1000],
    ['name' => 'Cream Cheese', 'category' => 'menu_base', 'stock_qty' => 2500, 'unit' => 'g', 'minimum_stock_alert' => 500],
    ['name' => 'Champignon Mushroom', 'category' => 'topping', 'stock_qty' => 2000, 'unit' => 'g', 'minimum_stock_alert' => 500],
    ['name' => 'Mushroom Ketchup', 'category' => 'menu_base', 'stock_qty' => 2000, 'unit' => 'ml', 'minimum_stock_alert' => 500],
    ['name' => 'Black Pepper', 'category' => 'menu_base', 'stock_qty' => 1000, 'unit' => 'g', 'minimum_stock_alert' => 200],
    ['name' => 'Calzone Sauce', 'category' => 'menu_base', 'stock_qty' => 3000, 'unit' => 'g', 'minimum_stock_alert' => 500],
    ['name' => 'Sweet Filling (Isian Manis)', 'category' => 'menu_base', 'stock_qty' => 2000, 'unit' => 'g', 'minimum_stock_alert' => 500],
];

foreach ($missingIngredients as $ing) {
    Ingredient::updateOrCreate(
        ['name' => $ing['name']],
        $ing
    );
}

// Fetch all ingredient IDs
$ings = Ingredient::pluck('id', 'name')->toArray();

// Base ones
$dough = $ings['Pizza Dough'];
$tomato = $ings['Tomato Sauce'];
$mozza = $ings['Mozzarella Cheese'];
$pepperoni = $ings['Pepperoni'];

// New ones
$fresh_meat = $ings['Fresh Meat / Daging Cincang'];
$parmesan = $ings['Formaggio Parmigiano'];
$pesto = $ings['Pesto Sauce'];
$bratwurst = $ings['Bratwurst Sausage'];
$enoki = $ings['Enoki Mushroom'];
$garlic = $ings['Garlic (Bawang Putih)'];
$oregano = $ings['Oregano'];
$basil = $ings['Basil Leaves'];
$wagyu = $ings['Wagyu Beef'];
$cream_cheese = $ings['Cream Cheese'];
$champignon = $ings['Champignon Mushroom'];
$mushroom_ketchup = $ings['Mushroom Ketchup'];
$black_pepper = $ings['Black Pepper'];
$calzone_sauce = $ings['Calzone Sauce'];
$sweet_filling = $ings['Sweet Filling (Isian Manis)'];

$recipes = [
    // 1. Pizza Carne e Parmigiana (Tomato, Mozzarella, Fresh meat, Parmesan)
    1 => [
        $dough => 1, 
        $tomato => 100, 
        $mozza => 150, 
        $fresh_meat => 100,
        $parmesan => 30
    ],
    // 2. Pizza Beef (Tomato, Mozzarella, Fresh meat)
    2 => [
        $dough => 1, 
        $tomato => 100, 
        $mozza => 150, 
        $fresh_meat => 100,
    ],
    // 3. Pizza Con Pesto di Parmigiano (Mozarella, Pesto, Parmesan)
    3 => [
        $dough => 1, 
        $mozza => 150, 
        $pesto => 50,
        $parmesan => 30
    ],
    // 4. Pizza Margherita (Tomato, Mozzarella, Parmesan)
    4 => [
        $dough => 1, 
        $tomato => 100, 
        $mozza => 150, 
        $parmesan => 20
    ],
    // 5. Pizza Bratwurst (Tomato, Mozzarella, Bratwurst)
    5 => [
        $dough => 1, 
        $tomato => 100, 
        $mozza => 150, 
        $bratwurst => 100
    ],
    // 6. Pizza Enoki (Tomato, Mozzarella, Enoki)
    6 => [
        $dough => 1, 
        $tomato => 100, 
        $mozza => 150, 
        $enoki => 80
    ],
    // 7. Pizza Beef Pepperoni (Tomato, Mozzarella, Beef pepperoni)
    7 => [
        $dough => 1, 
        $tomato => 100, 
        $mozza => 150, 
        $pepperoni => 100,
    ],
    // 8. Pizza Marinara (Garlic, Oregano, Basil. No cheese)
    8 => [
        $dough => 1, 
        $tomato => 150, // Extra tomato
        $garlic => 15,
        $oregano => 5,
        $basil => 5
    ],
    // 9. Manzo Super Tenero (Wagyu, Double Mozzarella, Parmesan)
    9 => [
        $dough => 1, 
        $tomato => 100,
        $mozza => 250, // Double 
        $wagyu => 150,
        $parmesan => 40
    ],
    // 10. Fiore Bianco (Mozzarella, Cream cheese, Champignon, Parmesan, Mushroom ketchup)
    10 => [
        $dough => 1, 
        $mozza => 150, 
        $cream_cheese => 50,
        $champignon => 50,
        $parmesan => 30,
        $mushroom_ketchup => 20
    ],
    // 11. Pizza Bianco (Mozzarella, Parmesan, Black pepper)
    11 => [
        $dough => 1, 
        $mozza => 150, 
        $parmesan => 40,
        $black_pepper => 5
    ],
    // 12. Fiore Rosso (Tomato, Mozzarella, Cream cheese, Champignon, Parmesan)
    12 => [
        $dough => 1, 
        $tomato => 100,
        $mozza => 150, 
        $cream_cheese => 50,
        $champignon => 50,
        $parmesan => 30
    ],
    // 13. Beef Calzone (Calzone sauce, beef)
    13 => [
        $dough => 1, 
        $calzone_sauce => 80, 
        $mozza => 100, 
        $fresh_meat => 100,
    ],
    // 14. Wagyu Calzone (Calzone sauce, wagyu)
    14 => [
        $dough => 1, 
        $calzone_sauce => 80, 
        $mozza => 100, 
        $wagyu => 100,
    ],
    // 15. Bratwurst Calzone (Calzone sauce, bratwurst)
    15 => [
        $dough => 1, 
        $calzone_sauce => 80, 
        $mozza => 100, 
        $bratwurst => 100,
    ],
    // 16. Cheese Calzone (Calzone sauce, cheese)
    16 => [
        $dough => 1, 
        $calzone_sauce => 80, 
        $mozza => 150, 
        $cream_cheese => 50
    ],
    // 17. Luna Dolce Calzone (Sweet filling)
    17 => [
        $dough => 1, 
        $sweet_filling => 150
    ],
];

// Re-map 1-17
DB::table('menu_ingredients')->whereBetween('menu_id', [1, 17])->delete();

$inserts = [];
foreach ($recipes as $menuId => $ingredients) {
    foreach ($ingredients as $ingredientId => $qty) {
        $inserts[] = [
            'menu_id' => $menuId,
            'ingredient_id' => $ingredientId,
            'qty_needed' => $qty,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

DB::table('menu_ingredients')->insert($inserts);
echo "Exact pizza ingredients mapped successfully!\n";
