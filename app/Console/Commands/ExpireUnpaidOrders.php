<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireUnpaidOrders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'orders:expire-unpaid';

    /**
     * The console command description.
     */
    protected $description = 'Batalkan pesanan online yang belum dibayar melewati batas waktu (15 menit)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredOrders = Order::expiredPending()->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('Tidak ada pesanan expired.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($expiredOrders as $order) {
            $order->update([
                'payment_status' => 'expired',
                'order_status' => 'cancelled',
            ]);

            // Cancel transaksi di Midtrans juga
            try {
                \Midtrans\Config::$serverKey = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = config('midtrans.is_production');
                
                $midtransOrderId = $order->order_number ?: ('ORD-' . $order->id);
                \Midtrans\Transaction::cancel($midtransOrderId);
            } catch (\Exception $e) {
                // Non-fatal: Midtrans mungkin sudah expired secara otomatis
                Log::warning("Failed to cancel Midtrans txn for Order #{$order->id}: " . $e->getMessage());
            }

            $count++;
            $this->line("  ✗ Order #{$order->id} ({$order->order_number}) expired — dibatalkan.");
        }

        $this->info("Selesai. {$count} pesanan expired dibatalkan.");
        Log::info("ExpireUnpaidOrders: {$count} orders expired and cancelled.");

        return self::SUCCESS;
    }
}
