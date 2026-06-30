<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Zona pengiriman untuk menghitung ongkos kirim secara dinamis.
     * Menggantikan delivery fee statis Rp 15.000.
     */
    public function up(): void
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');               // e.g. "Zona 1 — Kecamatan Sekitar"
            $table->text('description')->nullable(); // Deskripsi area coverage
            $table->decimal('fee', 12, 2);         // Ongkos kirim zona ini
            $table->decimal('min_distance_km', 8, 2)->default(0);  // Batas jarak minimum
            $table->decimal('max_distance_km', 8, 2)->nullable();  // Batas jarak maksimum
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_zones');
    }
};
