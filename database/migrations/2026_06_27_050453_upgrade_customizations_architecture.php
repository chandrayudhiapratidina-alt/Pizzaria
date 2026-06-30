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
            // Drop existing foreign key and make menu_id nullable
            $table->dropForeign(['menu_id']);
            $table->unsignedBigInteger('menu_id')->nullable()->change();

            // Add new category_id for category-level customizations
            $table->foreignId('category_id')->nullable()->after('menu_id')->constrained()->cascadeOnDelete();
            
            // Re-add foreign key for menu_id
            $table->foreign('menu_id')->references('id')->on('menus')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customizations', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            
            $table->dropForeign(['menu_id']);
            $table->unsignedBigInteger('menu_id')->nullable(false)->change();
            $table->foreign('menu_id')->references('id')->on('menus')->cascadeOnDelete();
        });
    }
};
