<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw statement to change enum to varchar
        DB::statement("ALTER TABLE customizations MODIFY type VARCHAR(255) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: revert back to enum, but skipping for simplicity
        // DB::statement("ALTER TABLE customizations MODIFY type ENUM('size', 'crust', 'topping') NOT NULL");
    }
};
