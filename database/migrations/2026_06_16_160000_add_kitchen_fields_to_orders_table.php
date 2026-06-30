<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('cooking_started_at')->nullable()->after('order_status');
            $table->timestamp('cooking_completed_at')->nullable()->after('cooking_started_at');
            $table->boolean('stock_deducted')->default(false)->after('cooking_completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['cooking_started_at', 'cooking_completed_at', 'stock_deducted']);
        });
    }
};
