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
            $table->foreignId('ingredient_id')->nullable()->constrained('ingredients')->nullOnDelete()->after('additional_price');
            $table->decimal('ingredient_qty', 10, 2)->default(0)->after('ingredient_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customizations', function (Blueprint $table) {
            $table->dropForeign(['ingredient_id']);
            $table->dropColumn(['ingredient_id', 'ingredient_qty']);
        });
    }
};
