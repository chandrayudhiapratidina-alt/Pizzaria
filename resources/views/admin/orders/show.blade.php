@extends('admin.layouts.app')

@section('title', 'Detail Pesanan #' . $order->id)

@section('content')
<style>
    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .detail-header h1 {
        font-size: 1.5rem;
        font-weight: 800;
    }
    .btn-back {
        padding: 8px 18px;
        background: #e9ecef;
        color: var(--text-main);
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        text-decoration: none;
        font-size: 0.9rem;
    }
    .btn-back:hover { background: #dee2e6; }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 25px;
    }
    .info-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        padding: 20px;
    }
    .info-card h3 {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.9rem;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: var(--text-muted); font-weight: 500; }
    .info-value { font-weight: 700; }

    .badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }
    .items-table th {
        background: #f8f9fa;
        padding: 14px 16px;
        text-align: left;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border-color);
    }
    .items-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.9rem;
    }
    .items-table tr:last-child td { border-bottom: none; }

    .customization-notes {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .status-actions {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        padding: 20px;
        margin-top: 20px;
    }
    .status-actions h3 {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 15px;
    }
    .action-forms {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    .action-form {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    .action-form select {
        padding: 8px 12px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-family: inherit;
        font-size: 0.85rem;
    }
    .action-form button {
        padding: 8px 18px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        font-family: inherit;
        font-size: 0.85rem;
    }
    .btn-status { background: #e3f2fd; color: #1565c0; }
    .btn-status:hover { background: #bbdefb; }
    .btn-payment { background: #e8f5e9; color: #1b7339; }
    .btn-payment:hover { background: #c8e6c9; }

    .total-row {
        background: #f8f9fa;
        font-weight: 800;
    }
</style>

<div class="detail-header">
    <h1><i class="fa-solid fa-receipt"></i> Pesanan #{{ $order->id }}</h1>
    <a href="{{ route('admin.orders.index') }}" class="btn-back">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="detail-grid">
    {{-- Order Info --}}
    <div class="info-card">
        <h3><i class="fa-solid fa-circle-info"></i> Informasi Pesanan</h3>
        <div class="info-row">
            <span class="info-label">ID Pesanan</span>
            <span class="info-value">#{{ $order->id }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Pelanggan</span>
            <span class="info-value">{{ $order->customer_name ?? 'Guest' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">WhatsApp</span>
            <span class="info-value">{{ $order->customer_whatsapp ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tipe Pesanan</span>
            <span class="info-value">
                @if($order->table_id)
                    <span class="badge" style="background: #e0e7ff; color: #4338ca;">Makan di Tempat (Dine-in)</span>
                @else
                    <span class="badge" style="background: #e0f2fe; color: #0369a1;">Pesanan Online / Takeaway</span>
                @endif
            </span>
        </div>
        @if($order->table_id)
        <div class="info-row">
            <span class="info-label">Meja</span>
            <span class="info-value">Meja {{ $order->table->table_number ?? '-' }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Waktu Pesanan</span>
            <span class="info-value">{{ $order->created_at->format('d M Y, H:i') }}</span>
        </div>
    </div>

    {{-- Payment Info --}}
    <div class="info-card">
        <h3><i class="fa-solid fa-money-bill-wave"></i> Informasi Pembayaran</h3>
        <div class="info-row">
            <span class="info-label">Total</span>
            <span class="info-value" style="color: var(--primary);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status Pesanan</span>
            <span class="badge" style="background-color: {{ $order->order_status_color }}; color: white;">{{ $order->order_status_label }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status Pembayaran</span>
            <span class="badge" style="background-color: {{ $order->payment_status_color }}; color: white;">{{ $order->payment_status_label }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Metode Pembayaran</span>
            <span class="info-value">{{ $order->payment_method ? strtoupper($order->payment_method) : 'Belum dipilih' }}</span>
        </div>
    </div>
</div>



{{-- Items --}}
<table class="items-table">
    <thead>
        <tr>
            <th>Menu</th>
            <th>Kustomisasi</th>
            <th>Qty</th>
            <th style="text-align: right;">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($order->items as $item)
        <tr>
            <td><strong>{{ $item->menu->name ?? 'Menu Dihapus' }}</strong></td>
            <td>
                @if ($item->customization_notes)
                    @php
                        $notes = is_array($item->customization_notes) ? $item->customization_notes : json_decode($item->customization_notes, true);
                    @endphp
                    @if (is_array($notes))
                        <div class="customization-notes">
                            @foreach ($notes as $key => $value)
                                <strong>{{ $key }}:</strong>
                                @if (is_array($value))
                                    {{ implode(', ', $value) }}
                                @else
                                    {{ $value }}
                                @endif
                                <br>
                            @endforeach
                        </div>
                    @endif
                @else
                    <span style="color: var(--text-muted);">-</span>
                @endif
            </td>
            <td>{{ $item->qty }}</td>
            <td style="text-align: right;"><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3" style="text-align: right;">TOTAL</td>
            <td style="text-align: right; color: var(--primary); font-size: 1.1rem;">
                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
            </td>
        </tr>
    </tbody>
</table>

{{-- Admin Actions --}}
<div class="status-actions">
    <h3><i class="fa-solid fa-sliders"></i> Ubah Status</h3>
    <div class="action-forms">
        {{-- Update Order Status --}}
        <form class="action-form" method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
            @csrf
            <label style="font-weight: 600; font-size: 0.85rem; white-space: nowrap;">Status Pesanan:</label>
            <select name="order_status">
                <option value="pending_payment" {{ $order->order_status == 'pending_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                <option value="confirmed" {{ $order->order_status == 'confirmed' ? 'selected' : '' }}>Terkonfirmasi</option>
                <option value="cooking" {{ $order->order_status == 'cooking' ? 'selected' : '' }}>Diproses</option>
                <option value="completed" {{ $order->order_status == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit" class="btn-status">Update</button>
        </form>

        {{-- Update Payment Status --}}
        @if (in_array($order->payment_status, ['pending', 'unpaid']))
        <form class="action-form" method="POST" action="{{ route('admin.orders.updatePayment', $order) }}">
            @csrf
            <input type="hidden" name="payment_status" value="paid">
            <label style="font-weight: 600; font-size: 0.85rem; white-space: nowrap;">Pembayaran:</label>
            <select name="payment_method">
                <option value="cash">Cash</option>
                <option value="qris">QRIS</option>
            </select>
            <button type="submit" class="btn-payment">
                <i class="fa-solid fa-check"></i> Konfirmasi Bayar
            </button>
        </form>
        @endif
    </div>
</div>
@endsection
