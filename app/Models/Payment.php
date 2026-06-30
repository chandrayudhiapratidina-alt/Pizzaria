<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'payment_method', 'payment_channel', 'amount',
        'status', 'transaction_id', 'transaction_time',
        'fraud_status', 'signature_key', 'raw_response',
    ];

    protected function casts(): array
    {
        return [
            'raw_response' => 'array',
            'transaction_time' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Apakah pembayaran ini berhasil (settlement/capture)?
     */
    public function isSettled(): bool
    {
        return in_array($this->status, ['settlement', 'capture']);
    }
}
