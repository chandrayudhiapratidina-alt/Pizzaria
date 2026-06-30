<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'order_type',
        'user_id', 'table_id', 'cashier_id', 'promotion_id',
        'customer_name', 'customer_whatsapp', 'customer_address', 'customer_email',
        'subtotal_amount', 'total_amount', 'delivery_fee', 'discount_amount',
        'payment_method', 'payment_status', 'order_status',
        'snap_token', 'paid_at', 'expires_at', 'stock_deducted',
        'latitude', 'longitude', 'delivery_distance_km',
        'cash_tendered', 'cash_change', 'payment_reference'
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'expires_at' => 'datetime',
            'stock_deducted' => 'boolean',
        ];
    }

    // ─── Auto-generate order_number ──────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }

            // Auto-set subtotal jika belum diisi
            if ($order->subtotal_amount <= 0 && $order->total_amount > 0) {
                $order->subtotal_amount = $order->total_amount + $order->discount_amount;
            }
        });
    }

    /**
     * Generate order number format: ORD-YYYYMMDD-XXXX
     */
    public static function generateOrderNumber(): string
    {
        $date = date('Ymd');
        $prefix = "ORD-{$date}-";

        $lastOrder = static::where('order_number', 'like', "{$prefix}%")
            ->orderByRaw("CAST(SUBSTRING(order_number, -4) AS UNSIGNED) DESC")
            ->first();

        if ($lastOrder) {
            $lastSeq = (int) substr($lastOrder->order_number, -4);
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        return $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
    }

    // ─── Relationships ─────────────────────────────────────────

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function biteshipDelivery()
    {
        return $this->hasOne(Delivery::class);
    }

    // ─── Order Type Helpers ────────────────────────────────────

    /**
     * Apakah pesanan ini adalah dine-in (datang langsung)?
     */
    public function isDineIn(): bool
    {
        return $this->order_type === 'dine_in';
    }

    /**
     * Apakah pesanan ini adalah online (delivery/pickup)?
     */
    public function isOnline(): bool
    {
        return in_array($this->order_type, ['delivery', 'pickup']);
    }

    /**
     * Apakah pesanan ini wajib bayar di muka?
     * Online orders (delivery/pickup) wajib bayar dulu.
     */
    public function requiresUpfrontPayment(): bool
    {
        return $this->isOnline();
    }

    /**
     * Apakah pesanan ini adalah delivery?
     */
    public function isDelivery(): bool
    {
        return $this->order_type === 'delivery';
    }

    /**
     * Apakah pesanan ini adalah pickup?
     */
    public function isPickup(): bool
    {
        return $this->order_type === 'pickup';
    }

    // ─── Status Helpers (State Machine) ───────────────────────

    public function isWaitingPayment(): bool
    {
        return $this->order_status === 'pending_payment';
    }

    public function isConfirmed(): bool
    {
        return $this->order_status === 'confirmed';
    }

    public function isCooking(): bool
    {
        return $this->order_status === 'cooking';
    }

    public function isCompleted(): bool
    {
        return $this->order_status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->order_status === 'cancelled';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isExpired(): bool
    {
        return $this->payment_status === 'expired';
    }

    /**
     * Apakah pesanan ini masih bisa di-cancel?
     */
    public function isCancellable(): bool
    {
        return in_array($this->order_status, ['pending_payment', 'confirmed', 'cooking']);
    }

    /**
     * Label status order yang human-readable (Bahasa Indonesia)
     */
    public function getOrderStatusLabelAttribute(): string
    {
        return match ($this->order_status) {
            'pending_payment' => 'Menunggu Pembayaran',
            'confirmed' => 'Terkonfirmasi',
            'cooking' => 'Diproses',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->order_status),
        };
    }

    /**
     * Label status payment yang human-readable
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'unpaid' => 'Belum Bayar',
            'pending' => 'Menunggu',
            'paid' => 'Lunas',
            'expired' => 'Kedaluwarsa',
            'cancelled' => 'Dibatalkan',
            'refunded' => 'Refund',
            default => ucfirst($this->payment_status),
        };
    }

    /**
     * CSS color class untuk badge status order
     */
    public function getOrderStatusColorAttribute(): string
    {
        return match ($this->order_status) {
            'pending_payment' => '#f39c12',
            'confirmed' => '#3498db',
            'cooking' => '#e67e22',
            'completed' => '#27ae60',
            'cancelled' => '#e74c3c',
            default => '#95a5a6',
        };
    }

    /**
     * CSS color class untuk badge status payment
     */
    public function getPaymentStatusColorAttribute(): string
    {
        return match ($this->payment_status) {
            'unpaid' => '#e74c3c',
            'pending' => '#f39c12',
            'paid' => '#27ae60',
            'expired' => '#95a5a6',
            'cancelled' => '#e74c3c',
            'refunded' => '#3498db',
            default => '#95a5a6',
        };
    }

    // ─── Auto Progression Logic ────────────────────────────────

    /**
     * Otomatis mengubah status pesanan berdasarkan timer di Settings.
     * Dipanggil di controller saat pesanan sedang dilihat/di-query.
     */
    public static function autoProgressAllActive(): void
    {
        $now = now();
        $autoProcessSeconds = (int) \App\Models\Setting::get('auto_process_seconds', 0);
        $autoCompleteSeconds = (int) \App\Models\Setting::get('auto_complete_seconds', 0);

        // 1. Tangani Expired Payments
        // Yang payment status = pending, dan sudah lewat expires_at
        $expiredOrders = static::where('payment_status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->whereNotIn('order_status', ['completed', 'cancelled'])
            ->get();
            
        foreach ($expiredOrders as $order) {
            $order->payment_status = 'expired';
            $order->order_status = 'cancelled';
            $order->updated_at = $now;
            $order->save();
            
            // Kembalikan stok yang dipesan
            try {
                $stockService = new \App\Services\StockDeductionService();
                $stockService->restoreOrderStock($order);
            } catch (\Exception $e) {
                // Log exception if needed
            }
        }

        // 2. Auto Process (Lunas -> Diproses)
        if ($autoProcessSeconds > 0) {
            // Ambil order yang sudah lunas dan statusnya confirmed, pending, atau new
            $ordersToProcess = static::whereIn('order_status', ['confirmed', 'pending', 'new'])
                ->where(function($q) {
                    $q->where('payment_status', 'paid')->orWhere('order_type', 'dine_in');
                })
                ->get();

            foreach ($ordersToProcess as $order) {
                if ($order->updated_at <= $now->copy()->subSeconds($autoProcessSeconds)) {
                    $order->order_status = 'cooking';
                    $order->updated_at = $now;
                    $order->save();
                }
            }
        }

        // 3. Auto Complete (Diproses -> Selesai)
        if ($autoCompleteSeconds > 0) {
            $ordersToComplete = static::whereIn('order_status', ['cooking', 'served', 'ready'])->get();

            foreach ($ordersToComplete as $order) {
                if ($order->updated_at <= $now->copy()->subSeconds($autoCompleteSeconds)) {
                    $order->order_status = 'completed';
                    $order->updated_at = $now;
                    $order->save();
                }
            }
        }
    }

    // ─── Query Scopes ──────────────────────────────────────────

    /**
     * Filter pesanan dine-in saja.
     */
    public function scopeDineIn($query)
    {
        return $query->where('order_type', 'dine_in');
    }

    /**
     * Filter pesanan online (delivery + pickup).
     */
    public function scopeOnline($query)
    {
        return $query->whereIn('order_type', ['delivery', 'pickup']);
    }

    /**
     * Filter pesanan yang sedang aktif (belum selesai / belum cancelled).
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('order_status', ['completed', 'cancelled'])
                     ->whereNotIn('payment_status', ['expired', 'cancelled']);
    }

    /**
     * Filter pesanan online yang sudah expired (belum bayar melewati batas waktu).
     */
    public function scopeExpiredPending($query)
    {
        return $query->where('payment_status', 'pending')
                     ->whereIn('order_type', ['delivery', 'pickup'])
                     ->where('order_status', 'pending_payment')
                     ->whereNotNull('expires_at')
                     ->where('expires_at', '<=', now());
    }

    /**
     * Berapa menit sejak order masuk.
     */
    public function getWaitingMinutesAttribute(): int
    {
        return $this->created_at->diffInMinutes(now());
    }

    /**
     * Hitung grand total (termasuk delivery fee, setelah diskon).
     */
    public function getGrandTotalAttribute(): float
    {
        return ($this->total_amount + $this->delivery_fee) - $this->discount_amount;
    }

    /**
     * Sisa waktu pembayaran dalam detik (untuk countdown timer).
     * Mengembalikan 0 jika sudah expired atau bukan online order.
     */
    public function getRemainingPaymentSecondsAttribute(): int
    {
        if (!$this->expires_at || $this->payment_status !== 'pending') {
            return 0;
        }

        $remaining = now()->diffInSeconds($this->expires_at, false);
        return max(0, (int) $remaining);
    }
}
