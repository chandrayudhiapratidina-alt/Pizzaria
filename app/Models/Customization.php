<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customization extends Model
{
    protected $fillable = [
        'menu_id', 'category_id', 'type', 'name', 'additional_price', 'stock', 'deduct_ingredient_id', 'deduct_qty'
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'deduct_ingredient_id');
    }

    /**
     * Cek apakah kustomisasi ini bisa dipilih (stok bahan bakunya cukup).
     */
    public function isAvailable(): bool
    {
        // Jika tidak memotong stok bahan baku, anggap tersedia
        if (!$this->deduct_ingredient_id) {
            return true;
        }

        // Cek stok bahan baku
        if ($this->ingredient) {
            return $this->ingredient->stock_qty >= $this->deduct_qty;
        }

        return false;
    }

    /**
     * Menghitung ketersediaan stok porsi berdasarkan bahan baku yang terhubung.
     */
    public function getAvailableStockAttribute()
    {
        // Jika tidak memotong stok bahan baku, anggap tersedia tak terbatas (tampilkan string statis atau nilai default)
        if (!$this->deduct_ingredient_id || $this->deduct_qty <= 0) {
            return $this->stock; // gunakan static stock jika tidak terhubung
        }

        if ($this->ingredient) {
            return floor($this->ingredient->stock_qty / $this->deduct_qty);
        }

        return 0;
    }
}
