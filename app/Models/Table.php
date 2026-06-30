<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Table extends Model
{
    use HasFactory;

    protected $fillable = ['table_number', 'qr_code_url', 'status'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
