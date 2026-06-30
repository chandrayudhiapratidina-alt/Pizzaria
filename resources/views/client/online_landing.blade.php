@extends('client.layouts.app')
@section('title', 'Pesan Online - ' . $storeName)
@section('content')
<style>
    .online-hero {
        text-align: center;
        padding: 50px 0 40px;
    }
    .online-hero h1 {
        font-family: 'Playfair Display', serif;
        font-size: 3rem;
        font-weight: 900;
        margin: 0 0 15px;
        color: #ffffff;
        letter-spacing: -1px;
    }
    .online-hero h1 span { 
        color: var(--primary); 
        position: relative;
    }
    .online-hero p {
        color: #a0a0a0;
        font-size: 1.15rem;
        margin: 0 0 50px;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }

    .mode-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        max-width: 800px;
        margin: 0 auto;
    }

    @media (max-width: 650px) {
        .mode-cards {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        .online-hero h1 {
            font-size: 2.2rem;
        }
    }

    .mode-card {
        background: linear-gradient(145deg, #222 0%, #111 100%);
        border-radius: 24px;
        text-align: center;
        text-decoration: none;
        color: #ffffff;
        box-shadow: 0 10px 40px rgba(0,0,0,0.4);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        border: 1px solid rgba(255,255,255,0.05);
    }
    .mode-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(232, 48, 74, 0.15);
        border-color: rgba(232, 48, 74, 0.3);
    }
    
    .mode-card-header {
        padding: 40px 30px 20px;
    }

    .mode-icon-wrapper {
        width: 90px;
        height: 90px;
        margin: 0 auto 25px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        transition: all 0.3s ease;
    }
    .delivery .mode-icon-wrapper {
        background: linear-gradient(135deg, rgba(232, 48, 74, 0.2), rgba(232, 48, 74, 0.05));
        color: var(--primary);
        box-shadow: 0 0 20px rgba(232, 48, 74, 0.2);
    }
    .pickup .mode-icon-wrapper {
        background: linear-gradient(135deg, rgba(243, 156, 18, 0.2), rgba(243, 156, 18, 0.05));
        color: #f39c12;
        box-shadow: 0 0 20px rgba(243, 156, 18, 0.2);
    }

    .mode-card:hover .mode-icon-wrapper {
        transform: scale(1.1);
    }

    .mode-card h2 {
        font-family: 'Playfair Display', serif;
        font-size: 1.8rem;
        font-weight: 800;
        margin: 0 0 15px;
    }
    .mode-card p {
        color: #999;
        font-size: 0.95rem;
        line-height: 1.6;
        margin: 0;
        padding: 0 10px;
    }

    .mode-action {
        margin-top: auto;
        padding: 20px;
        background: rgba(255,255,255,0.02);
        border-top: 1px solid rgba(255,255,255,0.05);
        font-weight: 700;
        font-size: 1.05rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: 0.3s ease;
    }
    .delivery .mode-action { color: var(--primary); }
    .pickup .mode-action { color: #f39c12; }
    
    .mode-card:hover .mode-action {
        background: rgba(255,255,255,0.05);
        padding-bottom: 25px; /* Slight bounce effect */
    }

    /* Security Notice */
    .security-notice {
        max-width: 800px;
        margin: 40px auto 0;
        background: linear-gradient(to right, rgba(46, 204, 113, 0.05), transparent);
        padding: 25px 30px;
        border-radius: 16px;
        border-left: 4px solid #2ecc71;
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .security-notice .shield-icon {
        font-size: 2.5rem;
        color: #2ecc71;
        flex-shrink: 0;
    }
    .security-notice h4 {
        margin: 0 0 8px;
        font-size: 1.1rem;
        font-weight: 800;
        color: #ffffff;
    }
    .security-notice p {
        margin: 0;
        font-size: 0.9rem;
        color: #aaa;
        line-height: 1.6;
    }

    /* Or Divider */
    .or-divider {
        text-align: center;
        margin: 40px auto 30px;
        max-width: 800px;
        position: relative;
    }
    .or-divider::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(to right, transparent, #444, transparent);
    }
    .or-divider span {
        background: var(--bg-dark);
        padding: 0 20px;
        position: relative;
        z-index: 1;
        color: #777;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .dine-in-link {
        text-align: center;
        margin-bottom: 50px;
    }
    .dine-in-link a {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #bbb;
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        padding: 12px 25px;
        border-radius: 30px;
        border: 1px solid #444;
        transition: 0.3s;
    }
    .dine-in-link a:hover { 
        color: #fff; 
        border-color: #fff;
        background: rgba(255,255,255,0.05);
    }
</style>


<div class="online-hero">
    <h1>Pesan <span>Online</span></h1>
    <p>Pilih cara Anda menikmati hidangan otentik kami. Kami siap melayani dengan kualitas terbaik.</p>
</div>

<div class="mode-cards">
    {{-- Delivery Card --}}
    <a href="{{ route('client.online.mode', 'delivery') }}" class="mode-card delivery">
        <div class="mode-card-header">
            <div class="mode-icon-wrapper">
                <i class="fa-solid fa-motorcycle"></i>
            </div>
            <h2>Delivery</h2>
            <p>{{ $storeName }} favorit Anda diantar langsung ke depan pintu rumah. Panas, segar, dan bebas repot.</p>
        </div>
        <div class="mode-action">
            Antar ke Alamat Saya <i class="fa-solid fa-arrow-right"></i>
        </div>
    </a>

    {{-- Pickup Card --}}
    <a href="{{ route('client.online.mode', 'pickup') }}" class="mode-card pickup">
        <div class="mode-card-header">
            <div class="mode-icon-wrapper">
                <i class="fa-solid fa-store"></i>
            </div>
            <h2>Pickup</h2>
            <p>Pesan sekarang, lewati antrian, dan ambil pesanan Anda langsung di restoran. Cepat dan praktis.</p>
        </div>
        <div class="mode-action">
            Ambil di Restoran <i class="fa-solid fa-arrow-right"></i>
        </div>
    </a>
</div>

{{-- Security Notice --}}
<div class="security-notice">
    <span class="shield-icon"><i class="fa-solid fa-shield-halved"></i></span>
    <div>
        <h4>Transaksi Aman & Terpercaya</h4>
        <p>Pesanan online wajib diselesaikan terlebih dahulu melalui pembayaran digital (QRIS, e-Wallet, atau Transfer Bank). Pesanan Anda akan langsung diproses oleh koki kami segera setelah pembayaran berhasil diverifikasi.</p>
    </div>
</div>

<div class="or-divider">
    <span>atau</span>
</div>

<div class="dine-in-link">
    <a href="{{ route('client.guest.catalog') }}">
        <i class="fa-solid fa-chair"></i> Datang langsung & scan QR Code di meja
    </a>
</div>

<!-- TESTIMONIALS (AUTO SLIDER) -->
<style>
    .testimonials {
        background: #151515; padding: 60px 0; border-radius: 20px; margin: 40px auto 80px; max-width: 1000px;
        overflow: hidden;
        position: relative;
    }
    .testimonial-slider-container {
        width: 100%; overflow: hidden; position: relative;
        padding: 20px 0;
        mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
        -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
    }
    .testimonial-track {
        display: flex; gap: 30px; width: max-content;
        animation: scroll 30s linear infinite;
    }
    .testimonial-track:hover {
        animation-play-state: paused; /* Berhenti saat kursor diarahkan */
    }
    @keyframes scroll {
        0% { transform: translateX(0); }
        100% { transform: translateX(calc(-50% - 15px)); }
    }
    .testi-card {
        background: #222; padding: 30px; border-radius: 16px; border: 1px solid #333;
        width: 350px; flex-shrink: 0;
        transition: transform 0.3s;
    }
    .testi-card:hover { transform: translateY(-5px); border-color: var(--gold); }
    .stars { color: var(--gold); font-size: 1.1rem; margin-bottom: 15px; }
</style>

<div class="testimonials">
    <div style="text-align: center; margin-bottom: 30px;">
        <h2 style="font-family: 'Playfair Display', serif; font-size: 2.2rem; font-weight: 800; color: #fff;">Suara Pelanggan Kami</h2>
        <p style="color: #a0a0a0; font-size: 1rem;">Lihat apa yang mereka katakan tentang pengalaman bersantap di {{ $storeName }}.</p>
    </div>
    
    <div class="testimonial-slider-container">
        <div class="testimonial-track">
            @php
                $reviewsToDisplay = (isset($latestReviews) && $latestReviews->count() > 0) ? $latestReviews : collect([
                    (object)['rating' => 5, 'comment' => 'Pizza terlezat yang pernah saya coba di kota ini. Crustnya sempurna!', 'guest_name' => 'Budi Pelanggan', 'review_type' => 'general'],
                    (object)['rating' => 4, 'comment' => 'Pengalaman makan yang luar biasa. Bahan premium sungguh membuat perbedaan.', 'guest_name' => 'Sari W.', 'review_type' => 'ambiance'],
                    (object)['rating' => 5, 'comment' => 'Pengiriman cepat dan pizzanya masih panas saat sampai. Sangat direkomendasikan!', 'guest_name' => 'Andi P.', 'review_type' => 'service']
                ]);
                $loopedReviews = $reviewsToDisplay->concat($reviewsToDisplay)->concat($reviewsToDisplay);
            @endphp

            @foreach($loopedReviews as $review)
            <div class="testi-card">
                <div class="stars">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $review->rating) <i class="fa-solid fa-star"></i> @else <i class="fa-regular fa-star"></i> @endif
                    @endfor
                </div>
                <p style="font-style:italic; margin-bottom:15px; color: #fff; line-height: 1.6;">"{{ Str::limit($review->comment ?? 'Sangat memuaskan! Recommended!', 120) }}"</p>
                <strong style="color:var(--gold);">— @if(isset($review->user)) {{ $review->user->name }} @else {{ $review->guest_name ?? 'Pelanggan' }} @endif</strong>
                
                <span style="display:block; font-size:0.8rem; color:var(--gray); margin-top:5px;">
                    @if(isset($review->review_type))
                        @if($review->review_type == 'service') Layanan Restoran @elseif($review->review_type == 'ambiance') Suasana & Tempat @else Pengalaman Umum @endif
                    @endif
                </span>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
