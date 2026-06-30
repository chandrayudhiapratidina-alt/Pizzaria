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
        Schema::table('promotions', function (Blueprint $table) {
            $table->string('banner_title')->nullable()->after('description');
            $table->string('banner_subtitle')->nullable()->after('banner_title');
            $table->string('background_image')->nullable()->after('banner_subtitle');
            $table->string('theme_color', 7)->nullable()->after('background_image');
            $table->string('icon')->nullable()->after('theme_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn(['banner_title', 'banner_subtitle', 'background_image', 'theme_color', 'icon']);
        });
    }
};
