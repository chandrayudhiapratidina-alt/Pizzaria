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
        Schema::table('menus', function (Blueprint $table) {
            $table->decimal('discount_price', 10, 2)->nullable()->after('base_price');
            $table->dateTime('discount_start')->nullable()->after('discount_price');
            $table->dateTime('discount_end')->nullable()->after('discount_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['discount_price', 'discount_start', 'discount_end']);
        });
    }
};
