@extends('client.layouts.app')

@section('title', 'Status Pesanan Online - ' . $storeName)

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
        z-index: 1;
        transition: width 0.5s ease;
        background: var(--primary);
    }
    .step-item {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 33.33%;
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
        color: #9ca3af;
        text-align: center;
        transition: all 0.3s ease;
    }
    .step-item.active .step-circle {
        border-color: var(--primary);
        color: var(--primary);
        box-shadow: 0 0 0 5px rgba(255, 71, 87, 0.1);
        animation: pulse 2s infinite;
    }
    .step-item.active .step-title { color: var(--primary); }
    .step-item.completed .step-circle {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }
    .step-item.completed .step-title { color: var(--primary); }

    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 5px rgba(255, 71, 87, 0.1); }
        50% { box-shadow: 0 0 0 10px rgba(255, 71, 87, 0.05); }
    }

    .status-bar {
        display: flex;
        justify-content: center;
        gap: 40px;
        margin-bottom: 2rem;
        background: rgba(255,255,255,0.02);
        border: 1px solid #333;
        padding: 1.5rem;
        border-radius: 10px;
        flex-wrap: wrap;
    }
    .status-col { text-align: center; }
    .status-col p:first-child { margin: 0; color: var(--gray); font-size: 0.9rem; }
    .status-value { margin: 5px 0 0; font-weight: 700; font-size: 1.1rem; text-transform: uppercase; }
    
    .customer-bar {
        margin-bottom: 2rem;
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
    }
    .customer-bar p { margin: 0; }
    .customer-bar .label { color: var(--gray); font-size: 0.9rem; }
    .customer-bar .value { font-weight: 600; margin-top: 3px; }

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

    .pay-now-btn {
        background: var(--primary);
        color: white;
        border: none;
        font-size: 1.1rem;
        padding: 15px 30px;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 700;
        font-family: inherit;
        transition: all 0.2s;
        margin-bottom: 10px;
    }
    .pay-now-btn:hover {
        opacity: 0.9;
        transform: translateY(-2px);
    }

    .warning-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        padding: 14px 18px;
        border-radius: 10px;
        margin-top: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .warning-box p {
        margin: 0;
        font-size: 0.85rem;
        color: #991b1b;
        font-weight: 600;
    }
</style>

<div class="receipt-wrapper">
    <div class="status-card">
        <h1 class="order-title">
            Pesanan <span>#{{ $order->id }}</span>
        </h1>

        <div class="order-type-badge">
            @if($order->isDelivery())
                <span class="badge-delivery">🚗 Delivery</span>
            @else
                <span class="badge-pickup">🏪 Pickup</span>
            @endif
        </div>

        @php
            $currentStep = 0;
            $isCompleted = false;
            $steps = ['Pembayaran', 'Diproses', $order->isDelivery() ? 'Diserahkan ke Kurir' : 'Selesai'];

            if ($order->payment_status === 'pending') {
                $currentStep = 1;
            } elseif ($order->payment_status === 'paid') {
                if ($order->order_status === 'completed') {
                    $currentStep = 4;
                    $isCompleted = true;
                } else {
                    $currentStep = 2;
                }
            }

            $canCancel = false;
            if (in_array($order->payment_status, ['pending', 'unpaid']) && !in_array($order->order_status, ['completed', 'cancelled'])) {
                $canCancel = true;
            }
        @endphp

        <style>
            .stepper-wrapper::after {
                background: var(--primary);
                width: {{ $currentStep <= 1 ? '0%' : ($currentStep == 2 ? '40%' : '80%') }};
            }
        </style>

        <div class="stepper-wrapper">
            @foreach($steps as $index => $stepName)
                @php $stepNum = $index + 1; @endphp
                <div class="step-item {{ $currentStep > $stepNum ? 'completed' : ($currentStep == $stepNum ? 'active' : '') }}">
                    <div class="step-circle">
                        @if($currentStep > $stepNum)
                            <i class="fa-solid fa-check"></i>
                        @else
                            {{ $stepNum }}
                        @endif
                    </div>
                    <div class="step-title">{{ $stepName }}</div>
                </div>
            @endforeach
        </div>

        <div class="status-bar">
            <div class="status-col">
                <p>Pembayaran</p>
                <p class="status-value" style="color: {{ $order->payment_status == 'paid' ? '#2ecc71' : '#e74c3c' }};">
                    {{ $order->payment_status == 'paid' ? '✅ Lunas' : '⏳ Belum Bayar' }}
                </p>
            </div>
            <div class="status-col">
                <p>Pesanan</p>
                <p class="status-value" style="color: {{ $order->order_status === 'completed' ? '#2ecc71' : '#f39c12' }};">
                    @switch($order->order_status)
                        @case('pending_payment') ⏳ Menunggu @break
                        @case('confirmed') ✅ Terkonfirmasi @break
                        @case('cooking') 🍳 Diproses @break
                        @case('completed') 🎉 {{ $order->isDelivery() ? 'Diserahkan Kurir' : 'Selesai' }} @break
                        @case('cancelled') ❌ Dibatalkan @break
                        @default {{ $order->order_status_label }}
                    @endswitch
                </p>
            </div>
            @if(!in_array($order->order_status, ['completed', 'cancelled']))
            <div class="status-col">
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
                            $estimateText = \App\Models\Setting::get('estimated_time_online', '30 - 45 Menit');
                        }
                    @endphp
                    <i class="fa-regular fa-clock"></i> {{ trim($estimateText) }}
                </p>
            </div>
            @endif
        </div>

        <div class="customer-bar">
            <div>
                <p class="label">Atas Nama</p>
                <p class="value">{{ $order->customer_name }}</p>
            </div>
            <div>
                <p class="label">WhatsApp</p>
                <p class="value">{{ $order->customer_whatsapp }}</p>
            </div>
            @if($order->payment_method)
            <div>
                <p class="label">Metode Bayar</p>
                @php
                    $methodLabels = [
                        'bank_transfer' => '🏦 Bank Transfer',
                        'ewallet' => '📲 e-Wallet',
                        'qris_online' => '📱 QRIS',
                    ];
                @endphp
                <p class="value">{{ $methodLabels[$order->payment_method] ?? ucfirst($order->payment_method) }}</p>
            </div>
            @endif
        </div>

        @if($order->isDelivery() && $order->customer_address)
        <div class="address-card">
            <div class="addr-label">Alamat Pengiriman</div>
            <div class="addr-value">{{ $order->customer_address }}</div>
        </div>

        @elseif($order->order_type === 'pickup')
        <div class="address-card" style="background: rgba(243, 156, 18, 0.05); border-color: rgba(243, 156, 18, 0.2);">
            <div class="addr-label" style="color: #f39c12;">🏪 Lokasi Pengambilan (Toko {{ $storeName }})</div>
            <div class="addr-value">
                {{ $storeAddress }}<br>
                <span style="font-size: 0.85rem; color: var(--gray); font-weight: 500;">Buka: {{ $storeOpeningHours }} WIB | 📞 {{ $storePhone }}</span>
            </div>
            <div style="margin-top: 10px;">
                <a href="https://maps.google.com/?q={{ urlencode($storeName . ' ' . $storeAddress) }}" target="_blank" style="color: #f39c12; font-size: 0.85rem; text-decoration: none; font-weight: 700;">
                    <i class="fa-solid fa-map-location-dot"></i> Buka di Google Maps
                </a>
            </div>
        </div>
        @endif

        <h3 class="items-title">Rincian Pesanan</h3>
        <div style="margin-bottom: 1rem;">
            @foreach($order->items as $item)
            <div class="item-row">
                <div>
                    <p style="margin: 0; font-weight: 600;">{{ $item->qty }}x {{ $item->menu->name ?? 'Menu tidak diketahui' }}</p>
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

        @if($order->payment_status === 'paid')
            <div class="payment-info">
                @if($order->order_status === 'completed')
                    @if($order->isDelivery())
                        🛵 Pesanan telah diserahkan kepada kurir! Silakan tunggu pesanannya tiba.
                    @else
                        🎉 Selesai! Pesanan Anda telah siap dinikmati.
                    @endif
                    
                    @if(Auth::check() && Auth::id() === $order->user_id)
                        @php
                            $itemsCount = $order->items->unique('menu_id')->count();
                            $reviewsCount = $order->reviews ? $order->reviews->where('user_id', Auth::id())->count() : 0;
                            $hasPendingReviews = $reviewsCount < $itemsCount;
                        @endphp
                        @if($hasPendingReviews)
                            <div style="margin-top: 15px;">
                                <a href="{{ route('client.online.orders.reviews.create', $order->id) }}" style="display:inline-block; background: var(--gold); color: #000; padding: 10px 20px; font-weight: 700; border-radius: 8px; text-decoration: none; box-shadow: 0 4px 15px rgba(251, 197, 49, 0.3);">
                                    <i class="fa-solid fa-star"></i> Beri Ulasan Menu
                                </a>
                            </div>
                        @else
                            <div style="margin-top: 15px;">
                                <span style="display:inline-block; background: rgba(255,255,255,0.1); color: var(--gray); padding: 10px 20px; font-weight: 700; border-radius: 8px;">
                                    <i class="fa-solid fa-check"></i> Menu Telah Diulas
                                </span>
                            </div>
                        @endif
                    @endif
                @else
                    ✅ Pembayaran terverifikasi! Pesanan Anda sedang diproses.
                @endif
            </div>
        @elseif(in_array($order->payment_status, ['expired', 'cancelled']))
            <div class="pending-info" style="background: rgba(231, 76, 60, 0.1); color: #e74c3c; border-color: rgba(231, 76, 60, 0.3);">
                ❌ Pesanan telah dibatalkan atau waktu pembayaran telah habis.
            </div>
        @else
            @if(in_array($order->payment_status, ['pending', 'unpaid']))
                <div class="pending-info" style="text-align: center; background: rgba(243, 156, 18, 0.05); padding: 15px; border-radius: 8px; border: 1px dashed rgba(243, 156, 18, 0.5); margin-bottom: 15px;">
                    <p style="margin: 0 0 5px 0; font-weight: 700; color: #fff; font-size: 1.1rem;">Selesaikan Pembayaran Anda</p>
                    <div id="payment-countdown" style="font-size: 1.8rem; font-family: monospace; font-weight: bold; color: #f39c12; letter-spacing: 2px;">
                        --:--
                    </div>
                </div>
            @endif

            @if($order->snap_token && $order->payment_status === 'pending')
                <div class="pending-info" style="text-align: center; background: rgba(255,255,255,0.02); border: 2px dashed rgba(243, 156, 18, 0.5);">
                    <button id="pay-button" class="pay-now-btn">
                        Bayar Sekarang — Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </button>
                    
                    @if(config('app.env') === 'local')
                    <div style="margin-top: 15px;">
                        <form action="{{ route('client.online.orders.simulate-payment', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" style="background: rgba(46, 204, 113, 0.2); color: #2ecc71; border: 1px solid #2ecc71; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer;">
                                <i class="fa-solid fa-wand-magic-sparkles"></i> Simulasi Bayar Lunas (Mode Tugas Kuliah)
                            </button>
                        </form>
                    </div>
                    @endif
                    
                    <div class="warning-box">
                        <span style="font-size: 1.2rem;">⚠️</span>
                        <p>Pesanan hanya akan masuk dapur setelah pembayaran berhasil diverifikasi.</p>
                    </div>
                </div>
            @else
                @if(config('app.env') === 'local')
                    <div class="payment-waiting" style="border: 2px dashed var(--primary); background: rgba(232, 48, 74, 0.05); padding: 20px; text-align: center; border-radius: 12px; margin-top: 15px;">
                        <h3 style="color: #fff; margin-bottom: 15px;">Instruksi Pembayaran (Simulasi Midtrans)</h3>
                        <p style="color: #ccc; font-size: 0.9rem; margin-bottom: 20px;">Karena Anda berada di mode <strong>Development (Tugas Kuliah)</strong> dan Midtrans API gagal terhubung, berikut adalah simulasi pembayarannya:</p>
                        
                        @if($order->payment_method === 'qris_online')
                            <div style="background: #fff; padding: 15px; border-radius: 10px; display: inline-block; margin-bottom: 20px;">
                                <i class="fa-solid fa-qrcode" style="font-size: 80px; color: #333;"></i>
                                <p style="color: #333; font-weight: 700; margin-top: 10px;">SCAN QRIS INI</p>
                            </div>
                        @else
                            <div style="background: rgba(255,255,255,0.05); border: 1px solid #444; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                                <p style="margin: 0; color: #999; font-size: 0.9rem;">Transfer ke Virtual Account BCA:</p>
                                <h2 style="margin: 10px 0; color: #fff; letter-spacing: 2px;">8077-{{ rand(1000, 9999) }}-{{ rand(1000, 9999) }}</h2>
                                <p style="margin: 0; font-weight: 700; color: var(--primary);">Atas Nama: {{ strtoupper($storeName) }} ORDER #{{ $order->id }}</p>
                            </div>
                        @endif
                        
                        <form action="{{ route('client.online.orders.simulate-payment', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" style="background: var(--primary); color: #fff; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 700; cursor: pointer; width: 100%; font-size: 1.1rem; box-shadow: 0 4px 15px rgba(232, 48, 74, 0.3);">
                                <i class="fa-solid fa-check-circle"></i> Saya Sudah Bayar (Simulasi)
                            </button>
                        </form>
                    </div>
                @else
                    <div class="payment-waiting" style="border-color: #7f1d1d; background: rgba(127, 29, 29, 0.1);">
                        <h3 style="color: #fca5a5;">⚠️ Token Pembayaran Gagal Dibuat</h3>
                        <p style="color: #fecaca;">Mohon maaf, terjadi masalah dengan sistem pembayaran. Silakan hubungi kami via WhatsApp untuk bantuan.</p>
                    </div>
                @endif
            @endif
        @endif
        
        <div class="action-buttons">
            <a href="{{ route('client.online.landing') }}" class="btn-action" style="background: rgba(255,255,255,0.1); color: #fff;">
                🏠 Kembali
            </a>
            <a href="{{ route('client.guest.catalog') }}" class="btn-action" style="background: rgba(46, 204, 113, 0.1); color: #2ecc71; border: 1px solid rgba(46, 204, 113, 0.3);">
                📋 Pesan Lagi
            </a>
            @if($order->payment_status === 'paid')
                <a href="{{ route('client.guest.orders.receipt', $order->order_number) }}" class="btn-action" style="background: #27ae60; color: white; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.4);">
                    📄 Download Invoice / Struk
                </a>
            @endif
            @if($canCancel)
            <form action="{{ route('client.online.orders.cancel', $order->id) }}" method="POST" id="cancel-form" style="display: inline;">
                @csrf
                <button type="button" onclick="confirmCancel()" class="btn-action" style="background: rgba(231, 76, 60, 0.1); color: #e74c3c; border: 1px solid rgba(231, 76, 60, 0.3);">
                    ❌ Batalkan Pesanan
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

@if(!$isCompleted)
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

@if($order->snap_token && $order->payment_status === 'pending')
<!-- Midtrans Snap JS -->
@php
    $midtransEnv = \App\Models\Setting::get('midtrans_environment', 'sandbox');
    $clientKey = \App\Models\Setting::get('midtrans_client_key') ?: config('midtrans.client_key');
    $snapUrl = $midtransEnv === 'production' ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp
<script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
<script>
    document.getElementById('pay-button').onclick = function(){
        snap.pay('{{ $order->snap_token }}', {
            onSuccess: function(result){
                fetch('{{ route('client.online.orders.verify', $order->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(result)
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        window.location.reload();
                    }
                });
            },
            onPending: function(result){
                alert("Menunggu pembayaran Anda...");
            },
            onError: function(result){
                alert("Pembayaran gagal! Silakan coba lagi.");
            },
            onClose: function(){
                // User closed popup
            }
        });
    };
</script>
@endif

@if(in_array($order->payment_status, ['pending', 'unpaid']) && $order->expires_at)
<script>
    // Countdown Timer 15 Menit
    var expiresAt = new Date("{{ \Carbon\Carbon::parse($order->expires_at)->toIso8601String() }}").getTime();
    
    var countdownTimer = setInterval(function() {
        var now = new Date().getTime();
        var distance = expiresAt - now;
        
        if (distance < 0) {
            clearInterval(countdownTimer);
            var el = document.getElementById("payment-countdown");
            if (el) {
                el.innerHTML = "KADALUARSA";
                el.style.color = "#e74c3c";
            }
        } else {
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;
            
            var el = document.getElementById("payment-countdown");
            if (el) el.innerHTML = minutes + ":" + seconds;
        }
    }, 1000);
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
