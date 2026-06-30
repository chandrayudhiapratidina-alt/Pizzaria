<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel payments — audit trail setiap transaksi pembayaran.
     * Satu order bisa punya beberapa attempt pembayaran (retry, expired, dll).
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('payment_method', 30)->nullable();    // qris, cash, bank_transfer, ewallet
            $table->string('payment_channel', 50)->nullable();   // bca_va, gopay, shopeepay, dll
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status', 20)->default('pending');    // pending, settlement, capture, deny, cancel, expire, refund
            $table->string('transaction_id', 100)->nullable();   // ID dari Midtrans
            $table->timestamp('transaction_time')->nullable();
            $table->string('fraud_status', 20)->nullable();      // accept, deny, challenge
            $table->text('signature_key')->nullable();
            $table->json('raw_response')->nullable();            // Full payload dari gateway
            $table->timestamps();

            $table->index('transaction_id');
            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
