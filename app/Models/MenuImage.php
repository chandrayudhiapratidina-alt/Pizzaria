<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuImage extends Model
{
    protected $fillable = [
        'menu_id', 'image_url', 'is_primary'
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
