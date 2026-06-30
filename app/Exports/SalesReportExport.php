<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = Order::with(['items', 'promotion'])
            ->where('payment_status', 'paid');

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No. Order',
            'Tanggal',
            'Customer',
            'Total Item',
            'Diskon',
            'Total Pendapatan',
            'Metode Pembayaran',
        ];
    }

    public function map($order): array
    {
        return [
            '#ORD-' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
            $order->created_at->format('Y-m-d H:i'),
            $order->customer_name ?? 'Guest',
            $order->items->sum('qty'),
            $order->discount_amount,
            $order->total_amount,
            strtoupper($order->payment_method),
        ];
    }
}
