<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan semua kustomisasi lama (yang repetitif per menu)
        \App\Models\Customization::query()->delete();

        // Ambil Kategori Pizza & Calzone
        $pizzaCat = \App\Models\Category::where('name', 'like', '%Pizza%')->first();
        
        if (!$pizzaCat) {
            $pizzaCat = \App\Models\Category::first();
        }

        // Ambil Bahan Baku
        $mozzarella = \App\Models\Ingredient::where('name', 'like', '%Mozzarella%')->first();
        $beef = \App\Models\Ingredient::where('name', 'like', '%Beef%')->first();
        $enoki = \App\Models\Ingredient::where('name', 'like', '%Enoki%')->first();

        // Buat Kustomisasi Kategori (Hanya untuk Pizza)
        $customizations = [
            [
                'category_id' => $pizzaCat->id,
                'menu_id' => null,
                'type' => 'topping',
                'name' => 'Ekstra Mozzarella',
                'additional_price' => 15000,
                'stock' => 100,
                'deduct_ingredient_id' => $mozzarella ? $mozzarella->id : null,
                'deduct_qty' => 50, // 50 gram
            ],
            [
                'category_id' => $pizzaCat->id,
                'menu_id' => null,
                'type' => 'topping',
                'name' => 'Ekstra Beef Cincang',
                'additional_price' => 15000,
                'stock' => 50,
                'deduct_ingredient_id' => $beef ? $beef->id : null,
                'deduct_qty' => 50, // 50 gram
            ],
            [
                'category_id' => $pizzaCat->id,
                'menu_id' => null,
                'type' => 'topping',
                'name' => 'Ekstra Jamur Enoki',
                'additional_price' => 5000,
                'stock' => 50,
                'deduct_ingredient_id' => $enoki ? $enoki->id : null,
                'deduct_qty' => 30, // 30 gram
            ],
            [
                'category_id' => $pizzaCat->id,
                'menu_id' => null,
                'type' => 'topping',
                'name' => 'Hot Honey Sauce',
                'additional_price' => 10000,
                'stock' => 100,
                'deduct_ingredient_id' => null, // Tidak terhubung bahan baku
                'deduct_qty' => 0,
            ],
        ];

        foreach ($customizations as $cust) {
            \App\Models\Customization::create($cust);
        }
    }
}
