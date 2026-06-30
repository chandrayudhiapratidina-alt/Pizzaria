<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_discount',
        'usage_limit',
        'usage_limit_per_user',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
        'is_first_order_only',
        'banner_title',
        'banner_subtitle',
        'background_image',
        'theme_color',
        'icon'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'is_first_order_only' => 'boolean',
        'discount_value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
    ];

    /**
     * Scope: hanya promo yang aktif dan valid
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            });
    }

    /**
     * Cek apakah promo ini valid untuk digunakan secara umum (global)
     */
    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        
        // Cek kuota global (termasuk yang sedang di-reserve)
        if ($this->usage_limit !== null) {
            $totalUsedAndReserved = $this->used_count + $this->redemptions()->where('status', 'reserved')->count();
            if ($totalUsedAndReserved >= $this->usage_limit) return false;
        }
        
        return true;
    }

    /**
     * Cek apakah promo ini valid untuk user tertentu (berdasarkan usage_limit_per_user)
     */
    public function isValidForUser($userId): bool
    {
        if (!$this->isValid()) return false;

        // Cek syarat pesanan pertama (Welcome Promo)
        if ($this->is_first_order_only) {
            $pastOrdersCount = \App\Models\Order::where('user_id', $userId)
                ->whereIn('order_status', ['completed', 'served', 'ready', 'cooking', 'new'])
                ->count();
                
            if ($pastOrdersCount > 0) {
                return false;
            }
        }

        if ($this->usage_limit_per_user !== null) {
            $userUsage = $this->redemptions()
                ->where('user_id', $userId)
                ->whereIn('status', ['reserved', 'applied'])
                ->count();
                
            if ($userUsage >= $this->usage_limit_per_user) {
                return false;
            }
        }

        return true;
    }

    /**
     * Hitung diskon berdasarkan jumlah pesanan
     */
    public function calculateDiscount(float $orderAmount): float
    {
        if ($orderAmount < $this->min_order_amount) {
            return 0;
        }

        if ($this->discount_type === 'fixed') {
            return min($this->discount_value, $orderAmount);
        }

        // percentage
        $discount = ($this->discount_value / 100) * $orderAmount;
        
        if ($this->max_discount !== null) {
            $discount = min($discount, $this->max_discount);
        }

        return round($discount, 2);
    }

    /**
     * Relasi ke orders yang menggunakan promo ini
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relasi ke riwayat penggunaan (redemptions)
     */
    public function redemptions()
    {
        return $this->hasMany(PromotionRedemption::class);
    }
}
