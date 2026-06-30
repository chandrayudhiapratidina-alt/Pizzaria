<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'category', 'stock_qty', 'unit', 'minimum_stock_alert'
    ];

    /**
     * Get human readable category label
     */
    public function getCategoryLabelAttribute()
    {
        $labels = [
            'menu_base' => 'Bahan Baku Utama',
            'topping' => 'Bahan Topping',
            'beverage' => 'Bahan Minuman',
            'other' => 'Lainnya',
        ];

        return $labels[$this->category] ?? 'Lainnya';
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_ingredients')
                    ->withPivot('qty_needed')
                    ->withTimestamps();
    }

    public function isLowStock()
    {
        return $this->stock_qty <= $this->minimum_stock_alert;
    }
}
