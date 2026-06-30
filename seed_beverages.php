<?php

use Illuminate\Support\Facades\DB;
use App\Models\Ingredient;

// 1. Update existing categories
Ingredient::whereIn('id', [1, 2])->update(['category' => 'menu_base']); // Dough, Tomato Sauce
Ingredient::whereIn('id', [3, 4])->update(['category' => 'topping']); // Mozzarella, Pepperoni

// 2. Add new Beverage Ingredients
$newIngredients = [
    [
        'name' => 'Daun Teh (Tea Leaves)',
        'category' => 'beverage',
        'stock_qty' => 1000,
        'unit' => 'g',
        'minimum_stock_alert' => 200,
    ],
    [
        'name' => 'Biji Kopi Arabika',
        'category' => 'beverage',
        'stock_qty' => 2000,
        'unit' => 'g',
        'minimum_stock_alert' => 500,
    ],
    [
        'name' => 'Sirup Leci',
        'category' => 'beverage',
        'stock_qty' => 3000,
        'unit' => 'ml',
        'minimum_stock_alert' => 500,
    ],
    [
        'name' => 'Air Mineral Kemasan',
        'category' => 'beverage',
        'stock_qty' => 100,
        'unit' => 'pcs',
        'minimum_stock_alert' => 20,
    ],
    [
        'name' => 'Air Soda Kemasan',
        'category' => 'beverage',
        'stock_qty' => 100,
        'unit' => 'pcs',
        'minimum_stock_alert' => 20,
    ],
    [
        'name' => 'Gula Pasir / Cair',
        'category' => 'beverage',
        'stock_qty' => 5000,
        'unit' => 'g',
        'minimum_stock_alert' => 1000,
    ],
];

foreach ($newIngredients as $ing) {
    Ingredient::updateOrCreate(
        ['name' => $ing['name']],
        $ing
    );
}

// 3. Map Beverage Menus
// Menu IDs: 
// 18. Iced Lychee Tea -> Sirup Leci (30ml), Daun Teh (5g)
// 19. Teh Manis (Panas) -> Daun Teh (5g), Gula (15g)
// 20. Teh Manis (Dingin) -> Daun Teh (5g), Gula (15g)
// 21. Teh Tawar (Panas) -> Daun Teh (5g)
// 22. Teh Tawar (Dingin) -> Daun Teh (5g)
// 23. Kopi Tubruk -> Biji Kopi (15g), Gula (15g)
// 24. Arabika Tubruk -> Biji Kopi (20g)
// 25. Air Mineral 400ml -> Air Mineral Kemasan (1pcs)
// 26. Teh Soda 330ml -> Air Soda (1pcs), Daun Teh (5g)

$teaId = Ingredient::where('name', 'Daun Teh (Tea Leaves)')->first()->id;
$coffeeId = Ingredient::where('name', 'Biji Kopi Arabika')->first()->id;
$lycheeId = Ingredient::where('name', 'Sirup Leci')->first()->id;
$mineralId = Ingredient::where('name', 'Air Mineral Kemasan')->first()->id;
$sodaId = Ingredient::where('name', 'Air Soda Kemasan')->first()->id;
$sugarId = Ingredient::where('name', 'Gula Pasir / Cair')->first()->id;

$beverageRecipes = [
    18 => [
        $lycheeId => 30,
        $teaId => 5,
    ],
    19 => [
        $teaId => 5,
        $sugarId => 15,
    ],
    20 => [
        $teaId => 5,
        $sugarId => 15,
    ],
    21 => [
        $teaId => 5,
    ],
    22 => [
        $teaId => 5,
    ],
    23 => [
        $coffeeId => 15,
        $sugarId => 15,
    ],
    24 => [
        $coffeeId => 20,
    ],
    25 => [
        $mineralId => 1,
    ],
    26 => [
        $sodaId => 1,
        $teaId => 5,
    ],
];

$inserts = [];
foreach ($beverageRecipes as $menuId => $ingredients) {
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
echo "Beverages mapped successfully!\n";
