<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Customization;
use App\Models\Ingredient;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        // Temukan kategori
        $catPizza = Category::where('slug', 'pizza')->first()->id ?? 1;
        $catBeverage = Category::where('slug', 'beverage')->first()->id ?? 2;
        $catSnack = Category::where('slug', 'snack')->first()->id ?? 3;

        // Bersihkan data lama
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Customization::truncate();
        DB::table('menu_ingredients')->truncate();
        Menu::truncate();
        Ingredient::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ==========================================
        // 0. Bahan Baku (Ingredients)
        // ==========================================
        $ingTepung = Ingredient::create(['name' => 'Tepung Terigu', 'stock_qty' => 5000, 'unit' => 'g', 'minimum_stock_alert' => 1000]);
        $ingKeju = Ingredient::create(['name' => 'Keju Mozzarella', 'stock_qty' => 2000, 'unit' => 'g', 'minimum_stock_alert' => 500]);
        $ingRagi = Ingredient::create(['name' => 'Ragi', 'stock_qty' => 500, 'unit' => 'g', 'minimum_stock_alert' => 100]);
        
        $ingColaSyrup = Ingredient::create(['name' => 'Sirup Cola', 'stock_qty' => 2000, 'unit' => 'ml', 'minimum_stock_alert' => 500]);
        $ingEsBatu = Ingredient::create(['name' => 'Es Batu', 'stock_qty' => 10000, 'unit' => 'g', 'minimum_stock_alert' => 2000]);
        
        $ingPotato = Ingredient::create(['name' => 'Kentang', 'stock_qty' => 3000, 'unit' => 'g', 'minimum_stock_alert' => 1000]);

        // ==========================================
        // 1. Makanan Utama (5 Varian Pizza)
        // ==========================================
        $pizzas = [
            [
                'name' => 'Meat Lovers',
                'description' => 'Pizza kaya daging dengan sosis, daging sapi cincang, dan pepperoni.',
                'base_price' => 50000,
                'image_url' => '/images/catalog/meat_lovers_pizza.png',
                'category_id' => $catPizza,
            ],
            [
                'name' => 'Margherita',
                'description' => 'Pizza klasik dengan saus tomat segar, keju mozzarella, dan daun basil.',
                'base_price' => 40000,
                'image_url' => '/images/catalog/margherita_pizza.png',
                'category_id' => $catPizza,
            ],
            [
                'name' => 'Pepperoni',
                'description' => 'Pizza favorit sepanjang masa dengan irisan pepperoni gurih dan keju melimpah.',
                'base_price' => 45000,
                'image_url' => '/images/catalog/pepperoni_pizza.png',
                'category_id' => $catPizza,
            ],
            [
                'name' => 'Cheese Classic',
                'description' => 'Pizza super keju dengan paduan mozzarella, parmesan, dan cheddar.',
                'base_price' => 45000,
                'image_url' => '/images/catalog/cheese_classic_pizza.png',
                'category_id' => $catPizza,
            ],
            [
                'name' => 'Veggie Supreme',
                'description' => 'Pizza sehat dengan aneka sayuran segar: paprika, jamur, zaitun, dan bawang.',
                'base_price' => 42000,
                'image_url' => '/images/catalog/veggie_supreme_pizza.png',
                'category_id' => $catPizza,
            ]
        ];

        foreach ($pizzas as $p) {
            $menu = Menu::create(array_merge($p, ['is_available' => true]));
            
            // Mapping Resep BOM untuk setiap pizza (Asumsi standar: 200g Tepung, 50g Keju, 10g Ragi)
            $menu->ingredients()->attach([
                $ingTepung->id => ['qty_needed' => 200],
                $ingKeju->id => ['qty_needed' => 50],
                $ingRagi->id => ['qty_needed' => 10],
            ]);
            
            // Atribut Varian: Ukuran (Medium / Small)
            Customization::create(['menu_id' => $menu->id, 'type' => 'size', 'name' => 'Small', 'additional_price' => 0]);
            Customization::create(['menu_id' => $menu->id, 'type' => 'size', 'name' => 'Medium', 'additional_price' => 20000]);

            // Tambahan BOM mapping untuk addon (Contoh: Cheese Crust memotong 50g Keju Mozzarella)
            Customization::create(['menu_id' => $menu->id, 'type' => 'crust', 'name' => 'Original', 'additional_price' => 0]);
            Customization::create([
                'menu_id' => $menu->id, 
                'type' => 'crust', 
                'name' => 'Cheese Crust', 
                'additional_price' => 15000,
                'deduct_ingredient_id' => $ingKeju->id,
                'deduct_qty' => 50
            ]);
        }

        // ==========================================
        // 2. Minuman (3 Varian)
        // ==========================================
        $drinks = [
            [
                'name' => 'Cola',
                'description' => 'Minuman soda yang menyegarkan.',
                'base_price' => 15000,
                'image_url' => '/images/catalog/cola_drink.png',
                'category_id' => $catBeverage,
                'bom' => [ $ingColaSyrup->id => 50 ] // 50ml syrup
            ],
            [
                'name' => 'Lemon Tea',
                'description' => 'Teh rasa lemon yang manis dan asam segar.',
                'base_price' => 12000,
                'image_url' => '/images/catalog/lemon_tea.png',
                'category_id' => $catBeverage,
            ],
            [
                'name' => 'Air Mineral',
                'description' => 'Air mineral murni.',
                'base_price' => 8000,
                'image_url' => '/images/catalog/mineral_water.png',
                'category_id' => $catBeverage,
            ]
        ];

        foreach ($drinks as $d) {
            $bom = $d['bom'] ?? [];
            unset($d['bom']);

            $menu = Menu::create(array_merge($d, ['is_available' => true]));
            
            foreach($bom as $ingId => $qty) {
                $menu->ingredients()->attach($ingId, ['qty_needed' => $qty]);
            }
            
            // Atribut: Dingin/Normal
            Customization::create(['menu_id' => $menu->id, 'type' => 'temperature', 'name' => 'Normal', 'additional_price' => 0]);
            Customization::create([
                'menu_id' => $menu->id, 
                'type' => 'temperature', 
                'name' => 'Dingin (Es)', 
                'additional_price' => 2000,
                'deduct_ingredient_id' => $ingEsBatu->id,
                'deduct_qty' => 100 // Butuh 100g es batu
            ]);
        }

        // ==========================================
        // 3. Snacks / Opsi Lain
        // ==========================================
        $snacks = [
            [
                'name' => 'Garlic Bread',
                'description' => 'Roti panggang dengan olesan mentega bawang putih yang wangi.',
                'base_price' => 20000,
                'image_url' => '/images/catalog/garlic_bread.png',
                'category_id' => $catSnack,
            ],
            [
                'name' => 'French Fries',
                'description' => 'Kentang goreng renyah dan gurih.',
                'base_price' => 25000,
                'image_url' => '/images/catalog/french_fries.png',
                'category_id' => $catSnack,
                'bom' => [ $ingPotato->id => 150 ] // Butuh 150g kentang
            ],
            [
                'name' => 'Chicken Wings',
                'description' => 'Sayap ayam bumbu pedas manis panggang.',
                'base_price' => 35000,
                'image_url' => '/images/catalog/chicken_wings.png',
                'category_id' => $catSnack,
            ]
        ];

        foreach ($snacks as $s) {
            $bom = $s['bom'] ?? [];
            unset($s['bom']);

            $menu = Menu::create(array_merge($s, ['is_available' => true]));
            
            foreach($bom as $ingId => $qty) {
                $menu->ingredients()->attach($ingId, ['qty_needed' => $qty]);
            }
        }
    }
}
