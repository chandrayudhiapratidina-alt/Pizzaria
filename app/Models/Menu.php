<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 'description', 'base_price', 'discount_type', 'discount_value', 'discount_price', 'discount_start', 'discount_end', 'image_url', 'category_id', 'is_available', 'daily_stock',
        'rating_avg',
        'reviews_count',
    ];

    protected $casts = [
        'discount_start' => 'datetime',
        'discount_end' => 'datetime',
        'base_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function customizations()
    {
        return $this->hasMany(Customization::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Relasi BOM: bahan baku yang dibutuhkan untuk membuat menu ini
     */
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'menu_ingredients')
                    ->withPivot('qty_needed')
                    ->withTimestamps();
    }

    /**
     * Mengecek apakah menu ini tersedia berdasarkan:
     * 1. Flag is_available
     * 2. Stok harian (daily_stock)
     * 3. BOM (ketersediaan bahan baku di resep)
     */
    public function checkAvailability(): bool
    {
        if (!$this->is_available) {
            return false;
        }

        if ($this->daily_stock !== null && $this->daily_stock <= 0) {
            return false;
        }

        // Cek bahan baku (BOM)
        if ($this->relationLoaded('ingredients') || $this->ingredients()->exists()) {
            foreach ($this->ingredients as $ingredient) {
                if ($ingredient->stock_qty < $ingredient->pivot->qty_needed) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Mengecek apakah menu sedang dalam periode diskon yang aktif
     */
    public function hasActiveDiscount(): bool
    {
        if ($this->discount_price === null) {
            return false;
        }

        $now = now();

        if ($this->discount_start !== null && $this->discount_start > $now) {
            return false;
        }

        if ($this->discount_end !== null && $this->discount_end < $now) {
            return false;
        }

        return true;
    }

    /**
     * Mendapatkan harga final (harga diskon jika aktif, atau harga dasar)
     */
    public function getFinalPriceAttribute()
    {
        return $this->hasActiveDiscount() ? $this->discount_price : $this->base_price;
    }
}
