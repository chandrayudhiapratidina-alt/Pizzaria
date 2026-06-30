<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('delivery_zones');

        DB::table('settings')->insert([
            ['key' => 'delivery_base_fee', 'value' => '5000', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'delivery_base_distance_km', 'value' => '2', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'delivery_fee_per_km', 'value' => '2000', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'delivery_max_distance_km', 'value' => '10', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->decimal('min_distance_km', 8, 2);
            $table->decimal('max_distance_km', 8, 2);
            $table->decimal('fee', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('settings')->whereIn('key', [
            'delivery_base_fee', 'delivery_base_distance_km', 'delivery_fee_per_km', 'delivery_max_distance_km'
        ])->delete();
    }
};
