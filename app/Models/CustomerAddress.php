<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $fillable = [
        'user_id', 'label', 'recipient_name', 'phone_number',
        'full_address', 'city', 'postal_code', 'notes', 'is_primary',
        'latitude', 'longitude'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
