@extends('cashier.layouts.app')
@section('title', 'Riwayat Transaksi')
@section('content')
<style>
    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .summary-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 25px;
    }
    .summary-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }
    .summary-label { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 8px; }
    .summary-value { font-size: 1.8rem; font-weight: 800; }
    
    .table-container {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        overflow: hidden;
    }
    .history-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .history-table th {
        background: #f8f9fa;
        padding: 15px 20px;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        border-bottom: 2px solid var(--border-color);
    }
    .history-table td {
        padding: 15px 20px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }
    .history-table tr:hover { background: #fafafa; }
</style>

<div class="history-header">
    <div>
        <h2 style="font-size: 1.4rem; font-weight: 800;">Riwayat Transaksi Hari Ini</h2>
        <p style="color: var(--text-muted); margin-top: 5px;">{{ \Carbon\Carbon::today()->format('l, d F Y') }}</p>
    </div>
</div>

<div class="summary-cards">
    <div class="summary-card">
        <div class="summary-label">Total Transaksi Selesai</div>
        <div class="summary-value">{{ $totalOrders }} <span style="font-size: 1rem; color: var(--text-muted);">pesanan</span></div>
    </div>
    <div class="summary-card">
        <div class="summary-label">Total Pendapatan Hari Ini</div>
        <div class="summary-value" style="color: var(--green);">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
    </div>
</div>

<div class="table-container">
    <table class="history-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Waktu</th>
                <th>Customer</th>
                <th>Tipe Pesanan</th>
                <th>Metode Bayar</th>
                <th style="text-align: right;">Total</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td style="font-weight: 700; color: var(--primary);">#ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td style="color: var(--text-muted);">{{ $order->created_at->format('H:i') }}</td>
                <td style="font-weight: 600;">{{ $order->customer_name }}</td>
                <td>
                    @if($order->order_type === 'delivery')
                        <span style="background: rgba(232, 48, 74, 0.1); color: var(--primary); padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.85rem; border: 1px solid rgba(232, 48, 74, 0.2);">
                            <i class="fa-solid fa-motorcycle"></i> Delivery
                        </span>
                    @elseif($order->order_type === 'pickup')
                        <span style="background: rgba(243, 156, 18, 0.1); color: #f39c12; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.85rem; border: 1px solid rgba(243, 156, 18, 0.2);">
                            <i class="fa-solid fa-box-open"></i> Pick Up
                        </span>
                    @else
                        <span style="background: #f1f2f6; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.85rem; color: #333;">
                            <i class="fa-solid fa-utensils"></i> Meja {{ $order->table ? $order->table->table_number : '-' }}
                        </span>
                    @endif
                </td>
                <td>
                    @php
                        $methodIcons = ['cash' => '💵', 'qris' => '📱', 'debit' => '💳', 'credit' => '💳', 'qris_online' => '📱', 'bank_transfer' => '🏦'];
                    @endphp
                    <span style="font-weight: 600;">{{ $methodIcons[$order->payment_method] ?? '💰' }} {{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'Cash')) }}</span>
                </td>
                <td style="text-align: right; font-weight: 800;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                <td style="text-align: center;">
                    <a href="{{ route('cashier.orders.receipt', $order->id) }}" target="_blank" style="color: var(--green); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                        <i class="fa-solid fa-print"></i> Nota
                    </a>
                </td>
            </tr>
            @endforeach
            
            @if($orders->count() == 0)
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                    <p style="font-weight: 600;">Belum ada transaksi selesai hari ini.</p>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection
