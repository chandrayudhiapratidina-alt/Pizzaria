<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migrasi data kategori dari ENUM ke foreign key.
     */
    public function up(): void
    {
        // 1. Seed kategori default dari nilai enum lama
        $defaults = [
            ['name' => 'Pizza',    'slug' => 'pizza',    'icon' => 'fa-solid fa-pizza-slice', 'sort_order' => 1],
            ['name' => 'Beverage', 'slug' => 'beverage', 'icon' => 'fa-solid fa-mug-hot',     'sort_order' => 2],
            ['name' => 'Snack',    'slug' => 'snack',    'icon' => 'fa-solid fa-cookie-bite',  'sort_order' => 3],
            ['name' => 'Pasta',    'slug' => 'pasta',    'icon' => 'fa-solid fa-bowl-food',    'sort_order' => 4],
            ['name' => 'Dessert',  'slug' => 'dessert',  'icon' => 'fa-solid fa-ice-cream',    'sort_order' => 5],
        ];

        $now = now();
        foreach ($defaults as $cat) {
            DB::table('categories')->insertOrIgnore(array_merge($cat, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // 2. Tambah kolom category_id ke menus
        Schema::table('menus', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('is_available')->constrained()->nullOnDelete();
        });

        // 3. Migrasi data: map nilai enum lama ke category_id
        $categories = DB::table('categories')->pluck('id', 'slug');
        
        DB::table('menus')->orderBy('id')->chunk(1000, function ($menus) use ($categories) {
            foreach ($menus as $menu) {
                $slug = strtolower($menu->category ?? 'pizza');
                $categoryId = $categories[$slug] ?? $categories->first();
                DB::table('menus')->where('id', $menu->id)->update(['category_id' => $categoryId]);
            }
        });

        // 4. Hapus kolom enum lama
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add enum column
        Schema::table('menus', function (Blueprint $table) {
            $table->string('category')->default('pizza')->after('is_available');
        });

        // Migrate data back
        DB::table('menus')->orderBy('id')->chunk(1000, function ($menus) {
            foreach ($menus as $menu) {
                $category = DB::table('categories')->where('id', $menu->category_id)->first();
                DB::table('menus')->where('id', $menu->id)->update(['category' => $category ? $category->slug : 'pizza']);
            }
        });

        // Drop foreign key column
        Schema::table('menus', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }
};
