@extends('client.layouts.app')
@section('title', 'Katalog Menu')
@section('content')
<style>
    /* Hero Section */
    .hero-banner {
        width: 100vw;
        position: relative;
        left: 50%;
        right: 50%;
        margin-left: -50vw;
        margin-right: -50vw;
        height: 80vh;
        min-height: 600px;
        background-color: var(--bg-dark);
        display: flex;
        align-items: center;
        margin-top: -100px; /* Offset the container padding */
    }
    .hero-bg {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background-image: url('{{ asset("images/banner_pizza.png") }}');
        background-size: cover;
        background-position: center;
    }
    .hero-overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(105deg, rgba(0,0,0,0.88) 35%, rgba(0,0,0,0.2) 70%, rgba(0,0,0,0.05) 100%);
    }
    .hero-overlay::after {
        content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: radial-gradient(ellipse at center, transparent 50%, rgba(0,0,0,0.4) 100%);
        pointer-events: none;
    }
    .hero-content {
        position: relative;
        z-index: 10;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 5%;
        width: 100%;
    }
    .hero-content h1 {
        font-family: 'Playfair Display', serif;
        font-size: 4.5rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        line-height: 1.1;
        margin-bottom: 20px;
        max-width: 600px;
        text-shadow: 0 2px 20px rgba(0,0,0,0.5);
    }
    .gradient-text {
        background: linear-gradient(45deg, #E8304A, #ff6b6b, #E8304A);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gradientFlow 3s linear infinite;
    }
    @keyframes gradientFlow {
        to { background-position: 200% center; }
    }
    .hero-content p {
        font-size: 1.2rem;
        color: #cccccc;
        max-width: 480px;
        margin-bottom: 40px;
        line-height: 1.6;
    }
    .hero-buttons {
        display: flex;
        gap: 20px;
    }
    .btn-hero-primary {
        background: var(--primary);
        color: white;
        padding: 14px 32px;
        border-radius: 30px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(232, 48, 74, 0.3);
    }
    .btn-hero-primary:hover {
        transform: scale(1.03);
        box-shadow: 0 6px 20px rgba(232, 48, 74, 0.5);
    }
    .btn-hero-secondary {
        background: transparent;
        color: white;
        padding: 14px 32px;
        border-radius: 30px;
        font-weight: 600;
        text-decoration: none;
        border: 2px solid white;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-hero-secondary:hover {
        background: rgba(255,255,255,0.1);
        transform: scale(1.03);
        box-shadow: 0 0 15px rgba(255,255,255,0.2);
    }
    .stats-bar {
        position: absolute;
        bottom: 0; left: 0; width: 100%;
        background: rgba(10, 10, 10, 0.8);
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(255,255,255,0.1);
        padding: 15px 0;
    }
    .stats-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 5%;
        display: flex;
        justify-content: space-around;
        color: var(--gold);
        font-weight: 600;
        font-size: 1.1rem;
    }
    .stat-item {
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
    }
    .stat-item:hover {
        color: #FFD700;
        text-shadow: 0 0 12px rgba(255,215,0,0.6);
    }
    .stat-divider {
        width: 1px;
        background: rgba(255,255,255,0.15);
    }
    .stats-container span { color: white; font-weight: 400; margin-left: 8px; font-size: 0.95rem; transition: color 0.3s ease; }
    .stat-item:hover span { color: #f0f0f0; }

    @media (max-width: 600px) {
        .hero-content h1 {
            font-size: 3rem;
        }
        .hero-buttons {
            flex-direction: column;
            gap: 15px;
        }
        .btn-hero-primary, .btn-hero-secondary {
            width: 100%;
            text-align: center;
            justify-content: center;
        }
        .stats-container {
            flex-direction: row;
            flex-wrap: wrap;
            gap: 10px;
        }
        .stat-divider { display: none; }
    }

    /* Floating Animations */
    .floating-pizza {
        position: absolute;
        right: 15%; top: 20%;
        width: 120px; height: 120px;
        border-radius: 50%;
        border: 2px solid rgba(232, 48, 74, 0.6);
        box-shadow: 0 0 30px rgba(232, 48, 74, 0.4);
        background: rgba(17,17,17,0.5);
        backdrop-filter: blur(5px);
        display: flex; align-items: center; justify-content: center;
        animation: rotateSlow 20s linear infinite;
        overflow: hidden;
    }
    .floating-pizza img {
        width: 100%; height: 100%; object-fit: cover;
    }
    .floating-flame {
        position: absolute;
        right: 35%; bottom: 30%;
        font-size: 3.5rem; color: var(--primary);
        animation: pulseFlame 1.5s ease-in-out infinite alternate;
    }
    @keyframes rotateSlow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    @keyframes pulseFlame {
        0% { transform: scale(1); color: var(--primary); }
        100% { transform: scale(1.2); color: #FF6B35; text-shadow: 0 0 20px rgba(255,107,53,0.6); }
    }

    /* Section Title */
    .section-title {
        text-align: center;
        margin: 80px 0 40px;
    }
    .section-title h2 {
        font-family: 'Playfair Display', serif;
        font-size: 2.8rem;
        margin-bottom: 10px;
        position: relative;
        display: inline-block;
    }
    .section-title h2::after {
        content: ''; position: absolute;
        bottom: -5px; left: 25%; width: 50%; height: 3px;
        background: var(--primary);
    }
    .section-title p { color: var(--gray); font-size: 1.1rem; }

    /* Menu Grid & Cards */
    .menu-filter {
        display: flex; justify-content: center; gap: 15px; margin-bottom: 40px; flex-wrap: wrap;
    }
    .cat-tab {
        padding: 10px 24px;
        border-radius: 30px;
        border: 1px solid #333;
        color: var(--gray);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .cat-tab:hover { border-color: var(--primary); color: white; }
    .cat-tab.active { background: var(--primary); border-color: var(--primary); color: white; }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
    }
    .menu-card {
        background: var(--card-bg);
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #222;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease, border-color 0.3s ease;
        position: relative;
    }
    .menu-card:hover {
        transform: translateY(-8px) scale(1.02);
        border-color: rgba(232, 48, 74, 0.3);
        box-shadow: 0 20px 40px rgba(0,0,0,0.4), 0 0 0 1px rgba(232,48,74,0.3);
    }
    .card-img-container {
        width: 100%; aspect-ratio: 1/1;
        overflow: hidden; position: relative;
    }
    .card-img-container::after {
        content: ''; position: absolute;
        bottom: 0; left: 0; width: 100%; height: 50%;
        background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%);
        pointer-events: none;
    }
    .card-img-container img {
        width: 100%; height: 100%; object-fit: cover;
        transition: transform 0.5s ease;
    }
    .menu-card:hover .card-img-container img { transform: scale(1.08); }
    .card-badge {
        position: absolute; top: 15px; left: 15px;
        background: var(--primary); color: white;
        padding: 4px 12px; border-radius: 4px;
        font-size: 0.75rem; font-weight: bold; letter-spacing: 1px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.5);
        z-index: 2;
    }
    .card-badge.hot-badge::before {
        content: '●';
        margin-right: 5px;
        color: white;
        animation: blink 1s infinite;
    }
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0; }
    }
    .card-content { padding: 20px; position: relative; z-index: 2; }
    .card-title { font-family: 'Playfair Display', serif; font-size: 1.4rem; margin: 0 0 8px; color: white; }
    .card-desc { color: var(--gray); font-size: 0.9rem; line-height: 1.5; margin-bottom: 20px; }
    .card-footer { display: flex; justify-content: space-between; align-items: center; position: relative; height: 35px; }
    .card-price { font-weight: 700; font-size: 1.25rem; color: var(--primary); font-variant-numeric: tabular-nums; }
    .btn-add {
        background: var(--primary); color: white; text-decoration: none;
        padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 0.9rem;
        position: absolute; right: 0;
        transform: translateY(20px); opacity: 0;
        transition: transform 0.25s ease, opacity 0.25s ease;
    }
    .menu-card:hover .btn-add { transform: translateY(0); opacity: 1; }
    .btn-add:hover { background: #ff4757; }

    /* Sold Out Style */
    .sold-out { opacity: 0.6; filter: grayscale(80%); pointer-events: none; }
    .sold-out .btn-add { display: none; }

    /* Filter Form */
    .search-container {
        max-width: 700px;
        margin: 0 auto 40px;
        padding: 0 20px;
    }
    .big-search-bar {
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 50px;
        padding: 8px 10px 8px 25px;
        backdrop-filter: blur(10px);
        transition: all 0.4s ease;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    .big-search-bar:focus-within {
        background: rgba(255, 255, 255, 0.1);
        border-color: var(--primary);
        box-shadow: 0 10px 40px rgba(232, 48, 74, 0.2);
        transform: translateY(-2px);
    }
    .big-search-bar .search-icon {
        color: var(--gray);
        font-size: 1.2rem;
    }
    .big-search-bar input {
        flex: 1;
        background: transparent;
        border: none;
        color: white;
        font-size: 1.1rem;
        padding: 10px 15px;
        outline: none;
        font-family: 'Inter', sans-serif;
    }
    .big-search-bar input::placeholder {
        color: #666;
    }
    .big-search-bar .btn-search {
        background: var(--primary);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 40px;
        font-weight: bold;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .big-search-bar .btn-search:hover {
        background: #ff4757;
        transform: scale(1.05);
    }

    /* Flash Sale */
    .flash-sale {
        background: linear-gradient(135deg, #1a0a0a 0%, #1a1a1a 100%);
        border-left: 3px solid var(--primary);
        border-radius: 16px;
        padding: 40px;
        margin-bottom: 50px;
        position: relative;
    }
    .flash-badge {
        position: absolute;
        top: -15px; right: 25px;
        background: var(--primary);
        color: white;
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: bold;
        box-shadow: 0 0 20px rgba(232, 48, 74, 0.7), 0 0 40px rgba(232, 48, 74, 0.3);
        z-index: 2;
    }
    .countdown {
        display: flex; gap: 10px; margin-top: 15px;
        font-family: monospace;
    }
    .time-box {
        background: rgba(232, 48, 74, 0.2);
        border: 1px solid rgba(232, 48, 74, 0.5);
        color: var(--primary);
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        min-width: 60px;
        font-weight: bold;
        font-size: 1.5rem;
    }
    .time-box span { display: block; font-size: 0.7rem; color: #fff; font-weight: normal; font-family: 'Inter', sans-serif; }
    .flash-title {
        font-family: 'Playfair Display', serif;
        font-size: clamp(2rem, 4vw, 3.5rem);
        margin: 0 0 10px;
        color: #fff;
        text-decoration-line: underline;
        text-decoration-color: var(--primary);
        text-decoration-thickness: 3px;
        text-underline-offset: 8px;
    }

    /* Features Section */
    .features-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px; margin: 60px 0;
    }
    .feature-card {
        background: var(--card-bg); padding: 30px; border-radius: 16px; text-align: center;
        border: 1px solid #222;
    }
    .feature-icon { font-size: 2.5rem; color: var(--primary); margin-bottom: 20px; }

    /* Testimonials Auto-Slider */
    .testimonials {
        background: #151515; padding: 60px 0; border-radius: 20px; margin: 80px 0;
        overflow: hidden; /* Hide horizontal scrollbar */
    }
    .testimonial-slider-container {
        display: flex;
        width: 100%;
        overflow: hidden;
        position: relative;
    }
    .testimonial-slider-container::before,
    .testimonial-slider-container::after {
        content: ""; position: absolute; top: 0; width: 100px; height: 100%; z-index: 2; pointer-events: none;
    }
    .testimonial-slider-container::before {
        left: 0; background: linear-gradient(to right, #151515 0%, transparent 100%);
    }
    .testimonial-slider-container::after {
        right: 0; background: linear-gradient(to left, #151515 0%, transparent 100%);
    }
    .testimonial-track {
        display: flex;
        gap: 30px;
        padding: 20px 0;
        width: max-content;
        animation: scrollSideways 20s linear infinite;
    }
    .testimonial-track:hover {
        animation-play-state: paused;
    }
    @keyframes scrollSideways {
        0% { transform: translateX(0); }
        100% { transform: translateX(calc(-50% - 15px)); }
    }
    .testi-card {
        background: var(--bg-dark); padding: 30px; border-radius: 12px; border: 1px solid #222;
        width: 350px; flex-shrink: 0;
        box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        transition: transform 0.3s;
    }
    .testi-card:hover {
        transform: translateY(-5px);
    }
    .stars { color: var(--gold); margin-bottom: 15px; }
    
    /* CTA Banner */
    .cta-banner {
        background: linear-gradient(135deg, #E8304A, #9b1c2e);
        padding: 60px 5%; border-radius: 20px; text-align: center; color: white;
        margin-bottom: 60px;
    }
    .cta-banner h2 { font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 15px; }

    /* QR Hero */
    .qr-hero {
        background: var(--card-bg);
        border: 1px solid var(--primary);
        border-radius: 16px;
        padding: 40px;
        text-align: center;
        margin-bottom: 40px;
        box-shadow: 0 10px 30px rgba(232, 48, 74, 0.15);
        position: relative;
        overflow: hidden;
    }
    .qr-hero::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 5px;
        background: linear-gradient(90deg, var(--primary), #ff6b6b);
    }
    .qr-hero h2 {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        margin-bottom: 10px;
        color: white;
    }
    .table-badge {
        display: inline-block;
        background: var(--primary);
        color: white;
        padding: 8px 24px;
        border-radius: 30px;
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 15px;
        box-shadow: 0 4px 15px rgba(232, 48, 74, 0.3);
    }
    .qr-hero p {
        color: var(--gray);
        font-size: 1.1rem;
        margin-bottom: 0;
    }
    .active-order-alert {
        margin-top: 20px;
        padding: 15px;
        background: rgba(255, 215, 0, 0.1);
        border: 1px solid var(--gold);
        border-radius: 10px;
        color: var(--gold);
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
    .active-order-alert a {
        background: var(--gold);
        color: black;
        padding: 8px 20px;
        border-radius: 20px;
        text-decoration: none;
        font-weight: bold;
        font-size: 0.9rem;
    }
    
    /* Review Modal Styles */
    .review-modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.8); backdrop-filter: blur(5px);
        display: none; align-items: center; justify-content: center;
        z-index: 9999; opacity: 0; transition: opacity 0.3s ease;
    }
    .review-modal-overlay.active { display: flex; opacity: 1; }
    .review-modal {
        background: var(--bg-dark); border: 1px solid #333;
        border-radius: 20px; padding: 40px; width: 90%; max-width: 500px;
        position: relative; transform: translateY(20px); transition: transform 0.3s ease;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    }
    .review-modal-overlay.active .review-modal { transform: translateY(0); }
    .review-close {
        position: absolute; top: 20px; right: 20px; color: var(--gray);
        font-size: 1.5rem; cursor: pointer; transition: color 0.2s;
    }
    .review-close:hover { color: white; }
    .review-stars {
        display: flex; flex-direction: row-reverse; justify-content: center; gap: 10px; margin: 20px 0;
    }
    .review-stars input { display: none; }
    .review-stars label {
        color: #444; font-size: 2.5rem; cursor: pointer; transition: color 0.2s;
    }
    .review-stars label:hover,
    .review-stars label:hover ~ label,
    .review-stars input:checked ~ label {
        color: var(--gold);
        text-shadow: 0 0 15px rgba(255, 215, 0, 0.4);
    }
    .review-form-group { margin-bottom: 20px; text-align: left; }
    .review-form-group label { display: block; margin-bottom: 8px; color: var(--gray); font-size: 0.9rem; }
    .review-form-group input, .review-form-group select, .review-form-group textarea {
        width: 100%; background: #1a1a1a; border: 1px solid #333; color: white;
        padding: 12px 15px; border-radius: 10px; font-family: 'Inter', sans-serif;
    }
    .review-form-group input:focus, .review-form-group select:focus, .review-form-group textarea:focus {
        outline: none; border-color: var(--primary); background: #222;
    }
</style>

<!-- HERO SECTION -->
@if(Session::has('table_id'))
<div class="qr-hero fade-in-up">
    <h2>Selamat Datang di {{ $storeName }}</h2>
    <div class="table-badge">Meja {{ Session::get('table_number') }}</div>
    <p>Silakan pilih menu Anda, pesanan akan langsung disiapkan dan dihidangkan ke meja ini.</p>
    @if(isset($activeOrder) && $activeOrder)
       <div class="active-order-alert">
           Anda memiliki pesanan aktif di meja ini. Anda bisa menambah menu (Add-on).
           <a href="{{ route('client.guest.orders.show', $activeOrder->id) }}">Lihat Pesanan Aktif</a>
       </div>
    @endif
</div>
@else
<div class="hero-banner fade-in-up">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Always <span class="gradient-text">Fresh</span><br>Ingredients</h1>
        <p>Rasakan kemewahan resep klasik yang dipanggang sempurna dengan penuh dedikasi menggunakan bahan baku organik premium kelas dunia.</p>
        <div class="hero-buttons">
            <a href="#menu-section" class="btn-hero-primary">Explore Menu</a>
            <a href="#why-us" class="btn-hero-secondary"><i class="fa-solid fa-utensils"></i> Kenapa Kami?</a>
        </div>
    </div>
    <!-- Removed floating pizza and flame as requested -->
    
    <div class="stats-bar">
        <div class="stats-container">
            <div class="stat-item"><span class="counter" data-target="{{ $totalMenuItems ?? 500 }}">0</span> <span>Menu Items</span></div>
            <div class="stat-divider"></div>
            <div class="stat-item"><span class="counter" data-target="{{ $averageRating ?? 4.9 }}" data-decimal="true">0</span>★ <span>Rating</span></div>
            <div class="stat-divider"></div>
            <div class="stat-item"><span class="counter" data-target="{{ $yearsOfExcellence ?? 10 }}">0</span>+ <span>Years of Excellence</span></div>
        </div>
    </div>
</div>
@endif

<!-- WELCOME PROMO BANNER -->
@if(isset($showWelcomeBanner) && $showWelcomeBanner && !session()->has('table_id'))
<div id="welcomePromoBanner" class="fade-in-up" style="background: linear-gradient(135deg, var(--primary), #a31023); padding: 50px 30px; border-radius: 20px; margin: 40px auto; text-align: center; color: white; box-shadow: 0 10px 30px rgba(226, 40, 65, 0.3); position: relative; overflow: hidden; max-width: 800px; border: 1px solid rgba(255,255,255,0.1); animation-delay: 0.1s;">
    <button type="button" onclick="document.getElementById('welcomePromoBanner').style.display='none'" style="position: absolute; top: 15px; right: 15px; background: rgba(0,0,0,0.2); border: none; color: white; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;">
        <i class="fa-solid fa-xmark"></i>
    </button>
    <div style="font-size: 3rem; margin-bottom: 15px;">🎉</div>
    <h2 style="font-family: 'Playfair Display', serif; font-size: 2.2rem; font-weight: 800; margin-bottom: 15px; line-height: 1.3;">{{ $welcomePromoTitle }}</h2>
    <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 25px;">{{ $welcomePromoSubtitle }}</p>
    
    <div style="background: white; color: var(--primary); padding: 12px 35px; border-radius: 30px; font-weight: 800; font-size: 1.1rem; display: inline-block; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        <i class="fa-solid fa-ticket" style="margin-right: 8px;"></i> Kode Promo: {{ $welcomePromoCode }}
    </div>
</div>
@endif

<!-- FLASH SALE SECTION -->
@if(isset($activePromo) && !session()->has('table_id'))
@php
    $themeColor = $activePromo->theme_color ?? 'var(--primary)';
    $themeIcon = $activePromo->icon ?? 'fa-bolt';
    $bannerTitle = $activePromo->banner_title ?: 'Gunakan Kode: ' . $activePromo->code;
    $bannerSubtitle = $activePromo->banner_subtitle ?: $activePromo->description;
    
    $bgStyle = "";
    if($activePromo->background_image) {
        $bgStyle = "background-image: linear-gradient(to right, rgba(17,17,17,0.95) 0%, rgba(17,17,17,0.85) 45%, rgba(17,17,17,0.2) 100%), url('".$activePromo->background_image."'); background-size: cover; background-position: center right; border-color: ".$themeColor."; box-shadow: 0 0 20px ".$themeColor."40;";
    } else {
        $bgStyle = "border-color: ".$themeColor."; box-shadow: 0 0 20px ".$themeColor."40;";
    }
@endphp
<div class="flash-sale fade-in-up" style="margin-top: 60px; {{ $bgStyle }}">
    <div class="flash-badge" style="background: {{ $themeColor }}; color: white; border: none; box-shadow: 0 0 15px {{ $themeColor }}80;"><i class="fa-solid {{ $themeIcon }}"></i> SPECIAL PROMO</div>
    <div style="display: flex; flex-wrap: wrap; gap: 30px; align-items: center;">
        <div style="flex: 1.5; min-width: 300px;">
            <h2 class="flash-title" style="color: {{ $themeColor }}; text-shadow: 0 2px 10px rgba(0,0,0,0.5); font-size: clamp(2rem, 4vw, 3.2rem); line-height: 1.2; margin-bottom: 10px; font-weight: 800;">{{ $bannerTitle }}</h2>
            <p style="color: #e0e0e0; font-size: 1.1rem; margin-bottom: 20px; max-width: 90%; line-height: 1.5;">{{ $bannerSubtitle }}</p>
            <p style="color: {{ $themeColor }}; font-weight: bold; background: rgba(0,0,0,0.5); padding: 5px 10px; border-radius: 5px; display: inline-block;">
                Diskon {{ $activePromo->discount_type == 'percentage' ? round($activePromo->discount_value) . '%' : 'Rp ' . number_format($activePromo->discount_value, 0, ',', '.') }}
                @if($activePromo->min_order_amount > 0)
                (Min. Belanja: Rp {{ number_format($activePromo->min_order_amount, 0, ',', '.') }})
                @endif
            </p>
            @if($activePromo->expires_at)
            <div class="countdown" id="countdownTimer" data-expires="{{ $activePromo->expires_at->format('Y-m-d\TH:i:s') }}">
                <div class="time-box" id="cd-days" style="border-color: {{ $themeColor }};">00<span style="color: {{ $themeColor }};">DAYS</span></div>
                <div class="time-box" id="cd-hours" style="border-color: {{ $themeColor }};">00<span style="color: {{ $themeColor }};">HOURS</span></div>
                <div class="time-box" id="cd-minutes" style="border-color: {{ $themeColor }};">00<span style="color: {{ $themeColor }};">MINS</span></div>
                <div class="time-box" id="cd-seconds" style="border-color: {{ $themeColor }};">00<span style="color: {{ $themeColor }};">SECS</span></div>
            </div>
            @endif
        </div>
        <div style="flex: 1; min-width: 300px;">
            <div class="menu-card" style="display: flex; flex-direction: row; align-items: center; padding: 15px; gap: 20px; border: 1px dashed {{ $themeColor }}; background: {{ $themeColor }}10; backdrop-filter: blur(5px);">
                <div style="flex: 1;">
                    <h4 style="margin:0 0 5px; color:#fff; font-family: 'Playfair Display', serif; font-size: 1.3rem;">Cara Menggunakan</h4>
                    <ul style="color: #ccc; font-size: 0.9rem; line-height: 1.5; padding-left: 20px; margin-bottom: 10px;">
                        <li>Pilih menu favorit Anda</li>
                        <li>Lanjut ke halaman Keranjang</li>
                        <li>Masukkan kode <strong style="color:{{ $themeColor }};">{{ $activePromo->code }}</strong> pada kolom voucher</li>
                    </ul>
                    <a href="#menu-section" class="btn-add" style="display: inline-block; margin-top: 5px; position: static; transform: none; opacity: 1; background: {{ $themeColor }}; color: white;"><i class="fa-solid fa-cart-shopping"></i> Pesan Sekarang</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- MENU SECTION -->
<div id="menu-section" class="section-title fade-in-up" style="animation-delay: 0.2s;">
    <h2>Menu Kami</h2>
    <p>Dibuat dengan penuh cinta dan bahan premium setiap harinya.</p>
</div>

<!-- SEARCH BAR -->
<div class="search-container fade-in-up" style="animation-delay: 0.25s;">
    <form action="{{ route('client.guest.catalog') }}" method="GET" class="big-search-bar">
        @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input type="text" name="search" placeholder="Temukan hidangan favorit Anda..." value="{{ request('search') }}">
        <button type="submit" class="btn-search">Cari</button>
    </form>
</div>

<div class="menu-filter fade-in-up" style="animation-delay: 0.3s;">
    <a href="{{ route('client.guest.catalog') }}" class="cat-tab {{ !request('category') ? 'active' : '' }}">Semua</a>
    @foreach($categories as $cat)
    <a href="{{ route('client.guest.catalog', array_merge(request()->query(), ['category' => $cat->id])) }}" class="cat-tab {{ request('category') == $cat->id ? 'active' : '' }}">
        {{ $cat->name }}
    </a>
    @endforeach
</div>

<div class="menu-grid fade-in-up" style="animation-delay: 0.4s; margin-top: 30px;">
    @foreach($menus as $menu)
    @php
        $isAvailable = $menu->checkAvailability();
    @endphp
    <div class="menu-card {{ !$isAvailable ? 'sold-out' : '' }}">
        <div class="card-img-container">
            @if(!$isAvailable)
                <div class="card-badge" style="background:#555;">SOLD OUT</div>
            @elseif($menu->hasActiveDiscount())
                <style>
                    @keyframes discountPulse {
                        0% { transform: scale(1); }
                        50% { transform: scale(1.08); }
                        100% { transform: scale(1); }
                    }
                    .discount-pulse {
                        animation: discountPulse 2s infinite ease-in-out;
                    }
                </style>
                @if($menu->discount_type == 'percentage')
                    <div class="card-badge discount-pulse" style="background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); font-weight: 800; font-size: 0.95rem; padding: 6px 14px; border-radius: 20px; box-shadow: 0 4px 15px rgba(255, 65, 108, 0.4); text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">🔥 {{ round($menu->discount_value) }}% OFF</div>
                @else
                    @php 
                        $hemat = $menu->base_price - $menu->final_price; 
                        $hematText = $hemat >= 1000 ? ($hemat / 1000) . 'K' : $hemat;
                    @endphp
                    <div class="card-badge discount-pulse" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); font-weight: 800; font-size: 0.95rem; padding: 6px 14px; border-radius: 20px; box-shadow: 0 4px 15px rgba(155, 89, 182, 0.4); text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">✨ HEMAT {{ $hematText }}</div>
                @endif
            @else
                @if($loop->first) <div class="card-badge hot-badge">BEST SELLER</div> @endif
            @endif
            
            @if($menu->image_url)
                <img src="{{ asset('storage/' . $menu->image_url) }}" alt="{{ $menu->name }}">
            @else
                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#222; color:#555;">No Image</div>
            @endif
        </div>
        <div class="card-content">
            <h3 class="card-title">{{ $menu->name }}</h3>
            <p class="card-desc">{{ Str::limit($menu->description, 70) }}</p>
            
            <div style="color: var(--gold); font-size: 0.9rem; margin-bottom: 15px; font-weight: 600;">
                <i class="fa-solid fa-star"></i> {{ number_format($menu->rating_avg, 1) }} 
                <span style="color: var(--gray); font-size: 0.85rem; font-weight: 400;">({{ $menu->reviews_count }} Ulasan)</span>
            </div>

            <div class="card-footer">
                @if($menu->hasActiveDiscount())
                    <div class="card-price" style="display: flex; flex-direction: column; gap: 2px;">
                        <span style="text-decoration: line-through; color: var(--gray); font-size: 0.9rem;">Rp {{ number_format($menu->base_price, 0, ',', '.') }}</span>
                        <span style="color: var(--primary);">Rp {{ number_format($menu->final_price, 0, ',', '.') }}</span>
                    </div>
                @else
                    <div class="card-price">Rp {{ number_format($menu->base_price, 0, ',', '.') }}</div>
                @endif
                @if($isAvailable)
                    <a href="javascript:void(0)" onclick="openQuickViewModal({{ $menu->id }})" class="btn-add"><i class="fa-solid fa-plus"></i> Add</a>
                @else
                    <a href="#" class="btn-add disabled" style="display: block; opacity: 1; transform: translateY(0); background: #555;">Habis</a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

@if(!Session::has('table_id'))
<!-- WHY CHOOSE US -->
<div id="why-us" class="section-title">
    <h2>Mengapa Memilih Kami</h2>
</div>
<div class="features-grid">
    <div class="feature-card">
        <i class="fa-solid fa-leaf feature-icon"></i>
        <h3 style="color:white; margin-bottom:10px; font-family:'Playfair Display',serif;">Bahan Baku Segar</h3>
        <p style="color:var(--gray); font-size:0.9rem;">Kami hanya menggunakan bahan baku organik terbaik setiap harinya untuk memastikan cita rasa yang maksimal.</p>
    </div>
    <div class="feature-card">
        <i class="fa-solid fa-fire-burner feature-icon"></i>
        <h3 style="color:white; margin-bottom:10px; font-family:'Playfair Display',serif;">Oven Kayu Bakar</h3>
        <p style="color:var(--gray); font-size:0.9rem;">Metode pemanggangan otentik untuk menghasilkan kulit pizza sempurna yang tak tertandingi.</p>
    </div>
    <div class="feature-card">
        <i class="fa-solid fa-motorcycle feature-icon"></i>
        <h3 style="color:white; margin-bottom:10px; font-family:'Playfair Display',serif;">Pengiriman Cepat</h3>
        <p style="color:var(--gray); font-size:0.9rem;">Panas dan segar sampai di depan pintu Anda dalam hitungan menit, dijamin.</p>
    </div>
</div>

<!-- TESTIMONIALS -->
<div class="testimonials">
    <div class="section-title" style="margin-top:0; display: flex; flex-direction: column; align-items: center; gap: 15px; padding: 0 5%;">
        <h2>Suara Pelanggan Kami</h2>
        <button type="button" class="btn-hero-primary" style="padding: 10px 24px; font-size: 0.9rem; border: none; cursor: pointer;" onclick="document.getElementById('reviewModalOverlay').classList.add('active')">
            <i class="fa-solid fa-pen-nib"></i> Tulis Ulasan Anda
        </button>
    </div>
    
    <div class="testimonial-slider-container">
        <div class="testimonial-track">
            @php
                // Jika tidak ada review, gunakan fallback (dummy)
                $reviewsToDisplay = (isset($latestReviews) && $latestReviews->count() > 0) ? $latestReviews : collect([
                    (object)[
                        'rating' => 5, 'comment' => 'Pizza terlezat yang pernah saya coba di kota ini. Crustnya sempurna!', 'guest_name' => 'Budi Pelanggan', 'review_type' => 'general'
                    ],
                    (object)[
                        'rating' => 4, 'comment' => 'Pengalaman makan yang luar biasa. Bahan premium sungguh membuat perbedaan.', 'guest_name' => 'Sari W.', 'review_type' => 'ambiance'
                    ],
                    (object)[
                        'rating' => 5, 'comment' => 'Pengiriman cepat dan pizzanya masih panas saat sampai. Sangat direkomendasikan!', 'guest_name' => 'Andi P.', 'review_type' => 'service'
                    ]
                ]);
                
                // Duplicate items to ensure smooth infinite loop
                $loopedReviews = $reviewsToDisplay->concat($reviewsToDisplay)->concat($reviewsToDisplay);
            @endphp

            @foreach($loopedReviews as $review)
            <div class="testi-card">
                <div class="stars">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $review->rating)
                            <i class="fa-solid fa-star"></i>
                        @else
                            <i class="fa-regular fa-star"></i>
                        @endif
                    @endfor
                </div>
                <p style="font-style:italic; margin-bottom:15px;">"{{ Str::limit($review->comment ?? 'Sangat memuaskan! Recommended!', 120) }}"</p>
                <strong style="color:var(--gold);">— @if(isset($review->user)) {{ $review->user->name }} @else {{ $review->guest_name ?? 'Pelanggan' }} @endif</strong>
                
                <span style="display:block; font-size:0.8rem; color:var(--gray); margin-top:5px;">
                    @if(isset($review->review_type))
                        @if($review->review_type == 'service')
                            Layanan Restoran
                        @elseif($review->review_type == 'ambiance')
                            Suasana & Tempat
                        @else
                            Pengalaman Umum
                        @endif
                    @endif
                </span>
            </div>
            @endforeach
        </div>
    </div>
</div>


@endif

<!-- REVIEW MODAL -->
<div class="review-modal-overlay" id="reviewModalOverlay">
    <div class="review-modal">
        <i class="fa-solid fa-xmark review-close" onclick="document.getElementById('reviewModalOverlay').classList.remove('active')"></i>
        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.8rem; text-align: center; margin-bottom: 10px;">Berikan Ulasan</h3>
        <p style="text-align: center; color: var(--gray); font-size: 0.9rem; margin-bottom: 20px;">Bagaimana pengalaman Anda bersama {{ $storeName }}?</p>
        
        <form id="storeReviewForm" onsubmit="submitStoreReview(event)">
            @csrf
            <div class="review-stars">
                <input type="radio" id="star5" name="rating" value="5" required />
                <label for="star5" title="5 stars"><i class="fa-solid fa-star"></i></label>
                <input type="radio" id="star4" name="rating" value="4" />
                <label for="star4" title="4 stars"><i class="fa-solid fa-star"></i></label>
                <input type="radio" id="star3" name="rating" value="3" />
                <label for="star3" title="3 stars"><i class="fa-solid fa-star"></i></label>
                <input type="radio" id="star2" name="rating" value="2" />
                <label for="star2" title="2 stars"><i class="fa-solid fa-star"></i></label>
                <input type="radio" id="star1" name="rating" value="1" />
                <label for="star1" title="1 star"><i class="fa-solid fa-star"></i></label>
            </div>

            @guest
            <div class="review-form-group">
                <label>Nama Anda</label>
                <input type="text" name="guest_name" placeholder="John Doe" required>
            </div>
            @endguest

            <div class="review-form-group">
                <label>Topik Ulasan</label>
                <select name="review_type" required>
                    <option value="general">Pengalaman Umum</option>
                    <option value="service">Pelayanan Staff</option>
                    <option value="ambiance">Suasana & Kenyamanan Tempat</option>
                </select>
            </div>

            <div class="review-form-group">
                <label>Komentar</label>
                <textarea name="comment" rows="4" placeholder="Ceritakan pengalaman Anda..."></textarea>
            </div>

            <button type="submit" class="btn-hero-primary" style="width: 100%; border: none; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 10px;">
                <span id="reviewBtnText">Kirim Ulasan</span>
                <i class="fa-solid fa-spinner fa-spin" id="reviewBtnSpinner" style="display: none;"></i>
            </button>
            <div id="reviewMessage" style="margin-top: 15px; text-align: center; font-size: 0.9rem; display: none;"></div>
        </form>
    </div>
</div>

<script>
    // Countdown Timer Logic
    const timerElement = document.getElementById('countdownTimer');
    if (timerElement) {
        const expiresAt = new Date(timerElement.getAttribute('data-expires')).getTime();
        
        const countdownInterval = setInterval(function () {
            const now = new Date().getTime();
            const distance = expiresAt - now;
            
            if (distance < 0) {
                clearInterval(countdownInterval);
                document.getElementById('cd-days').innerHTML = '00<span>DAYS</span>';
                document.getElementById('cd-hours').innerHTML = '00<span>HOURS</span>';
                document.getElementById('cd-minutes').innerHTML = '00<span>MINS</span>';
                document.getElementById('cd-seconds').innerHTML = '00<span>SECS</span>';
                return;
            }
            
            let days = Math.floor(distance / (1000 * 60 * 60 * 24));
            let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);

            days = days < 10 ? "0" + days : days;
            hours = hours < 10 ? "0" + hours : hours;
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            document.getElementById('cd-days').innerHTML = days + '<span>DAYS</span>';
            document.getElementById('cd-hours').innerHTML = hours + '<span>HOURS</span>';
            document.getElementById('cd-minutes').innerHTML = minutes + '<span>MINS</span>';
            document.getElementById('cd-seconds').innerHTML = seconds + '<span>SECS</span>';
        }, 1000);
    }
    // Stats Counter Animation
    const statsObserverOptions = { threshold: 0.5 };
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                const counters = entry.target.querySelectorAll('.counter');
                counters.forEach(counter => {
                    const target = parseFloat(counter.getAttribute('data-target'));
                    const isDecimal = counter.getAttribute('data-decimal') === 'true';
                    const duration = 1500; // 1.5s
                    const increment = target / (duration / 16);
                    let current = 0;
                    
                    const updateCounter = () => {
                        current += increment;
                        if(current < target) {
                            counter.innerText = isDecimal ? current.toFixed(1) : Math.ceil(current);
                            requestAnimationFrame(updateCounter);
                        } else {
                            counter.innerText = target;
                        }
                    };
                    updateCounter();
                });
                statsObserver.unobserve(entry.target);
            }
        });
    }, statsObserverOptions);

    const statsBar = document.querySelector('.stats-bar');
    if(statsBar) statsObserver.observe(statsBar);

    // Review Form Submission Logic
    function submitStoreReview(e) {
        e.preventDefault();
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const btnText = document.getElementById('reviewBtnText');
        const spinner = document.getElementById('reviewBtnSpinner');
        const msgDiv = document.getElementById('reviewMessage');
        
        submitBtn.disabled = true;
        btnText.innerText = 'Mengirim...';
        spinner.style.display = 'inline-block';
        msgDiv.style.display = 'none';

        const formData = new FormData(form);

        fetch('{{ route("client.guest.api.store-reviews") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': formData.get('_token'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                msgDiv.innerHTML = `<span style="color: var(--green);"><i class="fa-solid fa-circle-check"></i> ${data.message}</span>`;
                msgDiv.style.display = 'block';
                form.reset();
                setTimeout(() => {
                    document.getElementById('reviewModalOverlay').classList.remove('active');
                    msgDiv.style.display = 'none';
                }, 3000);
            } else {
                msgDiv.innerHTML = `<span style="color: var(--primary);"><i class="fa-solid fa-circle-exclamation"></i> ${data.message || 'Terjadi kesalahan.'}</span>`;
                msgDiv.style.display = 'block';
            }
        })
        .catch(err => {
            msgDiv.innerHTML = `<span style="color: var(--primary);"><i class="fa-solid fa-circle-exclamation"></i> Terjadi kesalahan jaringan.</span>`;
            msgDiv.style.display = 'block';
        })
        .finally(() => {
            submitBtn.disabled = false;
            btnText.innerText = 'Kirim Ulasan';
            spinner.style.display = 'none';
        });
    }
</script>

@endsection
