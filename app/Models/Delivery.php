<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'biteship_order_id', 'biteship_tracking_id',
        'waybill_id', 'courier_company', 'courier_type',
        'driver_name', 'driver_phone', 'driver_photo_url',
        'live_tracking_url', 'status', 'raw_response',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
