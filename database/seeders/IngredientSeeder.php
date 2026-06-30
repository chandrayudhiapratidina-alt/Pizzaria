<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = [
            ['name' => 'Pizza Dough', 'stock_qty' => 100, 'unit' => 'pcs', 'minimum_stock_alert' => 20],
            ['name' => 'Tomato Sauce', 'stock_qty' => 5000, 'unit' => 'gram', 'minimum_stock_alert' => 1000],
            ['name' => 'Mozzarella Cheese', 'stock_qty' => 5000, 'unit' => 'gram', 'minimum_stock_alert' => 1000],
            ['name' => 'Pepperoni', 'stock_qty' => 2000, 'unit' => 'gram', 'minimum_stock_alert' => 500],
        ];

        foreach ($ingredients as $ingredient) {
            \App\Models\Ingredient::create($ingredient);
        }
    }
}
