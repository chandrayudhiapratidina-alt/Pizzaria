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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('biteship_order_id')->nullable()->unique();
            $table->string('biteship_tracking_id')->nullable()->unique();
            $table->string('waybill_id')->nullable();
            $table->string('courier_company')->nullable();
            $table->string('courier_type')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('driver_photo_url')->nullable();
            $table->string('live_tracking_url')->nullable();
            $table->string('status')->default('placed');
            $table->text('raw_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
