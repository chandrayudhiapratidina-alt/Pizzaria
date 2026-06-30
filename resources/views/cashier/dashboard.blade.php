@extends('cashier.layouts.app')
@section('title', 'Antrian Pesanan')
@section('content')
<style>
    .stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 25px;
    }
    .mini-stat {
        background: white;
        padding: 18px 20px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .mini-stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .mini-stat-value { font-size: 1.5rem; font-weight: 800; }
    .mini-stat-label { font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; }
    
    .order-card {
        background: white;
        border-radius: 12px;
        margin-bottom: 20px;
        border: 1px solid var(--border-color);
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        overflow: hidden;
        transition: 0.2s;
    }
    .order-card:hover { box-shadow: 0 4px 15px rgba(0,0,0,0.06); }
    
    .order-stripe {
        height: 4px;
    }
    .stripe-new { background: var(--primary); }
    .stripe-cooking { background: #f39c12; }
    .stripe-ready { background: var(--green); }
    
    .order-body { padding: 20px; }
    
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }
    .order-id { font-size: 1.1rem; font-weight: 800; }
    .order-time { color: var(--text-muted); font-size: 0.85rem; margin-top: 4px; }
    .order-total { font-size: 1.4rem; font-weight: 800; color: var(--primary); }
    
    .tag {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    .tag-new { background: #ffeaa7; color: #d63031; }
    .tag-cooking { background: #fff3cd; color: #856404; }
    .tag-ready { background: #d4edda; color: #155724; }
    .tag-pending { background: #ffeaa7; color: #d63031; }
    .tag-unpaid { background: #ffeaa7; color: #d63031; }
    .tag-paid { background: #d4edda; color: #155724; }
    
    /* Order Type Tags */
    .tag-type-dinein { background: #e8f5e9; color: #2e7d32; }
    .tag-type-delivery { background: #e3f2fd; color: #1565c0; }
    .tag-type-pickup { background: #fff3e0; color: #e65100; }
    
    .table-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, #1e272e, #2d3436);
        color: white;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 800;
        margin-top: 8px;
    }
    
    .customer-row {
        display: flex;
        gap: 20px;
        background: #f8f9fa;
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-size: 0.9rem;
    }
    .customer-row span { font-weight: 600; }
    
    .item-list { list-style: none; padding: 0; margin: 0 0 15px 0; }
    .item-list li {
        padding: 10px 12px;
        background: #f8f9fa;
        margin-bottom: 6px;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .item-name { font-weight: 600; }
    .item-notes { font-size: 0.8rem; color: var(--text-muted); margin-top: 3px; }
    
    .action-bar {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }
    .action-bar select {
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        font-family: inherit;
        font-size: 0.95rem;
        background: white;
        font-weight: 500;
    }
    .action-bar label {
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--text-muted);
        text-transform: uppercase;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        border: 2px dashed var(--border-color);
    }
</style>

<div class="stats-row" style="grid-template-columns: repeat(4, 1fr);">
    <div class="mini-stat">
        <div class="mini-stat-icon" style="background: #ffeaa7; color: #d63031;">
            <i class="fa-solid fa-bell"></i>
        </div>
        <div>
            <div class="mini-stat-value">{{ $stats['unpaid'] }}</div>
            <div class="mini-stat-label">Antrian Belum Bayar</div>
        </div>
    </div>
    <div class="mini-stat">
        <div class="mini-stat-icon" style="background: #e3f2fd; color: #1565c0;">
            <i class="fa-solid fa-truck"></i>
        </div>
        <div>
            <div class="mini-stat-value">{{ $stats['online_ready'] }}</div>
            <div class="mini-stat-label">Antrian Online Aktif</div>
        </div>
    </div>
    <div class="mini-stat">
        <div class="mini-stat-icon" style="background: #d4edda; color: #155724;">
            <i class="fa-solid fa-check-double"></i>
        </div>
        <div>
            <div class="mini-stat-value">{{ $stats['completed'] }}</div>
            <div class="mini-stat-label">Selesai Hari Ini</div>
        </div>
    </div>
    <div class="mini-stat">
        <div class="mini-stat-icon" style="background: #e0f7fa; color: #00838f;">
            <i class="fa-solid fa-money-bill-wave"></i>
        </div>
        <div>
            <div class="mini-stat-value" style="font-size: 1.3rem;">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</div>
            <div class="mini-stat-label">Pendapatan Hari Ini</div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- SECTION: Online Orders — Siap Kirim / Pickup              --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
@if(isset($onlineOrders) && $onlineOrders->count() > 0)
<div style="margin-bottom: 30px;">
    <h2 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
        <span style="background: #e3f2fd; color: #1565c0; padding: 6px 12px; border-radius: 8px; font-size: 0.85rem;">
            <i class="fa-solid fa-truck"></i> {{ $onlineOrders->count() }}
        </span>
        Antrian Pesanan Online Aktif
    </h2>

    @foreach($onlineOrders as $order)
    <div class="order-card" style="border-left: 4px solid #1565c0;">
        <div class="order-body">
            <div class="order-header">
                <div>
                    <div class="order-id">
                        {{ $order->order_number ?? '#ORD-'.str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                        <span class="tag" style="background: #e3f2fd; color: #1565c0;">
                            {{ $order->order_status_label }}
                        </span>
                        @if($order->order_type === 'delivery')
                            <span class="tag tag-type-delivery">🚗 Delivery</span>
                        @else
                            <span class="tag tag-type-pickup">🏪 Pickup</span>
                        @endif
                    </div>
                    <div class="order-time"><i class="fa-regular fa-clock"></i> {{ $order->created_at->format('d M Y, H:i') }}</div>
                    @if($order->customer_name)
                    <div style="margin-top: 8px;">
                        <span style="font-weight: 600;">👤 {{ $order->customer_name }}</span>
                        @if($order->customer_whatsapp)
                        <span style="margin-left: 10px;">📱 {{ $order->customer_whatsapp }}</span>
                        @endif
                    </div>
                    @endif
                    @if($order->order_type === 'delivery' && $order->customer_address)
                    <div class="table-badge" style="background: linear-gradient(135deg, #1565c0, #1976d2);">
                        📍 {{ Str::limit($order->customer_address, 60) }}
                    </div>
                    @endif
                </div>
                <div class="order-total">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
            </div>

            <ul class="item-list">
                @foreach($order->items as $item)
                <li>
                    <div class="item-name">{{ $item->qty }}x {{ $item->menu->name ?? 'Unknown' }}</div>
                    <span style="font-weight: 600; white-space: nowrap;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </li>
                @endforeach
            </ul>

            {{-- Action Buttons --}}
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                {{-- One Button to Rule Them All (Sesuai Request: Cukup 1 tombol untuk terima & selesaikan pesanan) --}}
                <form action="{{ route('cashier.orders.status', $order->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_status" value="completed">
                    <input type="hidden" name="payment_status" value="paid">
                    <button type="submit" style="background: var(--green); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; font-family: inherit; display: flex; align-items: center; gap: 8px; font-size: 0.95rem;">
                        <i class="fa-solid fa-check-double"></i> Terima & Selesaikan Pesanan
                    </button>
                </form>

                <a href="{{ route('cashier.orders.receipt', $order->id) }}" target="_blank" style="background: #6c757d; color: white; padding: 12px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 6px; font-size: 0.95rem;">
                    <i class="fa-solid fa-print"></i> Cetak
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- SECTION: Dine-In Orders — Belum Bayar                     --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
@if($orders->count() > 0)
<h2 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
    <span style="background: #ffeaa7; color: #d63031; padding: 6px 12px; border-radius: 8px; font-size: 0.85rem;">
        <i class="fa-solid fa-bell"></i> {{ $orders->count() }}
    </span>
    Dine-In — Antrian Belum Bayar
</h2>
@endif

@if($orders->count() == 0 && (!isset($onlineOrders) || $onlineOrders->count() == 0))
    <div class="empty-state">
        <i class="fa-solid fa-clipboard-list" style="font-size: 3rem; color: #dfe4ea; margin-bottom: 15px;"></i>
        <h3 style="color: var(--text-muted); font-weight: 700;">Belum ada pesanan masuk</h3>
        <p style="color: var(--text-muted);">Pesanan baru dari pelanggan akan muncul di sini secara otomatis.</p>
    </div>
@endif

@foreach($orders as $order)
<div class="order-card">
    <div class="order-stripe stripe-{{ $order->order_status }}"></div>
    <div class="order-body">
        <div class="order-header">
            <div>
                <div class="order-id">
                    #ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                    <span class="tag tag-{{ $order->payment_status }}">{{ $order->payment_status_label }}</span>
                    @if($order->order_type === 'dine_in')
                        <span class="tag tag-type-dinein">🪑 Dine-In</span>
                    @elseif($order->order_type === 'delivery')
                        <span class="tag tag-type-delivery">🚗 Delivery</span>
                    @elseif($order->order_type === 'pickup')
                        <span class="tag tag-type-pickup">🏪 Pickup</span>
                    @endif
                </div>
                <div class="order-time"><i class="fa-regular fa-clock"></i> {{ $order->created_at->format('d M Y, H:i') }}</div>
                @if($order->order_type === 'dine_in')
                <div class="table-badge">
                    🪑 Meja {{ $order->table ? $order->table->table_number : ($order->table_id ?? '-') }}
                </div>
                @elseif($order->order_type === 'delivery')
                <div class="table-badge" style="background: linear-gradient(135deg, #1565c0, #1976d2);">
                    🚗 Delivery {{ $order->customer_address ? '— ' . Str::limit($order->customer_address, 40) : '' }}
                </div>
                @elseif($order->order_type === 'pickup')
                <div class="table-badge" style="background: linear-gradient(135deg, #e65100, #ef6c00);">
                    🏪 Pickup
                </div>
                @endif
            </div>
            <div class="order-total">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
        </div>

        @if($order->customer_name)
        <div class="customer-row">
            <div>👤 <span>{{ $order->customer_name }}</span></div>
            @if($order->customer_whatsapp)
            <div>📱 <span>{{ $order->customer_whatsapp }}</span></div>
            @endif
            @if($order->payment_method)
            <div>💳 <span>{{ ucfirst($order->payment_method) }}</span></div>
            @endif
        </div>
        @endif
        
        <ul class="item-list">
            @foreach($order->items as $item)
            <li>
                <div>
                    <div class="item-name">{{ $item->qty }}x {{ $item->menu->name ?? 'Unknown' }}</div>
                    @if($item->customization_notes)
                        @php $notes = is_string($item->customization_notes) ? json_decode($item->customization_notes, true) : $item->customization_notes; @endphp
                        @if($notes && is_array($notes))
                        <div class="item-notes">
                            @foreach($notes as $key => $val)
                                <strong>{{ $key }}:</strong> {{ is_array($val) ? implode(', ', $val) : $val }} &nbsp;
                            @endforeach
                        </div>
                        @endif
                    @endif
                </div>
                <span style="font-weight: 600; white-space: nowrap;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </li>
            @endforeach
        </ul>
        
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 15px;">
            <div style="flex: 1;">
                @if(!$order->promotion_id && in_array($order->payment_status, ['pending', 'unpaid']))
                <form action="{{ route('cashier.orders.promo', $order->id) }}" method="POST" style="display: flex; gap: 8px; max-width: 300px;">
                    @csrf
                    <input type="text" name="promo_code" placeholder="Kode Promo..." style="flex: 1; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px; font-family: inherit; font-size: 0.9rem; text-transform: uppercase;">
                    <button type="submit" style="background: var(--dark); color: white; border: none; padding: 8px 12px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.85rem;">Terapkan</button>
                </form>
                @elseif($order->promotion_id)
                <div style="background: #e6f4ea; border: 1px solid #c2f0d5; padding: 8px 12px; border-radius: 6px; display: inline-block;">
                    <span style="font-weight: 700; color: #1b7339; font-size: 0.85rem;"><i class="fa-solid fa-ticket"></i> Promo Dipakai: {{ $order->promotion->code ?? 'Unknown' }}</span>
                </div>
                @endif
            </div>

            <div style="text-align: right;">
                @if($order->discount_amount > 0)
                <div style="font-size: 0.9rem; color: var(--red); text-decoration: line-through; margin-bottom: 2px;">
                    Rp {{ number_format($order->items->sum('subtotal'), 0, ',', '.') }}
                </div>
                <div style="font-size: 0.9rem; color: var(--green); font-weight: 700; margin-bottom: 4px;">
                    - Rp {{ number_format($order->discount_amount, 0, ',', '.') }}
                </div>
                @endif
                <div style="font-size: 1.4rem; font-weight: 800; color: var(--primary);">
                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                </div>
            </div>
        </div>
        
        <div class="action-bar" style="justify-content: space-between; align-items: center;">
            @if(in_array($order->payment_status, ['pending', 'unpaid']))
            <div style="font-weight: 700; color: var(--text-muted); font-size: 0.95rem;">
                Status: <span style="color: #d63031; text-transform: uppercase;">Belum Bayar</span>
            </div>
            @else
            <div style="font-weight: 700; color: var(--text-muted); font-size: 0.95rem;">
                Metode Pembayaran: <span style="color: var(--primary); text-transform: uppercase;">{{ $order->payment_method ?? 'CASH' }}</span>
            </div>
            @endif
            
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('cashier.orders.receipt', $order->id) }}" target="_blank" style="background: {{ $order->payment_status == 'paid' ? 'var(--green)' : '#6c757d' }}; color: white; padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-print"></i> {{ $order->payment_status == 'paid' ? 'Cetak Lunas' : 'Cetak Tagihan' }}
                </a>
                
                @if(in_array($order->payment_status, ['pending', 'unpaid']))
                    <button type="button" onclick="openPayModal({{ $order->id }}, {{ $order->grand_total }})" style="background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 6px; cursor: pointer; font-family: inherit;">
                        <i class="fa-solid fa-money-bill-wave"></i> Bayar (Smart POS)
                    </button>
                @else
                    <div style="background: #e6f4ea; color: #1b7339; border: 1px solid #c2f0d5; padding: 10px 20px; border-radius: 8px; font-weight: 700; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-check-double"></i> SUDAH LUNAS
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- Payment Confirmation Modal --}}
<style>
    .modal-overlay {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;
    }
    .modal-overlay.active { display: flex; }
    .modal-content {
        background: white; border-radius: 16px; padding: 30px; width: 400px; max-width: 90vw;
    }
    .modal-content h3 { font-size: 1.1rem; margin-bottom: 20px; }
    .modal-content label { display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.85rem; }
    .modal-content select, .modal-content input[type="text"], .modal-content input[type="number"] {
        width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; margin-bottom: 16px;
    }
    .modal-buttons { display: flex; gap: 10px; justify-content: flex-end; }
    .modal-buttons button {
        padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; border: none; font-family: inherit;
    }
    .btn-cancel { background: #e9ecef; color: var(--text-main); }
    .btn-confirm { background: var(--primary); color: white; }
</style>

<div class="modal-overlay" id="payModal">
    <div class="modal-content">
        <h3 style="border-bottom: 1px solid var(--border-color); padding-bottom: 15px; margin-bottom: 20px;"><i class="fa-solid fa-money-bill-wave"></i> Smart POS - Pembayaran</h3>
        <form id="payForm" method="POST">
            @csrf
            <input type="hidden" name="payment_status" value="paid">
            <input type="hidden" name="order_status" value="completed">
            
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
        
        form.action = '/cashier/orders/' + orderId + '/status';
        
        document.getElementById('modalTotalHidden').value = grandTotal;
        document.getElementById('modalTotalDisplay').textContent = formatRupiah(grandTotal);
        
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
            calculateChange();
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
                
                btnConfirm.disabled = false;
                btnConfirm.style.opacity = '1';
                btnConfirm.style.cursor = 'pointer';
            } else {
                changeAlert.style.background = '#ffebee';
                changeAlert.style.color = '#c62828';
                changeAlert.style.borderColor = '#ffcdd2';
                changeDisplay.textContent = 'Uang tidak cukup!';
                
                btnConfirm.disabled = true;
                btnConfirm.style.opacity = '0.5';
                btnConfirm.style.cursor = 'not-allowed';
            }
        } else {
            changeAlert.style.display = 'none';
            btnConfirm.disabled = true;
            btnConfirm.style.opacity = '0.5';
            btnConfirm.style.cursor = 'not-allowed';
        }
    }
    
    document.getElementById('payModal').addEventListener('click', function(e) {
        if (e.target === this) closePayModal();
    });

    // Auto-refresh mechanism & Sound Notification
    let refreshInterval = 15000; // 15 seconds
    
    @php
        $latestOrder = \App\Models\Order::latest('id')->first();
        $latestOrderId = $latestOrder ? $latestOrder->id : 0;
    @endphp
    
    let currentLatestId = {{ $latestOrderId }};
    let storedLatestId = localStorage.getItem('cashier_latest_order_id');
    
    if (storedLatestId && currentLatestId > parseInt(storedLatestId)) {
        // Ada pesanan baru, mainkan suara notifikasi (Ding)
        let audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
        audio.play().catch(e => console.log("Audio autoplay blocked by browser."));
    }
    
    localStorage.setItem('cashier_latest_order_id', currentLatestId);
    
    // Refresh page periodically
    setTimeout(function() {
        window.location.reload();
    }, refreshInterval);
</script>

@endsection
