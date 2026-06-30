@extends('client.layouts.app')
@section('title', 'Checkout Online - ' . $storeName)
@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    /* ═══════════════════════════════════════════════════
       CHECKOUT - Premium E-Commerce Design System
       ═══════════════════════════════════════════════════ */
    .checkout-page {
        max-width: 1100px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 30px;
        align-items: start;
    }
    @media (max-width: 900px) {
        .checkout-page {
            grid-template-columns: 1fr;
        }
    }

    /* ── Step Progress Bar ── */
    .checkout-steps {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
        margin-bottom: 35px;
        padding: 0 20px;
    }
    .step {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .step-circle {
        width: 36px; height: 36px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 0.85rem;
        border: 2px solid #333;
        color: #555;
        background: var(--bg-dark);
        transition: all 0.4s ease;
        flex-shrink: 0;
    }
    .step.active .step-circle {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
        box-shadow: 0 0 20px rgba(232, 48, 74, 0.4);
    }
    .step.done .step-circle {
        background: #2ecc71;
        border-color: #2ecc71;
        color: #fff;
    }
    .step-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #555;
        transition: color 0.3s;
    }
    .step.active .step-label, .step.done .step-label { color: #fff; }
    .step-line {
        width: 60px; height: 2px;
        background: #333;
        margin: 0 5px;
        border-radius: 2px;
        position: relative;
        overflow: hidden;
    }
    .step-line.done { background: #2ecc71; }
    @media (max-width: 600px) {
        .step-label { display: none; }
        .step-line { width: 30px; }
    }

    /* ── Card Sections ── */
    .co-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 28px;
        margin-bottom: 24px;
        border: 1px solid rgba(255,255,255,0.06);
        position: relative;
        overflow: hidden;
        transition: border-color 0.3s ease;
    }
    .co-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary), #ff6b81, var(--primary));
        background-size: 200% auto;
        animation: shimmer 3s linear infinite;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .co-card:hover::before { opacity: 1; }
    .co-card:hover { border-color: rgba(255,255,255,0.1); }
    @keyframes shimmer {
        to { background-position: 200% center; }
    }

    .co-card-title {
        font-size: 1.05rem;
        font-weight: 800;
        margin: 0 0 20px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: -0.3px;
    }
    .co-card-title .card-icon {
        width: 34px; height: 34px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .icon-user { background: rgba(108, 92, 231, 0.15); color: #a29bfe; }
    .icon-loc { background: rgba(0, 206, 209, 0.15); color: #00cec9; }
    .icon-pay { background: rgba(253, 203, 110, 0.15); color: #fdcb6e; }
    .icon-summary { background: rgba(232, 48, 74, 0.15); color: var(--primary); }

    /* ── Mode Toggle ── */
    .mode-toggle {
        display: flex;
        background: rgba(255,255,255,0.05);
        border-radius: 12px;
        padding: 5px;
        margin-bottom: 24px;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .mt-btn {
        flex: 1;
        text-align: center;
        padding: 10px 15px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: 0.3s;
        color: #888;
        border: none;
        background: transparent;
    }
    .mt-btn.active {
        color: #fff;
    }
    .mt-btn.delivery.active {
        background: linear-gradient(135deg, rgba(21,101,192,0.8), rgba(100,181,246,0.6));
        box-shadow: 0 4px 15px rgba(21,101,192,0.4);
    }
    .mt-btn.pickup.active {
        background: linear-gradient(135deg, rgba(230,81,0,0.8), rgba(255,183,77,0.6));
        box-shadow: 0 4px 15px rgba(230,81,0,0.4);
    }
    .mt-btn i { margin-right: 5px; }

    /* ── Error Display ── */
    .errors-box {
        background: rgba(214, 48, 49, 0.08);
        color: #ff7675;
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 24px;
        font-size: 0.88rem;
        border: 1px solid rgba(214, 48, 49, 0.2);
        backdrop-filter: blur(5px);
    }
    .errors-box ul { margin: 0; padding-left: 18px; }
    .errors-box li { margin-bottom: 4px; }

    /* ── Form Inputs ── */
    .fg { margin-bottom: 16px; }
    .fg label {
        display: block;
        font-weight: 600;
        margin-bottom: 7px;
        font-size: 0.85rem;
        color: #999;
        letter-spacing: 0.2px;
    }
    .fg input, .fg select, .fg textarea {
        width: 100%;
        padding: 13px 16px;
        border: 1px solid #2a2a2a;
        border-radius: 12px;
        font-family: inherit;
        font-size: 0.95rem;
        transition: all 0.25s ease;
        background: rgba(255,255,255,0.03);
        color: #fff;
    }
    .fg input:focus, .fg select:focus, .fg textarea:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(232, 48, 74, 0.1);
        background: rgba(255,255,255,0.05);
    }
    .fg select option {
        background: #1a1a1a;
        color: #fff;
    }
    .fg textarea {
        resize: vertical;
        min-height: 75px;
    }
    .fg-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }
    @media (max-width: 500px) {
        .fg-row { grid-template-columns: 1fr; }
    }

    /* ── Map Section ── */
    .map-wrapper {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #2a2a2a;
        margin-bottom: 14px;
        position: relative;
    }
    .map-wrapper #map {
        height: 280px;
        z-index: 1;
    }
    .btn-gps {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, rgba(0,206,209,0.12), rgba(0,206,209,0.04));
        color: #00cec9;
        border: 1px solid rgba(0,206,209,0.25);
        border-radius: 12px;
        cursor: pointer;
        font-family: inherit;
        font-weight: 700;
        font-size: 0.9rem;
        transition: all 0.25s ease;
        margin-bottom: 16px;
    }
    .btn-gps:hover {
        background: linear-gradient(135deg, rgba(0,206,209,0.2), rgba(0,206,209,0.08));
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(0,206,209,0.15);
    }
    .btn-gps i { font-size: 1rem; }

    /* ── Delivery Status Box ── */
    #delivery-status-box {
        margin-top: 14px;
        padding: 14px 18px;
        border-radius: 12px;
        display: none;
        font-size: 0.88rem;
        font-weight: 600;
        backdrop-filter: blur(5px);
        transition: all 0.3s ease;
    }

    /* ── Payment Method Cards ── */
    .payment-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
    }
    .pay-card {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        border: 1px solid #2a2a2a;
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.25s ease;
        background: rgba(255,255,255,0.02);
        position: relative;
        overflow: hidden;
    }
    .pay-card::after {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0;
        width: 3px;
        background: transparent;
        transition: background 0.3s;
    }
    .pay-card:hover {
        border-color: #444;
        background: rgba(255,255,255,0.04);
        transform: translateX(3px);
    }
    .pay-card.selected {
        border-color: var(--primary);
        background: rgba(232, 48, 74, 0.06);
    }
    .pay-card.selected::after {
        background: var(--primary);
    }
    .pay-card input[type="radio"] {
        accent-color: var(--primary);
        width: 18px; height: 18px;
        flex-shrink: 0;
    }
    .pay-icon {
        width: 42px; height: 42px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .pay-icon.bank { background: rgba(108,92,231,0.12); color: #a29bfe; }
    .pay-icon.ewallet { background: rgba(46,213,115,0.12); color: #2ed573; }
    .pay-icon.qris { background: rgba(253,203,110,0.12); color: #fdcb6e; }
    .pay-info h4 {
        margin: 0;
        font-size: 0.92rem;
        font-weight: 700;
        color: #fff;
    }
    .pay-info p {
        margin: 3px 0 0;
        font-size: 0.78rem;
        color: #777;
    }
    .no-cod {
        margin-top: 14px;
        padding: 10px 14px;
        border-radius: 10px;
        background: rgba(232,48,74,0.06);
        border: 1px solid rgba(232,48,74,0.15);
        font-size: 0.82rem;
        color: #ff7675;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* ═══ RIGHT COLUMN: Sticky Summary ═══ */
    .summary-sticky {
        position: sticky;
        top: 110px;
    }

    .summary-card {
        background: var(--card-bg);
        border-radius: 16px;
        border: 1px solid rgba(255,255,255,0.06);
        overflow: hidden;
    }
    .summary-header {
        padding: 20px 24px 16px;
        border-bottom: 1px solid #222;
    }
    .summary-header h3 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .summary-body { padding: 0; }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 14px 24px;
        border-bottom: 1px solid rgba(255,255,255,0.04);
        transition: background 0.2s;
    }
    .summary-item:hover { background: rgba(255,255,255,0.02); }
    .summary-item:last-child { border-bottom: none; }
    .si-left {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        flex: 1;
    }
    .si-qty {
        background: rgba(232,48,74,0.12);
        color: var(--primary);
        font-weight: 800;
        font-size: 0.8rem;
        min-width: 28px; height: 28px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 8px;
        flex-shrink: 0;
    }
    .si-name {
        font-weight: 600;
        font-size: 0.9rem;
        color: #eee;
        line-height: 1.4;
    }
    .si-custom {
        font-size: 0.78rem;
        color: #666;
        margin-top: 3px;
    }
    .si-price {
        font-weight: 700;
        font-size: 0.9rem;
        color: #ccc;
        white-space: nowrap;
        padding-left: 10px;
    }

    .summary-footer {
        padding: 20px 24px;
        background: rgba(255,255,255,0.02);
        border-top: 1px solid #222;
    }
    .sf-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
        color: #888;
        margin-bottom: 10px;
    }
    .sf-row.total {
        font-size: 1.15rem;
        font-weight: 800;
        color: #fff;
        margin-bottom: 0;
        padding-top: 12px;
        border-top: 1px dashed #333;
    }
    .sf-row.total .sf-val { color: var(--primary); }

    .btn-checkout-submit {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 16px;
        font-size: 1.05rem;
        border-radius: 14px;
        margin-top: 18px;
        cursor: pointer;
        font-weight: 800;
        font-family: inherit;
        background: linear-gradient(135deg, var(--primary), #ff6b81);
        color: white;
        border: none;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .btn-checkout-submit::before {
        content: '';
        position: absolute;
        top: 0; left: -100%; width: 100%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
        transition: left 0.5s ease;
    }
    .btn-checkout-submit:hover::before { left: 100%; }
    .btn-checkout-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(232, 48, 74, 0.35);
    }
    .btn-checkout-submit:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        transform: none !important;
        box-shadow: none !important;
    }

    .secure-notice {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 14px;
        font-size: 0.78rem;
        color: #555;
    }
    .secure-notice i { color: #2ecc71; font-size: 0.85rem; }

    /* ── Pickup Info Banner ── */
    .pickup-banner {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 18px 22px;
        background: linear-gradient(135deg, rgba(230,81,0,0.1), rgba(255,183,77,0.05));
        border: 1px solid rgba(230,81,0,0.2);
        border-radius: 14px;
        margin-top: 8px;
    }
    .pickup-banner .pickup-icon {
        width: 50px; height: 50px;
        border-radius: 14px;
        background: rgba(230,81,0,0.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .pickup-banner h4 {
        margin: 0 0 3px;
        font-size: 0.95rem;
        color: #ffb74d;
    }
    .pickup-banner p {
        margin: 0;
        font-size: 0.82rem;
        color: #cc8a3e;
    }

    /* ── Animation classes ── */
    .co-card { animation: cardSlideIn 0.5s ease forwards; opacity: 0; }
    .co-card:nth-child(1) { animation-delay: 0.1s; }
    .co-card:nth-child(2) { animation-delay: 0.2s; }
    .co-card:nth-child(3) { animation-delay: 0.3s; }
    @keyframes cardSlideIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .summary-card {
        animation: cardSlideIn 0.5s ease forwards;
        animation-delay: 0.15s;
        opacity: 0;
    }
</style>

{{-- ═══ Step Progress ═══ --}}
<div class="checkout-steps">
    <div class="step done">
        <div class="step-circle"><i class="fa-solid fa-check" style="font-size:0.75rem"></i></div>
        <span class="step-label">Keranjang</span>
    </div>
    <div class="step-line done"></div>
    <div class="step active">
        <div class="step-circle">2</div>
        <span class="step-label">Checkout</span>
    </div>
    <div class="step-line"></div>
    <div class="step">
        <div class="step-circle">3</div>
        <span class="step-label">Pembayaran</span>
    </div>
    <div class="step-line"></div>
    <div class="step">
        <div class="step-circle">4</div>
        <span class="step-label">Selesai</span>
    </div>
</div>

<div class="checkout-page">
    {{-- ═══ LEFT COLUMN ═══ --}}
    <div class="checkout-left">
        <form action="{{ route('client.online.checkout.process') }}" method="POST" id="checkout-form">
            @csrf
            <input type="hidden" name="order_type" value="{{ $orderMode }}">

            <div class="mode-toggle">
                <button type="button" class="mt-btn delivery {{ $orderMode === 'delivery' ? 'active' : '' }}" onclick="switchMode('delivery')">
                    <i class="fa-solid fa-motorcycle"></i> Kirim ke Alamat (Delivery)
                </button>
                <button type="button" class="mt-btn pickup {{ $orderMode === 'pickup' ? 'active' : '' }}" onclick="switchMode('pickup')">
                    <i class="fa-solid fa-bag-shopping"></i> Ambil Sendiri (Pickup)
                </button>
            </div>

            @if($errors->any())
                <div class="errors-box">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Card 1: Data Pemesan --}}
            <div class="co-card">
                <h3 class="co-card-title">
                    <span class="card-icon icon-user"><i class="fa-solid fa-user"></i></span>
                    Data Pemesan
                </h3>

                <div class="fg">
                    <label for="customer_name">Nama Lengkap</label>
                    <input type="text" id="customer_name" name="customer_name" required placeholder="Siapa nama penerima?" value="{{ old('customer_name', $user->name ?? '') }}">
                </div>

                <div class="fg-row">
                    <div class="fg">
                        <label for="customer_whatsapp">No. WhatsApp</label>
                        <input type="text" id="customer_whatsapp" name="customer_whatsapp" required placeholder="08xxxxxxxxxx" value="{{ old('customer_whatsapp', $user->phone_number ?? '') }}">
                    </div>
                    <div class="fg">
                        <label for="customer_email">Email <span style="color:#555">(opsional)</span></label>
                        <input type="email" id="customer_email" name="customer_email" placeholder="email@contoh.com" value="{{ old('customer_email', $user->email ?? '') }}">
                    </div>
                </div>
            </div>

            {{-- Card 2: Alamat --}}
            <div class="co-card" id="delivery-card" style="display: {{ $orderMode === 'delivery' ? 'block' : 'none' }}">
                <h3 class="co-card-title">
                    <span class="card-icon icon-loc"><i class="fa-solid fa-location-dot"></i></span>
                    Alamat Pengiriman
                </h3>

                @if(isset($addresses) && count($addresses) > 0)
                <div class="fg">
                    <label for="address_id">Alamat Tersimpan</label>
                    <select name="address_id" id="address_id" onchange="handleAddressChange()">
                        @foreach($addresses as $addr)
                            <option value="{{ $addr->id }}" data-lat="{{ $addr->latitude }}" data-lng="{{ $addr->longitude }}">
                                {{ $addr->label }} — {{ \Illuminate\Support\Str::limit($addr->full_address, 45) }}
                            </option>
                        @endforeach
                        <option value="new">＋ Tandai Lokasi Baru di Peta</option>
                    </select>
                </div>
                @else
                <input type="hidden" name="address_id" id="address_id" value="new">
                @endif

                <div id="new_address_group" style="display: {{ isset($addresses) && count($addresses) > 0 ? 'none' : 'block' }};">
                    <label style="display:block; font-weight:600; color:#999; margin-bottom:8px; font-size:0.85rem;">Tandai Lokasi Anda di Peta</label>
                    <div class="map-wrapper">
                        <div id="map"></div>
                    </div>

                    <button type="button" onclick="getCurrentLocation()" class="btn-gps">
                        <i class="fa-solid fa-location-crosshairs"></i>
                        Deteksi Lokasi Saya Otomatis
                    </button>

                    <div class="fg">
                        <label for="new_address">Detail Alamat & Patokan</label>
                        <textarea id="new_address" name="new_address" placeholder="Contoh: Jl. Merdeka No. 10, RT 03/RW 05 (Pagar hitam sebelah warung)"></textarea>
                    </div>

                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                </div>

                <div id="delivery-status-box"></div>
            </div>

            {{-- Card 2: Pickup --}}
            <div class="co-card" id="pickup-card" style="display: {{ $orderMode === 'pickup' ? 'block' : 'none' }}">
                <h3 class="co-card-title">
                    <span class="card-icon icon-loc"><i class="fa-solid fa-store"></i></span>
                    Pengambilan Pesanan
                </h3>
                <div class="pickup-banner" style="align-items: flex-start;">
                    <div class="pickup-icon">🏪</div>
                    <div>
                        <h4>Ambil di Restoran {{ $storeName }}</h4>
                        <p style="margin-bottom: 5px; color: #fff;">{{ $storeAddress }}</p>
                        <a href="https://maps.google.com/?q={{ urlencode($storeName . ' ' . $storeAddress) }}" target="_blank" style="color: #f39c12; font-size: 0.85rem; text-decoration: none; font-weight: 700; display: inline-block; margin-bottom: 8px;">
                            <i class="fa-solid fa-map-location-dot"></i> Buka di Google Maps
                        </a>
                        <p>Anda akan menerima notifikasi WhatsApp ketika pesanan sudah siap untuk diambil.</p>
                    </div>
                </div>
            </div>



            {{-- Card 3: Metode Pembayaran --}}
            <div class="co-card">
                <h3 class="co-card-title">
                    <span class="card-icon icon-pay"><i class="fa-solid fa-wallet"></i></span>
                    Metode Pembayaran
                </h3>
                
                <div class="payment-grid" style="grid-template-columns: 1fr;">
                    <label class="pay-card selected">
                        <input type="radio" name="payment_method" value="qris_online" checked>
                        <div class="pay-info" style="display:flex; flex-direction:column; gap:2px; flex: 1;">
                            <strong style="color:#fff;"><i class="fa-solid fa-shield-halved" style="color:#2ecc71;"></i> Midtrans Secure Payment</strong>
                            <span style="color:#999; font-size:0.8rem;">GoPay, OVO, Dana, ShopeePay, Virtual Account BCA, BNI, Mandiri, BRI, Kartu Kredit, dll.</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Submit Button (mobile only, visible below 900px) --}}
            <div class="co-card" style="display:none; background:transparent; border:none; padding:0;">
                {{-- handled by sticky summary --}}
            </div>
        </form>
    </div>

    {{-- ═══ RIGHT COLUMN: Sticky Summary ═══ --}}
    <div class="summary-sticky">
        <div class="summary-card">
            <div class="summary-header">
                <h3>
                    <span class="card-icon icon-summary" style="width:30px;height:30px;border-radius:8px;font-size:0.85rem"><i class="fa-solid fa-receipt"></i></span>
                    Ringkasan Pesanan
                </h3>
            </div>
            <div class="summary-body">
                @foreach($cart as $item)
                <div class="summary-item">
                    <div class="si-left">
                        <span class="si-qty">{{ $item['qty'] }}</span>
                        <div>
                            <div class="si-name">{{ $item['menu_name'] }}</div>
                            @if(!empty($item['customization_notes']))
                                <div class="si-custom">
                                    @foreach($item['customization_notes'] as $key => $val)
                                        {{ $key }}: {{ is_array($val) ? implode(', ', $val) : $val }}{{ !$loop->last ? ' · ' : '' }}
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="si-price">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</div>
                </div>
                @endforeach
            </div>
            <div class="summary-footer">
                @if(!session()->has('voucher_promo_id'))
                <div style="margin-bottom: 15px;">
                    <form action="{{ route('client.online.checkout.promo.apply') }}" method="POST" style="display: flex; gap: 8px;">
                        @csrf
                        <input type="text" name="promo_code" placeholder="Punya kode promo?" style="flex: 1; padding: 10px 12px; border: 1px solid #333; border-radius: 8px; background: rgba(255,255,255,0.05); color: #fff; font-size: 0.9rem;" required>
                        <button type="submit" style="padding: 10px 15px; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s;">Terapkan</button>
                    </form>
                </div>
                @else
                <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; background: rgba(232, 48, 74, 0.1); padding: 10px 15px; border-radius: 8px; border: 1px dashed var(--primary);">
                    <div style="color: var(--primary); font-weight: 600;"><i class="fa-solid fa-ticket"></i> Promo {{ session('voucher_code') }} Aktif</div>
                    <form action="{{ route('client.online.checkout.promo.remove') }}" method="POST">
                        @csrf
                        <button type="submit" style="background: transparent; color: #ff7675; border: none; font-size: 0.8rem; cursor: pointer; font-weight: 600;"><i class="fa-solid fa-xmark"></i> Hapus</button>
                    </form>
                </div>
                @endif

                <div class="sf-row">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                @if(isset($discountAmt) && $discountAmt > 0)
                <div class="sf-row">
                    <span style="color:var(--primary); font-weight: 600;">Diskon @if(!empty($promoCode))({{ $promoCode }})@endif</span>
                    <span style="color:var(--primary); font-weight: 700;">- Rp {{ number_format($discountAmt, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="sf-row" id="delivery-fee-row" style="display: {{ $orderMode === 'delivery' ? 'flex' : 'none' }}">
                    <span>Ongkos Kirim</span>
                    <span id="delivery-fee-display">Rp {{ number_format($deliveryFee ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="sf-row total">
                    <span>Total</span>
                    @php $displayTotal = max(0, $total - (isset($discountAmt) ? $discountAmt : 0)) + $deliveryFee; @endphp
                    <span class="sf-val" id="grand-total-display">Rp {{ number_format($displayTotal, 0, ',', '.') }}</span>
                </div>

                <button type="submit" form="checkout-form" class="btn-checkout-submit btn-pay-checkout" onclick="this.innerHTML='<i class=\'fa-solid fa-spinner fa-spin\'></i> Memproses...'; this.style.opacity='0.8';">
                    <i class="fa-solid fa-credit-card"></i>
                    Lanjut ke Pembayaran
                </button>

                <div class="secure-notice">
                    <i class="fa-solid fa-shield-check"></i>
                    Transaksi aman & terenkripsi
                </div>
            </div>
        </div>
    </div>
</div>

<script>

var baseTotal = {{ $total }};
var discountAmt = {{ isset($discountAmt) ? $discountAmt : 0 }};
var orderMode = '{{ $orderMode }}';
var storeLat = {{ $storeLat ?? 0 }};
var storeLng = {{ $storeLng ?? 0 }};
var map, marker, routeLine;
var currentFee = 0;
var deliveryConfig = {
    baseFee: {{ \App\Models\Setting::get('delivery_base_fee', 5000) }},
    baseDistance: {{ \App\Models\Setting::get('delivery_base_distance_km', 3) }},
    feePerKm: {{ \App\Models\Setting::get('delivery_fee_per_km', 2000) }},
    maxDistance: {{ \App\Models\Setting::get('delivery_max_distance_km', 20) }},
    markupFee: {{ \App\Models\Setting::get('delivery_markup_fee', 2000) }}
};

function initMap() {
    var mapEl = document.getElementById('map');
    if (!mapEl || typeof L === 'undefined' || map) return;

    var centerLat = storeLat || -6.8797;
    var centerLng = storeLng || 109.1256;

    map = L.map('map', { zoomControl: false }).setView([centerLat, centerLng], 13);
    L.control.zoom({ position: 'topright' }).addTo(map);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OSM'
    }).addTo(map);

    // Store marker (custom)
    var storeIcon = L.divIcon({
        html: '<div style="background:var(--primary);color:#fff;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.9rem;box-shadow:0 2px 10px rgba(232,48,74,0.5);border:2px solid #fff"><i class="fa-solid fa-store"></i></div>',
        className: '',
        iconSize: [32, 32],
        iconAnchor: [16, 16]
    });
    L.marker([storeLat, storeLng], { icon: storeIcon }).addTo(map).bindPopup("<b>" + "{{ $storeName }}" + "</b><br>Lokasi Dapur");

    // User marker (draggable)
    var userIcon = L.divIcon({
        html: '<div style="background:#00cec9;color:#fff;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1rem;box-shadow:0 3px 15px rgba(0,206,209,0.5);border:3px solid #fff;cursor:grab"><i class="fa-solid fa-house"></i></div>',
        className: '',
        iconSize: [36, 36],
        iconAnchor: [18, 18]
    });
    marker = L.marker([centerLat, centerLng], { draggable: true, icon: userIcon }).addTo(map);

    marker.on('dragend', function(e) {
        var position = marker.getLatLng();
        updateLocation(position.lat, position.lng);
        reverseGeocode(position.lat, position.lng);
        drawRoute(position.lat, position.lng);
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateLocation(e.latlng.lat, e.latlng.lng);
        reverseGeocode(e.latlng.lat, e.latlng.lng);
        drawRoute(e.latlng.lat, e.latlng.lng);
    });

    setTimeout(function() { map.invalidateSize(); }, 500);
}

function drawRoute(lat, lng) {
    if (routeLine) map.removeLayer(routeLine);
    routeLine = L.polyline(
        [[storeLat, storeLng], [lat, lng]],
        { color: '#E8304A', weight: 2, dashArray: '8, 8', opacity: 0.6 }
    ).addTo(map);
}

function getCurrentLocation() {
    var btn = document.querySelector('.btn-gps');
    if (btn) {
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mendeteksi lokasi...';
        btn.disabled = true;
    }
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            handleLocationSuccess(position.coords.latitude, position.coords.longitude, btn);
        }, function(error) {
            console.warn("Geolocation failed or blocked, trying IP fallback...", error);
            fetchIPLocation(btn);
        });
    } else {
        console.warn("Browser does not support geolocation, trying IP fallback...");
        fetchIPLocation(btn);
    }
}

function fetchIPLocation(btn) {
    fetch('http://ip-api.com/json/')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                handleLocationSuccess(data.lat, data.lon, btn);
                alert("Lokasi terdeteksi berdasarkan jaringan internet Anda.");
            } else {
                throw new Error("IP API failed");
            }
        })
        .catch(err => {
            alert("Gagal mendeteksi lokasi. Pastikan izin GPS diaktifkan di browser Anda.");
            if (btn) {
                btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Deteksi Lokasi Saya Otomatis';
                btn.disabled = false;
            }
        });
}

function handleLocationSuccess(lat, lng, btn) {
    marker.setLatLng([lat, lng]);
    map.setView([lat, lng], 16);
    updateLocation(lat, lng);
    reverseGeocode(lat, lng);
    drawRoute(lat, lng);
    if (btn) {
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Lokasi terdeteksi!';
        setTimeout(function() {
            btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Deteksi Lokasi Saya Otomatis';
            btn.disabled = false;
        }, 2000);
    }
}

function reverseGeocode(lat, lng) {
    var url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data && data.display_name) {
                document.getElementById('new_address').value = data.display_name;
            }
        })
        .catch(err => console.error("Geocoding failed:", err));
}

function haversineDistance(lat1, lon1, lat2, lon2) {
    function toRad(x) { return x * Math.PI / 180; }
    var R = 6371;
    var dLat = toRad(lat2 - lat1);
    var dLon = toRad(lon2 - lon1);
    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

function updateLocation(lat, lng) {
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
    var distance = haversineDistance(storeLat, storeLng, lat, lng);
    calculateFeeByDistance(distance);
}

function calculateFeeByDistance(distanceKm) {
    var statusBox = document.getElementById('delivery-status-box');
    var btnSubmit = document.querySelector('.btn-pay-checkout');
    
    statusBox.style.display = 'block';
    if (distanceKm > deliveryConfig.maxDistance) {
        statusBox.style.background = 'rgba(214, 48, 49, 0.08)';
        statusBox.style.color = '#ff7675';
        statusBox.style.border = '1px solid rgba(214, 48, 49, 0.2)';
        statusBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Jarak terlalu jauh (' + distanceKm.toFixed(1) + ' km). Maks. ' + deliveryConfig.maxDistance + ' km.';
        btnSubmit.disabled = true;
        currentFee = 0;
        updateTotals();
    } else {
        statusBox.style.background = 'rgba(243, 156, 18, 0.08)';
        statusBox.style.color = '#f39c12';
        statusBox.style.border = '1px solid rgba(243, 156, 18, 0.2)';
        statusBox.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menghitung ongkos kirim...';
        btnSubmit.disabled = true;
        
        var lat = document.getElementById('latitude').value;
        var lng = document.getElementById('longitude').value;
        var token = document.querySelector('input[name="_token"]').value;

        fetch('{{ route("client.online.checkout.biteship-rates") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ lat: lat, lng: lng })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                var extraMsg = (data.message && data.message !== 'Berhasil dihitung.') ? ` <strong>(${data.message})</strong>` : '';
                var providerInfo = data.provider ? ' Kurir: ' + data.provider : '';
                statusBox.style.background = 'rgba(46, 204, 113, 0.08)';
                statusBox.style.color = '#2ecc71';
                statusBox.style.border = '1px solid rgba(46, 204, 113, 0.2)';
                statusBox.innerHTML = '<i class="fa-solid fa-circle-check"></i> Jarak: ' + distanceKm.toFixed(1) + ' km.' + providerInfo + extraMsg;
                btnSubmit.disabled = false;
                currentFee = data.fee;
            } else {
                statusBox.style.background = 'rgba(214, 48, 49, 0.08)';
                statusBox.style.color = '#ff7675';
                statusBox.style.border = '1px solid rgba(214, 48, 49, 0.2)';
                statusBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> ' + data.message;
                btnSubmit.disabled = true;
                currentFee = 0;
            }
            updateTotals();
        })
        .catch(err => {
            console.error('Biteship Error:', err);
            statusBox.style.background = 'rgba(214, 48, 49, 0.08)';
            statusBox.style.color = '#ff7675';
            statusBox.style.border = '1px solid rgba(214, 48, 49, 0.2)';
            statusBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Gagal menghubungi server pengiriman.';
            btnSubmit.disabled = true;
            currentFee = 0;
            updateTotals();
        });
    }
}

function handleAddressChange() {
    if (orderMode !== 'delivery') return;
    var select = document.getElementById('address_id');
    if (!select || select.type === 'hidden') return;
    var newAddressGroup = document.getElementById('new_address_group');

    if (select.value === 'new') {
        newAddressGroup.style.display = 'block';
        document.getElementById('new_address').required = true;
        if (map) {
            setTimeout(function() { map.invalidateSize(); }, 200);
        }
        var lat = document.getElementById('latitude').value;
        var lng = document.getElementById('longitude').value;
        if (lat && lng) {
            updateLocation(lat, lng);
        } else {
            document.getElementById('delivery-status-box').style.display = 'none';
            document.querySelector('.btn-pay-checkout').disabled = true;
        }
    } else {
        newAddressGroup.style.display = 'none';
        document.getElementById('new_address').required = false;
        var selectedOption = select.options[select.selectedIndex];
        var lat = parseFloat(selectedOption.getAttribute('data-lat'));
        var lng = parseFloat(selectedOption.getAttribute('data-lng'));
        
        // Update hidden inputs for calculateFeeByDistance AJAX request
        if (!isNaN(lat)) document.getElementById('latitude').value = lat;
        if (!isNaN(lng)) document.getElementById('longitude').value = lng;
        
        if (!isNaN(lat) && !isNaN(lng) && lat !== 0) {
            var distance = haversineDistance(storeLat, storeLng, lat, lng);
            calculateFeeByDistance(distance);
        } else {
            var statusBox = document.getElementById('delivery-status-box');
            statusBox.style.display = 'block';
            statusBox.style.background = 'rgba(253,203,110,0.08)';
            statusBox.style.color = '#fdcb6e';
            statusBox.style.border = '1px solid rgba(253,203,110,0.2)';
            statusBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Alamat ini belum punya koordinat. Gunakan "Tandai Lokasi Baru di Peta".';
            document.querySelector('.btn-pay-checkout').disabled = true;
            currentFee = 0;
            updateTotals();
        }
    }
}

function updateTotals() {
    var feeDisplay = document.getElementById('delivery-fee-display');
    if (feeDisplay) {
        if (currentFee > 0) {
            feeDisplay.textContent = 'Rp ' + currentFee.toLocaleString('id-ID');
            feeDisplay.style.fontSize = '0.9rem';
        } else {
            feeDisplay.textContent = 'Pilih lokasi untuk hitung otomatis';
            feeDisplay.style.fontSize = '0.8rem';
        }
    }
    var grandDisplay = document.getElementById('grand-total-display');
    if (grandDisplay) {
        var calculatedTotal = Math.max(0, baseTotal - discountAmt);
        if (orderMode === 'pickup') {
            grandDisplay.textContent = 'Rp ' + calculatedTotal.toLocaleString('id-ID');
        } else {
            if (currentFee > 0) {
                grandDisplay.textContent = 'Rp ' + (calculatedTotal + currentFee).toLocaleString('id-ID');
            } else {
                grandDisplay.textContent = 'Rp ' + (calculatedTotal).toLocaleString('id-ID') + ' + Ongkir';
            }
        }
        grandDisplay.style.fontSize = '1.1rem';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var checked = document.querySelector('input[name="payment_method"]:checked');
    if (checked) {
        checked.closest('.pay-card').classList.add('selected');
    }
    if (orderMode === 'delivery') {
        initMap();
        handleAddressChange();
    }
});

function updatePayCardState(radio) {
    document.querySelectorAll('.pay-card').forEach(function(card) {
        card.classList.remove('selected');
    });
    if (radio.checked) {
        radio.closest('.pay-card').classList.add('selected');
    }
}

function switchMode(mode) {
    orderMode = mode;
    document.querySelector('input[name="order_type"]').value = mode;
    
    // Update Toggle UI
    document.querySelector('.mt-btn.delivery').classList.remove('active');
    document.querySelector('.mt-btn.pickup').classList.remove('active');
    document.querySelector('.mt-btn.' + mode).classList.add('active');
    
    // Show/Hide Cards
    if (mode === 'delivery') {
        document.getElementById('delivery-card').style.display = 'block';
        document.getElementById('pickup-card').style.display = 'none';
        document.getElementById('delivery-fee-row').style.display = 'flex';
        initMap();
        handleAddressChange();
    } else {
        document.getElementById('delivery-card').style.display = 'none';
        document.getElementById('pickup-card').style.display = 'block';
        document.getElementById('delivery-fee-row').style.display = 'none';
        currentFee = 0;
        document.querySelector('.btn-pay-checkout').disabled = false;
        updateTotals();
    }
}
</script>
@endsection
