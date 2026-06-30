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
        Schema::table('customizations', function (Blueprint $table) {
            $table->foreignId('deduct_ingredient_id')->nullable()->after('additional_price')->constrained('ingredients')->nullOnDelete();
            $table->decimal('deduct_qty', 10, 2)->default(0)->after('deduct_ingredient_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customizations', function (Blueprint $table) {
            $table->dropForeign(['deduct_ingredient_id']);
            $table->dropColumn(['deduct_ingredient_id', 'deduct_qty']);
        });
    }
};
