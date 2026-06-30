@extends('admin.layouts.app')

@section('title', 'Manajemen Pesanan')

@section('content')
<style>
    .orders-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .orders-header h1 {
        font-size: 1.5rem;
        font-weight: 800;
    }
    .filter-bar {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .filter-bar select {
        padding: 8px 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-family: inherit;
        font-size: 0.85rem;
        background: white;
        cursor: pointer;
    }
    .filter-bar .btn-filter {
        padding: 8px 18px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
    }
    .filter-bar .btn-reset {
        padding: 8px 18px;
        background: #e9ecef;
        color: var(--text-main);
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        text-decoration: none;
    }

    .orders-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }
    .orders-table th {
        background: #f8f9fa;
        padding: 14px 16px;
        text-align: left;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border-color);
    }
    .orders-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.9rem;
    }
    .orders-table tr:last-child td {
        border-bottom: none;
    }
    .orders-table tr:hover {
        background: #fafbfc;
    }

    .badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-block;
    }
    /* Badge styles replaced with inline style from model accessor */

    .btn-action {
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        font-family: inherit;
        text-decoration: none;
        display: inline-block;
    }
    .btn-view { background: #e3f2fd; color: #1565c0; }
    .btn-view:hover { background: #bbdefb; }
    .btn-pay { background: #e8f5e9; color: #1b7339; }
    .btn-pay:hover { background: #c8e6c9; }

    .order-items-summary {
        font-size: 0.8rem;
        color: var(--text-muted);
        line-height: 1.5;
    }

    .pagination-wrap {
        display: flex;
        justify-content: center;
        margin-top: 20px;
        gap: 5px;
    }
    .pagination-wrap a, .pagination-wrap span {
        padding: 8px 14px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        text-decoration: none;
        color: var(--text-main);
        font-size: 0.85rem;
        font-weight: 600;
    }
    .pagination-wrap .current {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* Modal for payment confirmation */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    .modal-overlay.active { display: flex; }
    .modal-content {
        background: white;
        border-radius: 16px;
        padding: 30px;
        width: 400px;
        max-width: 90vw;
    }
    .modal-content h3 {
        font-size: 1.1rem;
        margin-bottom: 20px;
    }
    .modal-content label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
        font-size: 0.85rem;
    }
    .modal-content select {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-family: inherit;
        margin-bottom: 16px;
    }
    .modal-buttons {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }
    .modal-buttons button {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        font-family: inherit;
    }
    .btn-cancel { background: #e9ecef; color: var(--text-main); }
    .btn-confirm { background: var(--primary); color: white; }
</style>

<div class="orders-header">
    <h1><i class="fa-solid fa-receipt"></i> Manajemen Pesanan</h1>
</div>

{{-- Filter Bar --}}
<form method="GET" action="{{ route('admin.orders.index') }}" class="filter-bar" style="margin-bottom: 20px;">
    <select name="status">
        <option value="">Semua Status</option>
        <option value="pending_payment" {{ request('status') == 'pending_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Terkonfirmasi</option>
        <option value="cooking" {{ request('status') == 'cooking' ? 'selected' : '' }}>Diproses</option>
        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
    </select>
    <select name="payment">
        <option value="">Semua Pembayaran</option>
        <option value="unpaid" {{ request('payment') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
        <option value="pending" {{ request('payment') == 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="paid" {{ request('payment') == 'paid' ? 'selected' : '' }}>Paid</option>
        <option value="expired" {{ request('payment') == 'expired' ? 'selected' : '' }}>Expired</option>
        <option value="cancelled" {{ request('payment') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        <option value="refunded" {{ request('payment') == 'refunded' ? 'selected' : '' }}>Refunded</option>
    </select>
    <button type="submit" class="btn-filter">Filter</button>
    <a href="{{ route('admin.orders.index') }}" class="btn-reset">Reset</a>
</form>

<table class="orders-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Pelanggan</th>
            <th>Tipe / Meja</th>
            <th>Item</th>
            <th>Total</th>
            <th>Status Pesanan</th>
            <th>Pembayaran</th>
            <th>Waktu</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($orders as $order)
        <tr>
            <td><strong>#{{ $order->id }}</strong></td>
            <td>{{ $order->customer_name ?? 'Guest' }}</td>
            <td>
                @if($order->table_id)
                    <span class="badge" style="background: #e0e7ff; color: #4338ca;">Meja {{ $order->table->table_number }}</span>
                @else
                    <span class="badge" style="background: #e0f2fe; color: #0369a1;"><i class="fa-solid fa-globe"></i> Online</span>
                @endif
            </td>
            <td>
                <div class="order-items-summary">
                    @foreach ($order->items as $item)
                        {{ $item->qty }}x {{ $item->menu->name ?? 'Menu Dihapus' }}<br>
                    @endforeach
                </div>
            </td>
            <td><strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></td>
            <td>
                <span class="badge" style="background-color: {{ $order->order_status_color }}; color: white;">
                    {{ $order->order_status_label }}
                </span>
            </td>
            <td>
                <span class="badge" style="background-color: {{ $order->payment_status_color }}; color: white;">
                    {{ $order->payment_status_label }}
                </span>
            </td>
            <td>{{ $order->created_at->format('d/m H:i') }}</td>
            <td>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn-action btn-view">
                        <i class="fa-solid fa-eye"></i> Detail
                    </a>
                    @if (in_array($order->payment_status, ['pending', 'unpaid']))
                        <button class="btn-action btn-pay" onclick="openPayModal({{ $order->id }}, {{ $order->grand_total }})">
                            <i class="fa-solid fa-check"></i> Bayar
                        </button>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" style="text-align: center; padding: 40px; color: var(--text-muted);">
                <i class="fa-solid fa-inbox" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                Tidak ada pesanan ditemukan.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- Pagination --}}
@if ($orders->hasPages())
<div class="pagination-wrap">
    @if ($orders->onFirstPage())
        <span>&laquo;</span>
    @else
        <a href="{{ $orders->previousPageUrl() }}">&laquo;</a>
    @endif

    @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
        @if ($page == $orders->currentPage())
            <span class="current">{{ $page }}</span>
        @else
            <a href="{{ $url }}">{{ $page }}</a>
        @endif
    @endforeach

    @if ($orders->hasMorePages())
        <a href="{{ $orders->nextPageUrl() }}">&raquo;</a>
    @else
        <span>&raquo;</span>
    @endif
</div>
@endif

{{-- Payment Confirmation Modal --}}
<div class="modal-overlay" id="payModal">
    <div class="modal-content">
        <h3 style="border-bottom: 1px solid var(--border-color); padding-bottom: 15px; margin-bottom: 20px;"><i class="fa-solid fa-money-bill-wave"></i> Smart POS - Pembayaran</h3>
        <form id="payForm" method="POST">
            @csrf
            <input type="hidden" name="payment_status" value="paid">
            
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <div style="font-size: 0.9rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Total Tagihan</div>
                <div id="modalTotalDisplay" style="font-size: 1.8rem; font-weight: 800; color: var(--primary);">Rp 0</div>
                <input type="hidden" id="modalTotalHidden" value="0">
            </div>

            <label>Metode Pembayaran</label>
            <select name="payment_method" id="paymentMethodSelect" onchange="togglePaymentFields()">
                <option value="cash">Uang Tunai (Cash)</option>
                <option value="qris">QRIS / e-Wallet</option>
                <option value="bank_transfer">Transfer Bank</option>
            </select>

            <!-- Input Uang Tunai -->
            <div id="cashFields">
                <label>Uang Diterima (Rp)</label>
                <input type="number" name="cash_tendered" id="cashTendered" class="form-control" placeholder="Ketik nominal tanpa titik" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 10px; font-size: 1.1rem; font-weight: 700;" oninput="calculateChange()">
                
                <div id="changeAlert" style="display: none; background: #e8f5e9; color: #1b7339; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-weight: 700; text-align: center; border: 1px solid #c8e6c9;">
                    Kembalian: <span id="changeDisplay">Rp 0</span>
                </div>
            </div>

            <!-- Input Referensi Transfer/QRIS -->
            <div id="transferFields" style="display: none;">
                <label>Nomor Referensi (Opsional)</label>
                <input type="text" name="payment_reference" class="form-control" placeholder="Contoh: REF123456" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 15px;">
            </div>

            <div class="modal-buttons" style="margin-top: 20px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                <button type="button" class="btn-cancel" onclick="closePayModal()">Batal</button>
                <button type="submit" class="btn-confirm" id="btnConfirmPay">Konfirmasi Bayar</button>
            </div>
        </form>
    </div>
</div>

<script>
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
}

function openPayModal(orderId, grandTotal) {
    const modal = document.getElementById('payModal');
    const form = document.getElementById('payForm');
    
    // Set Action URL
    form.action = '/admin/orders/' + orderId + '/update-payment';
    
    // Set Total
    document.getElementById('modalTotalHidden').value = grandTotal;
    document.getElementById('modalTotalDisplay').textContent = formatRupiah(grandTotal);
    
    // Reset Form
    document.getElementById('paymentMethodSelect').value = 'cash';
    document.getElementById('cashTendered').value = '';
    togglePaymentFields();
    
    modal.classList.add('active');
}

function closePayModal() {
    document.getElementById('payModal').classList.remove('active');
}

function togglePaymentFields() {
    const method = document.getElementById('paymentMethodSelect').value;
    const cashFields = document.getElementById('cashFields');
    const transferFields = document.getElementById('transferFields');
    const btnConfirm = document.getElementById('btnConfirmPay');
    
    if (method === 'cash') {
        cashFields.style.display = 'block';
        transferFields.style.display = 'none';
        calculateChange(); // Validate button state based on cash input
    } else {
        cashFields.style.display = 'none';
        transferFields.style.display = 'block';
        btnConfirm.disabled = false;
        btnConfirm.style.opacity = '1';
    }
}

function calculateChange() {
    const total = parseFloat(document.getElementById('modalTotalHidden').value) || 0;
    const tendered = parseFloat(document.getElementById('cashTendered').value) || 0;
    const changeAlert = document.getElementById('changeAlert');
    const changeDisplay = document.getElementById('changeDisplay');
    const btnConfirm = document.getElementById('btnConfirmPay');
    
    if (tendered > 0) {
        changeAlert.style.display = 'block';
        if (tendered >= total) {
            const change = tendered - total;
            changeAlert.style.background = '#e8f5e9';
            changeAlert.style.color = '#1b7339';
            changeAlert.style.borderColor = '#c8e6c9';
            changeDisplay.textContent = formatRupiah(change);
            
            // Enable submit
            btnConfirm.disabled = false;
            btnConfirm.style.opacity = '1';
            btnConfirm.style.cursor = 'pointer';
        } else {
            changeAlert.style.background = '#ffebee';
            changeAlert.style.color = '#c62828';
            changeAlert.style.borderColor = '#ffcdd2';
            changeDisplay.textContent = 'Uang tidak cukup!';
            
            // Disable submit
            btnConfirm.disabled = true;
            btnConfirm.style.opacity = '0.5';
            btnConfirm.style.cursor = 'not-allowed';
        }
    } else {
        changeAlert.style.display = 'none';
        // Disable submit because cash is empty
        btnConfirm.disabled = true;
        btnConfirm.style.opacity = '0.5';
        btnConfirm.style.cursor = 'not-allowed';
    }
}

// Close modal when clicking overlay
document.getElementById('payModal').addEventListener('click', function(e) {
    if (e.target === this) closePayModal();
});
</script>
@endsection
