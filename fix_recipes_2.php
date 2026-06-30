<?php

use Illuminate\Support\Facades\DB;

$recipes = [
    // Pizza Carne e Parmigiana
    1 => [
        1 => 1, // Dough
        2 => 100, // Tomato Sauce
        3 => 150, // Mozzarella
        4 => 50, // Pepperoni
    ],
    // Pizza Beef
    2 => [
        1 => 1, 
        2 => 100, 
        3 => 150, 
        4 => 50, 
    ],
    // Pizza Con Pesto di Parmigiano
    3 => [
        1 => 1, 
        3 => 150, 
    ],
    // Pizza Margherita
    4 => [
        1 => 1, 
        2 => 100, 
        3 => 150, 
    ],
    // Pizza Bratwurst
    5 => [
        1 => 1, 
        2 => 100, 
        3 => 150, 
    ],
    // Pizza Enoki
    6 => [
        1 => 1, 
        2 => 100, 
        3 => 150, 
    ],
    // Pizza Beef Pepperoni
    7 => [
        1 => 1, 
        2 => 100, 
        3 => 150, 
        4 => 100, // Pepperoni
    ],
    // Pizza Marinara
    8 => [
        1 => 1, 
        2 => 200, // Tomato Sauce (extra)
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
echo "Recipes 1-8 mapped successfully!\n";
