<?php

use Illuminate\Support\Facades\DB;

// Clear existing to avoid duplicates
DB::table('menu_ingredients')->truncate();

$recipes = [
    // Manzo Super Tenero
    9 => [
        1 => 1, // 1 pcs Pizza Dough
        3 => 200, // 200g Mozzarella
        4 => 50, // 50g Pepperoni (beef proxy)
    ],
    // Fiore Bianco
    10 => [
        1 => 1, 
        3 => 150, 
    ],
    // Pizza Bianco
    11 => [
        1 => 1, 
        3 => 100, 
    ],
    // Fiore Rosso
    12 => [
        1 => 1, 
        2 => 150, // 150g Tomato Sauce
        3 => 100, 
    ],
    // Beef Calzone
    13 => [
        1 => 1, 
        2 => 50, 
        3 => 50, 
        4 => 50,
    ],
    // Wagyu Calzone
    14 => [
        1 => 1, 
        2 => 50, 
        3 => 50, 
        4 => 50,
    ],
    // Bratwurst Calzone
    15 => [
        1 => 1, 
        2 => 50, 
        3 => 50, 
        4 => 50,
    ],
    // Cheese Calzone
    16 => [
        1 => 1, 
        3 => 150, 
    ],
    // Luna Dolce Calzone
    17 => [
        1 => 1, 
    ],
];

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
echo "Recipes mapped successfully!\n";
