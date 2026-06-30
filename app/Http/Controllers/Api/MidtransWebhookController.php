<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\StockDeductionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    /**
     * Handle notification/webhook dari Midtrans (server-to-server).
     * 
     * Endpoint ini dipanggil oleh server Midtrans, BUKAN oleh browser pelanggan.
     * Signature divalidasi menggunakan SHA512 hash.
     * 
     * Flow:
     * 1. Validasi signature key
     * 2. Simpan ke tabel payments (audit trail)
     * 3. Update status order berdasarkan transaction_status
     * 4. Potong stok jika pembayaran berhasil
     * 5. Return HTTP 200 agar Midtrans tidak retry
     */
    public function handle(Request $request, StockDeductionService $stockService)
    {
        $payload = $request->all();

        Log::info('Midtrans Webhook Received', ['order_id' => $payload['order_id'] ?? 'unknown']);

        // ═══════════════════════════════════════════════════════════
        // 1. VALIDASI SIGNATURE (Anti-Manipulasi)
        // ═══════════════════════════════════════════════════════════
        $serverKey = config('midtrans.server_key');

        if (empty($serverKey)) {
            Log::error('Midtrans webhook: Server key not configured');
            return response()->json(['error' => 'Server configuration error'], 500);
        }

        $orderId      = $payload['order_id'] ?? '';
        $statusCode   = $payload['status_code'] ?? '';
        $grossAmount  = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($expectedSignature !== $signatureKey) {
            Log::warning('Midtrans webhook: Invalid signature', [
                'order_id' => $orderId,
                'expected' => substr($expectedSignature, 0, 20) . '...',
                'received' => substr($signatureKey, 0, 20) . '...',
            ]);
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        // ═══════════════════════════════════════════════════════════
        // 2. EXTRACT ORDER
        // ═══════════════════════════════════════════════════════════
        // Format order_id dari Midtrans: "ORD-YYYYMMDD-XXXX" atau legacy "ORD-123"
        $rawOrderId = $orderId;
        
        // Cari order berdasarkan order_number terlebih dahulu
        $order = Order::where('order_number', $rawOrderId)->first();
        
        // Fallback: cari berdasarkan format legacy "ORD-{id}"
        if (!$order && preg_match('/^ORD-(\d+)$/', $rawOrderId, $matches)) {
            $order = Order::find($matches[1]);
        }

        if (!$order) {
            Log::warning('Midtrans webhook: Order not found', ['order_id' => $rawOrderId]);
            return response()->json(['error' => 'Order not found'], 404);
        }

        // ═══════════════════════════════════════════════════════════
        // 3. SIMPAN KE TABEL PAYMENTS (Audit Trail)
        // ═══════════════════════════════════════════════════════════
        $transactionStatus = $payload['transaction_status'] ?? '';
        $paymentType       = $payload['payment_type'] ?? '';
        $transactionId     = $payload['transaction_id'] ?? '';
        $fraudStatus       = $payload['fraud_status'] ?? null;

        // Tentukan payment_channel
        $paymentChannel = $paymentType;
        if (!empty($payload['va_numbers']) && is_array($payload['va_numbers'])) {
            $paymentChannel = $payload['va_numbers'][0]['bank'] ?? $paymentType;
        } elseif (!empty($payload['issuer'])) {
            $paymentChannel = $payload['issuer'];
        }

        Payment::updateOrCreate(
            ['transaction_id' => $transactionId],
            [
                'order_id'         => $order->id,
                'payment_method'   => $paymentType,
                'payment_channel'  => $paymentChannel,
                'amount'           => $grossAmount,
                'status'           => $transactionStatus,
                'transaction_time' => $payload['transaction_time'] ?? null,
                'fraud_status'     => $fraudStatus,
                'signature_key'    => $signatureKey,
                'raw_response'     => $payload,
            ]
        );

        // ═══════════════════════════════════════════════════════════
        // 4. UPDATE ORDER STATUS
        // ═══════════════════════════════════════════════════════════
        switch ($transactionStatus) {
            case 'capture':
                // Capture hanya berlaku untuk kartu kredit
                // Cek fraud_status
                if ($fraudStatus === 'accept' || $fraudStatus === null) {
                    $this->markAsPaid($order, $paymentType, $stockService);
                } elseif ($fraudStatus === 'challenge') {
                    Log::warning("Midtrans webhook: Payment challenged for Order #{$order->id}");
                    // Biarkan pending, tunggu admin review
                }
                break;

            case 'settlement':
                // Settlement = pembayaran berhasil (VA, QRIS, e-Wallet)
                $this->markAsPaid($order, $paymentType, $stockService);
                break;

            case 'pending':
                // Midtrans mengirim status pending — order sudah dibuat, tunggu bayar
                // Tidak perlu ubah apa-apa
                Log::info("Midtrans webhook: Payment pending for Order #{$order->id}");
                break;

            case 'deny':
            case 'cancel':
                $this->markAsCancelled($order, 'cancelled');
                break;

            case 'expire':
                $this->markAsCancelled($order, 'expired');
                break;

            case 'refund':
            case 'partial_refund':
                $order->payment_status = 'refunded';
                $order->save();
                Log::info("Midtrans webhook: Refund for Order #{$order->id}");
                break;
        }

        // ═══════════════════════════════════════════════════════════
        // 5. RETURN 200 — Midtrans tidak akan retry
        // ═══════════════════════════════════════════════════════════
        return response()->json(['status' => 'ok']);
    }

    /**
     * Tandai order sebagai sudah dibayar dan masukkan ke dapur.
     */
    private function markAsPaid(Order $order, string $paymentType, StockDeductionService $stockService): void
    {
        // Guard: jangan double-process
        if ($order->payment_status === 'paid') {
            Log::info("Midtrans webhook: Order #{$order->id} already paid, skipping.");
            return;
        }

        $order->payment_status = 'paid';
        $order->payment_method = $paymentType;
        $order->paid_at = now();

        // ═══════════════════════════════════════════════════════
        // KUNCI KEAMANAN: Order online BARU masuk dapur di sini
        // ═══════════════════════════════════════════════════════
        if ($order->isOnline() && $order->order_status === 'pending_payment') {
            $order->order_status = 'confirmed';
        }

        $order->save();

        // [BITESHIP] Jika pesanan ini adalah delivery, panggil kurir via Biteship
        if ($order->isDelivery()) {
            try {
                $biteshipService = new \App\Services\BiteshipService();
                $biteshipResponse = $biteshipService->createOrder($order);
                if ($biteshipResponse['success']) {
                    Log::info("Biteship order created for Order #{$order->id}");
                } else {
                    Log::error("Failed to create Biteship order for Order #{$order->id}: " . $biteshipResponse['message']);
                }
            } catch (\Exception $e) {
                Log::error("Exception creating Biteship order for Order #{$order->id}: " . $e->getMessage());
            }
        }

        // Stok bahan baku SUDAH dipotong saat proses checkout di OnlineOrderController
        // Hal ini untuk mengamankan stok secara instan (Reserve on Checkout).
        // Oleh karena itu, TIDAK ADA LAGI pemotongan stok di tahap ini untuk mencegah Double-Deduction.

        // Track promo usage
        if ($order->promotion_id) {
            \App\Models\Promotion::where('id', $order->promotion_id)->increment('used_count');
            \App\Models\PromotionRedemption::where('order_id', $order->id)->update(['status' => 'applied']);
        }

        Log::info("Midtrans webhook: Order #{$order->id} marked as PAID via {$paymentType}");
    }

    /**
     * Tandai order sebagai dibatalkan/expired.
     */
    private function markAsCancelled(Order $order, string $reason): void
    {
        // Jangan cancel order yang sudah paid
        if ($order->payment_status === 'paid') {
            Log::warning("Midtrans webhook: Attempted to cancel already-paid Order #{$order->id}");
            return;
        }

        $order->payment_status = $reason; // 'expired' atau 'cancelled'
        $order->order_status = 'cancelled';
        $order->save();

        // KEMBALIKAN STOK! Karena stok sudah dipotong saat checkout (Reserve on Checkout),
        // jika pesanan dibatalkan/kadaluarsa, stok harus dikembalikan agar tidak terjadi Inventory Hoarding.
        try {
            $stockService = new \App\Services\StockDeductionService();
            $stockService->restoreOrderStock($order);
        } catch (\Exception $e) {
            Log::error("Stock restoration failed for cancelled Order #{$order->id} (Webhook): " . $e->getMessage());
        }

        if ($order->promotion_id) {
            \App\Models\PromotionRedemption::where('order_id', $order->id)->update(['status' => 'cancelled']);
        }

        Log::info("Midtrans webhook: Order #{$order->id} marked as {$reason}");
    }
}
