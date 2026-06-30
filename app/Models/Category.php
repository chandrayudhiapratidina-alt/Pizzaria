<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'icon', 'description', 'sort_order'
    ];

    /**
     * Auto-generate slug from name on create
     */
    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Relasi: satu kategori punya banyak menu
     */
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * Relasi: kustomisasi yang berlaku untuk seluruh menu di kategori ini
     */
    public function customizations()
    {
        return $this->hasMany(Customization::class);
    }
}
