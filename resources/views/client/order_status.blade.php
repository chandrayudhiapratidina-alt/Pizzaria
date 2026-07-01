@extends('client.layouts.app')

@section('title', 'Status Pesanan - ' . $storeName)

@section('content')
<style>
    .receipt-wrapper {
        max-width: 800px;
        margin: 0 auto;
    }
    .status-card {
        background: var(--card-bg);
        padding: 2rem;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        margin-bottom: 20px;
        border: 1px solid #333;
    }
    .order-title {
        text-align: center;
        margin-bottom: 0.5rem;
        font-weight: 800;
        color: #fff;
        font-size: 1.5rem;
    }
    .order-title span { color: var(--primary); }
    
    /* Order Type Badge */
    .order-type-badge {
        text-align: center;
        margin-bottom: 2rem;
    }
    .order-type-badge span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 700;
    }
    .badge-dinein {
        background: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }
    .badge-delivery {
        background: #e3f2fd;
        color: #1565c0;
        border: 1px solid #bbdefb;
    }
    .badge-pickup {
        background: #fff3e0;
        color: #e65100;
        border: 1px solid #ffe0b2;
    }
    
    .status-bar {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        background: rgba(255,255,255,0.02);
        padding: 1.5rem;
        border-radius: 10px;
        border: 1px solid #333;
    }
    .status-col p:first-child { margin: 0; color: var(--gray); font-size: 0.9rem; }
    .status-value { margin: 5px 0 0; font-weight: 700; font-size: 1.2rem; text-transform: uppercase; }
    
    .customer-bar {
        margin-bottom: 2rem;
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
    }
    .customer-bar p { margin: 0; }
    .customer-bar .label { color: var(--gray); font-size: 0.9rem; }
    .customer-bar .value { font-weight: 600; margin-top: 3px; }
    
    .items-title {
        margin-bottom: 1rem;
        color: #fff;
        border-bottom: 2px solid #333;
        padding-bottom: 10px;
        font-size: 1.1rem;
    }
    
    .item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px dashed #333;
    }
    .item-row:last-child { border-bottom: none; }
    
    .total-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1.3rem;
        font-weight: 800;
        border-top: 2px solid #333;
        padding-top: 1rem;
        margin-top: 1rem;
    }
    .total-bar span:last-child { color: var(--primary); }
    
    .payment-info {
        margin-top: 1.5rem;
        background: rgba(46, 204, 113, 0.1);
        color: #2ecc71;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        text-align: center;
        border: 1px solid rgba(46, 204, 113, 0.3);
    }
    
    .pending-info {
        margin-top: 1.5rem;
        text-align: center;
        background: rgba(243, 156, 18, 0.1);
        color: #f39c12;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid rgba(243, 156, 18, 0.3);
    }
    
    .action-buttons {
        margin-top: 2rem;
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
    }
    .btn-action {
        padding: 12px 25px;
        border-radius: 10px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
        font-family: inherit;
        font-size: 1rem;
    }
    .btn-back { background: rgba(255,255,255,0.1); color: #fff; }
    .btn-refresh { background: var(--primary); color: white; }

    /* Address Card */
    .address-card {
        background: rgba(255,255,255,0.02);
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 2rem;
        border: 1px solid #333;
    }
    .address-card .addr-label {
        font-size: 0.85rem;
        color: var(--gray);
        font-weight: 600;
        margin-bottom: 4px;
    }
    .address-card .addr-value {
        font-weight: 600;
        line-height: 1.5;
    }

    /* Stepper */
    .stepper-wrapper {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        position: relative;
        padding: 0 10px;
    }
    .stepper-wrapper::before {
        content: "";
        position: absolute;
        top: 20px;
        left: 10%;
        right: 10%;
        height: 4px;
        background: #333;
        z-index: 1;
    }
    .stepper-wrapper::after {
        content: "";
        position: absolute;
        top: 20px;
        left: 10%;
        height: 4px;
        background: var(--primary);
        z-index: 1;
        transition: width 0.5s ease;
    }
    .step-item {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1; /* Auto distribute width */
    }
    .step-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: var(--bg-dark);
        border: 4px solid #333;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: #777;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }
    .step-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #777;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    /* Active State */
    .step-item.active .step-circle {
        border-color: var(--primary);
        color: var(--primary);
        box-shadow: 0 0 0 5px rgba(192, 10, 39, 0.1);
    }
    .step-item.active .step-title {
        color: var(--primary);
    }
    
    /* Completed State */
    .step-item.completed .step-circle {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }
    .step-item.completed .step-title {
        color: var(--primary);
    }
</style>

<div class="receipt-wrapper">
    <div class="status-card">
        <h1 class="order-title">
            Pesanan <span>#{{ $order->id }}</span>
        </h1>

        {{-- Order Type Badge --}}
        <div class="order-type-badge">
            <span class="badge-dinein">🪑 Dine-In {{ $order->table ? '— Meja ' . $order->table->table_number : '' }}</span>
        </div>

        {{-- ═══════════════════════════════════════════ --}}
        {{-- STEPPER: Konteks berbeda per order type    --}}
        {{-- ═══════════════════════════════════════════ --}}
        @php
            $currentStep = 0;
            $isCompleted = false;

            $steps = ['Pesanan', 'Diproses', 'Selesai'];
            
            if ($order->order_status === 'completed') {
                    $currentStep = count($steps);
                    $isCompleted = true;
                } elseif (in_array($order->order_status, ['cooking', 'served', 'ready'])) {
                    $currentStep = 2;
                } else {
                    $currentStep = 1;
                }

                // Logic Pembatalan Pesanan
                $canCancel = false;
                if (in_array($order->payment_status, ['pending', 'unpaid']) && !in_array($order->order_status, ['completed', 'cancelled'])) {
                    $canCancel = true;
                }
            @endphp
        
        <style>
            .stepper-wrapper::after {
                width: {{ $currentStep <= 1 ? '0%' : min((($currentStep - 1) / (count($steps) - 1) * 100), 100) . '%' }};
            }
        </style>

        <div class="stepper-wrapper">
            @foreach($steps as $index => $stepName)
                @php $stepNum = $index + 1; @endphp
                <div class="step-item {{ $currentStep > $stepNum ? 'completed' : ($currentStep == $stepNum ? 'active' : '') }} {{ $currentStep >= $stepNum ? 'completed' : '' }}">
                    <div class="step-circle">
                        @if($currentStep > $stepNum || ($currentStep == $stepNum && $isCompleted))
                            <i class="fa-solid fa-check"></i>
                        @else
                            {{ $stepNum }}
                        @endif
                    </div>
                    <div class="step-title">{{ $stepName }}</div>
                </div>
            @endforeach
        </div>

        <div class="status-bar" style="justify-content: center; gap: 30px;">
            <div class="status-col" style="text-align: center;">
                <p>Status Pembayaran</p>
                <p class="status-value" style="color: {{ $order->payment_status == 'paid' ? '#2ecc71' : '#e74c3c' }};">
                    {{ $order->payment_status == 'paid' ? '✅ Lunas' : '⏳ Belum Bayar' }}
                </p>
            </div>
            <div class="status-col" style="text-align: center;">
                <p>Status Pesanan</p>
                <p class="status-value" style="color: {{ in_array($order->order_status, ['completed']) ? '#2ecc71' : '#f39c12' }};">
                    @switch($order->order_status)
                        @case('pending_payment') ⏳ Menunggu @break
                        @case('confirmed') 👨‍🍳 Diterima @break
                        @case('cooking') 🔥 Diproses @break
                        @case('completed') 🎉 Selesai @break
                        @case('cancelled') ❌ Dibatalkan @break
                    @endswitch
                </p>
            </div>
            @if(!in_array($order->order_status, ['completed', 'cancelled']))
            <div class="status-col" style="text-align: center;">
                <p>Estimasi Proses</p>
                <p class="status-value" style="color: #f39c12;">
                    @php
                        $autoProcess = (int) \App\Models\Setting::get('auto_process_seconds', 0);
                        $autoComplete = (int) \App\Models\Setting::get('auto_complete_seconds', 0);
                        $totalSeconds = $autoProcess + $autoComplete;
                        
                        if ($totalSeconds > 0) {
                            $minutes = floor($totalSeconds / 60);
                            $secs = $totalSeconds % 60;
                            $estimateText = '';
                            if ($minutes > 0) $estimateText .= $minutes . ' Menit ';
                            if ($secs > 0 || $minutes == 0) $estimateText .= $secs . ' Detik';
                        } else {
                            $estimateText = \App\Models\Setting::get('estimated_time_dinein', '15 - 20 Menit');
                        }
                    @endphp
                    <i class="fa-regular fa-clock"></i> {{ trim($estimateText) }}
                </p>
            </div>
            @endif
        </div>

        {{-- Customer Info --}}
        <div class="customer-bar">
            <div>
                <p class="label">Atas Nama</p>
                <p class="value">{{ $order->customer_name }}</p>
            </div>
            @if($order->table)
            <div>
                <p class="label">Meja</p>
                <p class="value">{{ $order->table->table_number }}</p>
            </div>
            @endif
            @if($order->customer_whatsapp)
            <div>
                <p class="label">WhatsApp</p>
                <p class="value">{{ $order->customer_whatsapp }}</p>
            </div>
            @endif
            @if($order->payment_method)
            <div>
                <p class="label">Metode Bayar</p>
                @php
                    $methodLabels = [
                        'cash' => '💵 Cash', 
                        'qris' => '📱 QRIS', 
                        'qris_online' => '📱 QRIS Online',
                        'bank_transfer' => '🏦 Bank Transfer',
                        'ewallet' => '📲 e-Wallet',
                        'debit' => '💳 Debit', 
                        'credit' => '💳 Kredit',
                    ];
                @endphp
                <p class="value">{{ $methodLabels[$order->payment_method] ?? ucfirst($order->payment_method) }}</p>
            </div>
            @endif
        </div>



        <h3 class="items-title">Rincian Pesanan</h3>
        
        <div style="margin-bottom: 1rem;">
            @foreach($order->items as $item)
            <div class="item-row">
                <div>
                    <p style="margin: 0; font-weight: 600;">{{ $item->qty }}x {{ $item->menu->name ?? 'Menu tidak diketahui' }}</p>
                    @if($item->customization_notes)
                        @php
                            $notes = json_decode($item->customization_notes, true);
                        @endphp
                        @if($notes && is_array($notes))
                        <ul style="margin: 5px 0 0; padding-left: 20px; font-size: 0.85rem; color: var(--gray);">
                            @foreach($notes as $key => $note)
                                @if(is_array($note))
                                    <li><strong>{{ $key }}:</strong> {{ implode(', ', $note) }}</li>
                                @else
                                    <li><strong>{{ $key }}:</strong> {{ $note }}</li>
                                @endif
                            @endforeach
                        </ul>
                        @endif
                    @endif
                </div>
                <div style="font-weight: 600; white-space: nowrap;">
                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                </div>
            </div>
            @endforeach
        </div>

        <div class="total-bar">
            <span>Total</span>
            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
        
        {{-- Payment Status Messages --}}
        @if($order->payment_status === 'paid')
        <div class="payment-info">
            ✅ Pembayaran telah diterima. Terima kasih!
        </div>
        @elseif(in_array($order->payment_status, ['expired', 'cancelled']))
        <div class="pending-info" style="background: rgba(231, 76, 60, 0.1); color: #e74c3c; border-color: rgba(231, 76, 60, 0.3);">
            ❌ Pesanan telah dibatalkan atau waktu pembayaran telah habis.
        </div>
        @else
            <div class="pending-info" style="background: rgba(46, 204, 113, 0.1); color: #2ecc71; border-color: rgba(46, 204, 113, 0.3);">
                <p style="margin: 0; font-weight: 600;">
                    💳 Pembayaran dilakukan di kasir setelah pesanan Anda disajikan. 
                    Tunjukkan nomor pesanan <strong style="color: #fff;">#{{ $order->id }}</strong> ke kasir.
                </p>
            </div>
        @endif
        
        <div class="action-buttons">
            @if(in_array($order->payment_status, ['pending', 'unpaid']) && !in_array($order->order_status, ['completed', 'cancelled']))
                <a href="{{ route('client.guest.catalog') }}" class="btn-action" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                    ➕ Tambah Pesanan
                </a>
            @endif
            <a href="{{ route('client.guest.catalog') }}" class="btn-action btn-back" style="background: rgba(46, 204, 113, 0.1); color: #2ecc71; border: 1px solid rgba(46, 204, 113, 0.3);">
                📋 Lihat Menu
            </a>
            @if($order->payment_status === 'paid')
                <a href="{{ route('client.guest.orders.receipt', $order->order_number) }}" class="btn-action" style="background: #27ae60; color: white; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.4);">
                    📄 Download Struk
                </a>
            @endif
            @if($canCancel)
            <form action="{{ route('client.guest.orders.cancel', $order->id) }}" method="POST" id="cancel-form" style="display: inline;">
                @csrf
                <button type="button" onclick="confirmCancel()" class="btn-action" style="background: rgba(231, 76, 60, 0.1); color: #e74c3c; border: 1px solid rgba(231, 76, 60, 0.3);">
                    ❌ Batalkan Pesanan
                </button>
            </form>
            @endif
        </div>
    </div>
</div>



@if(isset($isCompleted) && !$isCompleted)
<script>
    // AJAX Polling setiap 5 detik untuk mengecek perubahan status
    setInterval(function() {
        fetch('{{ route('client.guest.api.orders.status', $order->id) }}')
            .then(res => res.json())
            .then(data => {
                if (data.order_status !== '{{ $order->order_status }}' || data.payment_status !== '{{ $order->payment_status }}') {
                    // Jika ada perubahan, refresh halaman agar UI terupdate
                    window.location.reload();
                }
            })
            .catch(err => console.error('Polling error:', err));
    }, 5000);
</script>
@endif

@if($canCancel)
<script>
    function confirmCancel() {
        if(confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) {
            document.getElementById('cancel-form').submit();
        }
    }
</script>
@endif



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var currentStatus = '{{ $order->order_status }}';
            var currentPaymentStatus = '{{ $order->payment_status }}';
            var isCompleted = ['completed', 'cancelled'].includes(currentStatus);
            
            if (!isCompleted) {
                setInterval(function() {
                    fetch('{{ route('client.guest.api.orders.status', $order->id) }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.order_status !== currentStatus || data.payment_status !== currentPaymentStatus) {
                            window.location.reload();
                        }
                    })
                    .catch(error => console.error('Error fetching order status:', error));
                }, 5000); // Cek setiap 5 detik
            }
        });
    </script>
@endsection
