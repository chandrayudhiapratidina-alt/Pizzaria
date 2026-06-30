<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Upgrade tabel orders untuk standar E-commerce:
     * - order_number: Human-readable unique identifier
     * - subtotal_amount: Total sebelum diskon & ongkir
     * - paid_at: Timestamp pembayaran berhasil
     * - expires_at: Batas waktu bayar (15 menit untuk online orders)
     * - order_status & payment_status: Diperluas ke VARCHAR
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_number', 30)->nullable()->unique()->after('id');
            $table->decimal('subtotal_amount', 12, 2)->default(0)->after('total_amount');
            $table->timestamp('paid_at')->nullable()->after('snap_token');
            $table->timestamp('expires_at')->nullable()->after('paid_at');
        });

        // Ubah order_status dari ENUM ke VARCHAR agar bisa menampung status baru
        // Status baru: pending_payment, confirmed, cooking, ready, on_delivery, completed, served, cancelled
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_status VARCHAR(30) NOT NULL DEFAULT 'pending_payment'");
        
        // Ubah payment_status dari ENUM ke VARCHAR
        // Status baru: unpaid, pending, paid, expired, cancelled, refunded
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status VARCHAR(30) NOT NULL DEFAULT 'unpaid'");

        // Generate order_number untuk order yang sudah ada
        $orders = DB::table('orders')->whereNull('order_number')->orderBy('id')->get();
        foreach ($orders as $order) {
            $date = date('Ymd', strtotime($order->created_at));
            $seq = str_pad($order->id, 4, '0', STR_PAD_LEFT);
            DB::table('orders')->where('id', $order->id)->update([
                'order_number' => "ORD-{$date}-{$seq}",
                'subtotal_amount' => $order->total_amount + $order->discount_amount,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_number', 'subtotal_amount', 'paid_at', 'expires_at']);
        });

        // Best-effort revert ke enum
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_status ENUM('new','cooking','ready','served') NOT NULL DEFAULT 'new'");
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending','paid') NOT NULL DEFAULT 'pending'");
    }
};
