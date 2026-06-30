<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class MenuIngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Bersihkan tabel relasi (agar tidak duplikat jika dijalankan ulang)
        DB::table('menu_ingredients')->truncate();
        
        // Disable foreign key checks for clearing ingredients
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('ingredients')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Buat Daftar Bahan Baku Utama
        $ingredients = [
            'Pizza Dough' => ['unit' => 'Pcs', 'stock' => 500, 'min_alert' => 50],
            'Keju Mozzarella' => ['unit' => 'Gram', 'stock' => 50000, 'min_alert' => 5000],
            'Saus Tomat Murni' => ['unit' => 'Ml', 'stock' => 30000, 'min_alert' => 3000],
            'Daging Sapi Cincang (Beef)' => ['unit' => 'Gram', 'stock' => 20000, 'min_alert' => 2000],
            'Irisan Pepperoni' => ['unit' => 'Gram', 'stock' => 15000, 'min_alert' => 1500],
            'Sosis Bratwurst' => ['unit' => 'Gram', 'stock' => 15000, 'min_alert' => 1500],
            'Daging Wagyu' => ['unit' => 'Gram', 'stock' => 10000, 'min_alert' => 1000],
            'Jamur Enoki' => ['unit' => 'Gram', 'stock' => 4000, 'min_alert' => 400], // Dipisah
            'Jamur Kancing' => ['unit' => 'Gram', 'stock' => 4000, 'min_alert' => 400], // Dipisah
            'Daun Teh Kering' => ['unit' => 'Gram', 'stock' => 5000, 'min_alert' => 500], // Dipisah
            'Buah Leci (Kaleng)' => ['unit' => 'Pcs', 'stock' => 200, 'min_alert' => 20], // Dipisah
            'Biji Kopi Arabika' => ['unit' => 'Gram', 'stock' => 5000, 'min_alert' => 500],
        ];

        $ingredientModels = [];
        foreach ($ingredients as $name => $data) {
            $ingredientModels[$name] = Ingredient::create([
                'name' => $name,
                'unit' => $data['unit'],
                'stock_qty' => $data['stock'],
                'minimum_stock_alert' => $data['min_alert'],
            ]);
        }

        // 3. Pasangkan Bahan Baku ke Masing-masing Menu (BOM / Recipe)
        $menus = Menu::all();

        foreach ($menus as $menu) {
            $menuName = strtolower($menu->name);
            $recipe = [];

            // ==========================================
            // Kategori: PIZZA & CALZONE (Menu 1 - 17)
            // ==========================================
            if (str_contains($menuName, 'pizza') || str_contains($menuName, 'calzone') || str_contains($menuName, 'manzo') || str_contains($menuName, 'fiore')) {
                // Base semua pizza: Dough 1 pcs, Mozzarella 120 gram, Saus Tomat 80 ml
                $recipe[$ingredientModels['Pizza Dough']->id] = ['qty_needed' => 1];
                $recipe[$ingredientModels['Keju Mozzarella']->id] = ['qty_needed' => 120];
                $recipe[$ingredientModels['Saus Tomat Murni']->id] = ['qty_needed' => 80];

                // Topping khusus berdasarkan nama menu
                if (str_contains($menuName, 'beef') || str_contains($menuName, 'carne') || str_contains($menuName, 'manzo')) {
                    $recipe[$ingredientModels['Daging Sapi Cincang (Beef)']->id] = ['qty_needed' => 80];
                }
                
                if (str_contains($menuName, 'pepperoni')) {
                    $recipe[$ingredientModels['Irisan Pepperoni']->id] = ['qty_needed' => 50];
                }
                
                if (str_contains($menuName, 'bratwurst')) {
                    $recipe[$ingredientModels['Sosis Bratwurst']->id] = ['qty_needed' => 70];
                }
                
                if (str_contains($menuName, 'wagyu')) {
                    $recipe[$ingredientModels['Daging Wagyu']->id] = ['qty_needed' => 100];
                }
                
                if (str_contains($menuName, 'enoki')) {
                    $recipe[$ingredientModels['Jamur Enoki']->id] = ['qty_needed' => 30];
                    $recipe[$ingredientModels['Jamur Kancing']->id] = ['qty_needed' => 30];
                }

                // Jika Margherita/Cheese, tambah extra mozzarella
                if (str_contains($menuName, 'margherita') || str_contains($menuName, 'cheese') || str_contains($menuName, 'bianco')) {
                    $recipe[$ingredientModels['Keju Mozzarella']->id] = ['qty_needed' => 200]; // Extra Cheese
                }
            }
            
            // ==========================================
            // Kategori: MINUMAN (Teh & Kopi)
            // ==========================================
            if (str_contains($menuName, 'teh')) {
                $recipe[$ingredientModels['Daun Teh Kering']->id] = ['qty_needed' => 10]; // 10 gram teh
                
                if (str_contains($menuName, 'lychee') || str_contains($menuName, 'leci')) {
                    $recipe[$ingredientModels['Buah Leci (Kaleng)']->id] = ['qty_needed' => 2]; // 2 buah leci
                }
            }
            
            if (str_contains($menuName, 'kopi') || str_contains($menuName, 'arabika')) {
                $recipe[$ingredientModels['Biji Kopi Arabika']->id] = ['qty_needed' => 25]; // 25 gram per cup
            }

            // Eksekusi pemasangan ke database
            if (!empty($recipe)) {
                $menu->ingredients()->sync($recipe);
            }
        }
    }
}
