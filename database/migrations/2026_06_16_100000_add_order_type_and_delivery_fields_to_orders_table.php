<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom untuk membedakan Dine-In vs Online (Delivery/Pickup)
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_type', 20)->default('dine_in')->after('id');  // dine_in, delivery, pickup
            $table->text('customer_address')->nullable()->after('customer_whatsapp');
            $table->string('customer_email')->nullable()->after('customer_address');
            $table->decimal('delivery_fee', 12, 2)->default(0)->after('total_amount');
        });

        // Ubah payment_method dari ENUM ke VARCHAR agar lebih fleksibel
        // Menampung: cash, qris, qris_online, bank_transfer, ewallet
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method VARCHAR(30) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_type', 'customer_address', 'customer_email', 'delivery_fee']);
        });

        // Kembalikan ke enum (best effort)
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('qris', 'cash') NULL");
    }
};
