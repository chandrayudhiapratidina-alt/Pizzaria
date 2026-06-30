@extends('client.layouts.app')

@section('title', 'Checkout Dine-In - ' . $storeName)

@section('content')
<style>
    .checkout-page {
        display: flex;
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
        padding-top: 20px;
    }
    .checkout-left { flex: 1; }
    .checkout-right { width: 400px; }
    
    .checkout-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 25px;
        border: 1px solid #333;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    .checkout-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.5rem;
        font-weight: 800;
        margin-top: 0;
        margin-bottom: 25px;
        color: #fff;
        border-bottom: 1px solid #333;
        padding-bottom: 15px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #ccc;
        font-weight: 600;
        font-size: 0.95rem;
    }
    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #444;
        background: rgba(0,0,0,0.5);
        border-radius: 8px;
        color: white;
        font-family: 'Inter', sans-serif;
        transition: border-color 0.3s;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--primary);
    }
    .form-control:disabled {
        background: rgba(255,255,255,0.05);
        color: #888;
        cursor: not-allowed;
    }
    
    .order-item {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #222;
    }
    .order-item img {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
    }
    .item-details { flex: 1; }
    .item-name {
        font-weight: 700;
        margin: 0 0 5px;
        font-size: 1rem;
    }
    .item-notes {
        font-size: 0.8rem;
        color: var(--gray);
        margin: 0 0 5px;
    }
    .item-price-qty {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        color: #ccc;
    }
    .summary-row.total {
        font-size: 1.3rem;
        font-weight: 800;
        color: #fff;
        border-top: 1px solid #333;
        padding-top: 15px;
        margin-top: 10px;
    }
    
    .btn-checkout-submit {
        width: 100%;
        padding: 15px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 800;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }
    .btn-checkout-submit:hover {
        background: #d62c42;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(232, 48, 74, 0.4);
    }
    
    .info-box {
        background: rgba(201, 168, 76, 0.1);
        border: 1px solid rgba(201, 168, 76, 0.3);
        color: var(--gold);
        padding: 15px;
        border-radius: 8px;
        display: flex;
        gap: 15px;
        align-items: flex-start;
        margin-bottom: 25px;
    }
    .info-box i { font-size: 1.5rem; margin-top: 2px; }
    .info-box p { margin: 0; font-size: 0.9rem; line-height: 1.5; }

    @media (max-width: 900px) {
        .checkout-page { flex-direction: column; }
        .checkout-right { width: 100%; }
    }
</style>

<div class="checkout-page">
    <div class="checkout-left">
        <div class="info-box">
            <i class="fa-solid fa-bell-concierge"></i>
            <div>
                <strong>Pesanan Makan di Tempat (Dine-In)</strong>
                <p>Pesanan Anda akan langsung dikirim ke dapur. Anda tidak perlu membayar sekarang. Pembayaran dapat dilakukan nanti di kasir setelah Anda selesai menikmati hidangan.</p>
            </div>
        </div>

        <div class="checkout-card">
            <h2 class="checkout-title">Informasi Pesanan</h2>
            <form action="{{ route('client.guest.checkout') }}" method="POST" id="checkout-form">
                @csrf
                <input type="hidden" name="order_type" value="dine_in">
                
                <div class="form-group">
                    <label>Nomor Meja</label>
                    <input type="text" class="form-control" value="Meja {{ Session::get('table_number') }}" disabled>
                    <small style="color:var(--gray); margin-top:5px; display:block;">Anda sedang memesan untuk meja ini.</small>
                </div>
                
                <div class="form-group">
                    <label>Atas Nama (Opsional)</label>
                    <input type="text" name="customer_name" class="form-control" placeholder="Nama Anda (Untuk dipanggil pelayan jika perlu)" value="{{ auth()->check() ? auth()->user()->name : '' }}">
                </div>
                
            </form>
            
            <form action="{{ route('client.guest.cart.voucher') }}" method="POST" style="margin-top: 30px;">
                @csrf
                <label style="display:block; margin-bottom:8px; color:#ccc; font-weight:600;">Punya Kode Promo?</label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" name="voucher_code" class="form-control" placeholder="Masukkan kode" value="{{ $promoCode }}" {{ $promoCode ? 'disabled' : '' }}>
                    @if(!$promoCode)
                        <button type="submit" class="btn btn-primary">Terapkan</button>
                    @endif
                </div>
                @if(session('voucher_error'))
                    <p style="color: var(--primary); font-size: 0.85rem; margin-top: 5px;">{{ session('voucher_error') }}</p>
                @endif
                @if(session('voucher_success'))
                    <p style="color: #2ecc71; font-size: 0.85rem; margin-top: 5px;">{{ session('voucher_success') }}</p>
                @endif
            </form>
            @if($promoCode)
            <form action="{{ route('client.guest.cart.voucher.remove') }}" method="POST" style="margin-top: 5px;">
                @csrf
                <button type="submit" style="background:none; border:none; color:var(--primary); font-size:0.85rem; cursor:pointer; padding:0;">Hapus Promo</button>
            </form>
            @endif
        </div>
    </div>
    
    <div class="checkout-right">
        <div class="checkout-card">
            <h2 class="checkout-title" style="font-size: 1.3rem;">Ringkasan Pesanan</h2>
            
            <div style="margin-bottom: 20px;">
                @foreach($cart as $item)
                <div class="order-item">
                    <img src="{{ $item['image_url'] ? asset('storage/' . $item['image_url']) : asset('images/default_pizza.jpg') }}" alt="{{ $item['menu_name'] }}">
                    <div class="item-details">
                        <p class="item-name">{{ $item['menu_name'] }}</p>
                        @if(!empty($item['customization_notes']))
                            <p class="item-notes">
                            @foreach($item['customization_notes'] as $key => $note)
                                @if(is_array($note))
                                    {{ $key }}: {{ implode(', ', $note) }}<br>
                                @else
                                    {{ $key }}: {{ $note }}<br>
                                @endif
                            @endforeach
                            </p>
                        @endif
                        <div class="item-price-qty">
                            <span style="color: var(--gray);">{{ $item['qty'] }}x</span>
                            <span>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="summary-row">
                <span>Subtotal</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            
            @if($discountAmt > 0)
            <div class="summary-row" style="color: #2ecc71;">
                <span>Diskon Promo ({{ $promoCode }})</span>
                <span>- Rp {{ number_format($discountAmt, 0, ',', '.') }}</span>
            </div>
            @endif
            
            @php
                $grandTotal = max(0, $total - $discountAmt);
            @endphp
            
            <div class="summary-row total">
                <span>Total Bayar</span>
                <span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
            </div>
            
            <div style="margin-top: 25px;">
                <button type="submit" form="checkout-form" class="btn-checkout-submit" onclick="this.innerHTML='<i class=\'fa-solid fa-spinner fa-spin\'></i> Memproses...'; this.style.opacity='0.8';">
                    <i class="fa-solid fa-fire-burner"></i> Kirim ke Dapur
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
