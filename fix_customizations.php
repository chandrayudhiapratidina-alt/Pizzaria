<?php

$menus = [10,11,12,13,14,15,16,17]; 
\App\Models\Customization::whereIn('menu_id', $menus)->delete(); 
\App\Models\Customization::insert([
    [
        'category_id'=>1,
        'type'=>'topping',
        'name'=>'Mozzarella',
        'additional_price'=>15000,
        'deduct_ingredient_id'=>3, // Mozzarella Cheese
        'deduct_qty'=>50,
        'stock'=>0
    ],
    [
        'category_id'=>1,
        'type'=>'topping',
        'name'=>'Beef',
        'additional_price'=>15000,
        'deduct_ingredient_id'=>4, // Pepperoni for Beef
        'deduct_qty'=>50,
        'stock'=>0
    ],
    [
        'category_id'=>1,
        'type'=>'topping',
        'name'=>'Enoki',
        'additional_price'=>5000,
        'deduct_ingredient_id'=>null,
        'deduct_qty'=>0,
        'stock'=>0
    ],
    [
        'category_id'=>1,
        'type'=>'topping',
        'name'=>'Hot Honey Sauce',
        'additional_price'=>10000,
        'deduct_ingredient_id'=>null,
        'deduct_qty'=>0,
        'stock'=>0
    ]
]);
