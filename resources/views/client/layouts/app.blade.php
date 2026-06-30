<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $storeName . ' - Authentic Pizza Experience')</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ $storeName }} menyajikan pengalaman makan pizza otentik dengan bahan-bahan premium. Pesan sekarang untuk Delivery atau Dine-in!">
    <meta name="keywords" content="pizza, {{ strtolower($storeName) }}, restoran pizza, pesan pizza online, delivery pizza, pizza premium, makanan italia">
    <meta name="author" content="{{ $storeName }}">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="restaurant">
    <meta property="og:title" content="@yield('title', $storeName . ' - Authentic Pizza Experience')">
    <meta property="og:description" content="{{ $storeName }} menyajikan pengalaman makan pizza otentik dengan bahan-bahan premium. Pesan sekarang untuk Delivery atau Dine-in!">
    <!-- Gambar default untuk share link (bisa diganti dengan gambar nyata) -->
    <meta property="og:image" content="{{ asset('assets/images/seo-cover.jpg') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', $storeName . ' - Authentic Pizza Experience')">
    <meta name="twitter:description" content="{{ $storeName }} menyajikan pengalaman makan pizza otentik dengan bahan-bahan premium. Pesan sekarang untuk Delivery atau Dine-in!">
    <meta name="twitter:image" content="{{ asset('assets/images/seo-cover.jpg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;0,900;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --bg-dark: #111111;
            --primary: #E8304A;
            --text-warm: #F5F0E8;
            --gold: #C9A84C;
            --card-bg: #1A1A1A;
            --footer-bg: #0A0A0A;
            --gray: #888888;
        }
        html { scroll-behavior: smooth; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-warm);
            overflow-x: hidden;
            line-height: 1.8;
        }
        .navbar {
            background: rgba(10, 10, 10, 0.75);
            backdrop-filter: blur(12px);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.2rem 5%;
            box-shadow: 0 4px 30px rgba(0,0,0,0.5);
            flex-wrap: wrap;
            gap: 15px;
            border-bottom: 1px solid transparent;
            border-image: linear-gradient(90deg, transparent, #E8304A 50%, transparent) 1;
            transition: all 0.3s ease;
        }
        .navbar .logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 900;
            color: #ffffff;
            text-decoration: none;
            letter-spacing: 1px;
        }
        .navbar .logo span { color: var(--primary); }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .nav-links a.nav-item {
            text-decoration: none;
            color: var(--text-warm);
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            position: relative;
        }
        .nav-links a.nav-item::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s ease;
        }
        .nav-links a.nav-item:hover::after { width: 100%; }
        .nav-links a.nav-item:hover { color: #ffffff; }
        
        .btn-outline {
            border: 1px solid transparent;
            padding: 8px 16px;
            border-radius: 20px;
        }
        .btn-outline:hover {
            border-color: rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.05);
        }
        
        .btn-pesan {
            background: var(--primary);
            color: #fff !important;
            padding: 8px 24px;
            border-radius: 30px;
            font-weight: 600 !important;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(232, 48, 74, 0.2);
        }
        .btn-pesan:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(232, 48, 74, 0.5);
        }
        .cart-badge {
            background: var(--primary);
            color: white;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(232, 48, 74, 0.7); }
            50% { transform: scale(1.3); box-shadow: 0 0 0 6px rgba(232, 48, 74, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(232, 48, 74, 0); }
        }
        @media (max-width: 600px) {
            .navbar {
                padding: 10px 15px;
                flex-wrap: nowrap;
                justify-content: space-between;
                align-items: center;
                gap: 10px;
            }
            .navbar .logo {
                font-size: 1.3rem;
                flex-shrink: 0;
            }
            .nav-links {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                -ms-overflow-style: none; /* IE and Edge */
                scrollbar-width: none; /* Firefox */
                gap: 15px;
                width: 100%;
                justify-content: flex-start;
                align-items: center;
                padding-bottom: 2px;
            }
            .nav-links::-webkit-scrollbar {
                display: none;
            }
            .nav-item, .btn-pesan, .cart-icon-wrapper, .mode-badge-nav {
                flex-shrink: 0;
                white-space: nowrap;
                font-size: 0.85rem !important;
            }
            .btn-pesan {
                padding: 6px 12px;
            }
            .cart-text {
                display: none;
            }
            .cart-icon-wrapper {
                display: flex;
                align-items: center;
                gap: 5px;
            }
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 5%;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 71, 87, 0.4);
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success { background: #e3fbee; color: #2ecc71; border: 1px solid #c2f0d5; }
        .alert-error { background: #ffeaa7; color: #d63031; border: 1px solid #ffeaa7; }
        
        /* Animations */
        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.8s forwards ease-out;
        }
        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Footer */
        .footer {
            background: var(--footer-bg);
            border-top: 1px solid var(--primary);
            padding: 60px 5% 30px;
            margin-top: 80px;
            color: var(--gray);
            font-size: 0.95rem;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
            margin-bottom: 40px;
        }
        .footer h3 {
            font-family: 'Playfair Display', serif;
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .footer-socials a {
            color: #fff;
            font-size: 1.5rem;
            margin-right: 15px;
            transition: color 0.3s;
        }
        .footer-socials a:hover { color: var(--primary); }
        .footer-bottom {
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
        }

        /* Floating IG */
        .floating-ig {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 0 20px rgba(220, 39, 67, 0.5);
            z-index: 1000;
            text-decoration: none;
            transition: transform 0.3s ease;
        }
        .floating-ig::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            border-radius: 50%;
            border: 2px solid #e6683c;
            animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
        @keyframes ping {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(1.8); opacity: 0; }
        }
        .floating-ig:hover {
            transform: scale(1.1);
            color: #fff;
            box-shadow: 0 0 30px rgba(220, 39, 67, 0.8);
        }
        .floating-ig .ig-tooltip {
            position: absolute;
            right: 80px;
            background: rgba(17, 17, 17, 0.9);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transform: translateX(10px);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .floating-ig:hover .ig-tooltip {
            opacity: 1;
            transform: translateX(0);
        }

        /* Global Micro-Interactions */
        .observe-me {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .observe-me.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .top-progress-bar {
            position: fixed;
            top: 0; left: 0; height: 3px;
            background: var(--primary);
            z-index: 9999;
            width: 0%;
            transition: width 0.4s ease-out;
            box-shadow: 0 0 10px var(--primary);
        }


        
        .noise-overlay {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            pointer-events: none;
            z-index: 1;
            opacity: 0.03;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }
        
        .content-wrapper { position: relative; z-index: 2; }

        /* ── SPA UI STYLES ── */
        
        /* Modals (Global) */
        .spa-modal-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.8); backdrop-filter: blur(5px);
            z-index: 10000; display: none; align-items: center; justify-content: center;
            opacity: 0; transition: opacity 0.3s ease;
        }
        .spa-modal-overlay.active { display: flex; opacity: 1; }
        .spa-modal-content {
            background: var(--card-bg); border-radius: 20px; padding: 30px;
            width: 90%; max-width: 500px; position: relative;
            transform: translateY(20px); transition: transform 0.3s ease;
            border: 1px solid #333; box-shadow: 0 10px 40px rgba(0,0,0,0.8);
        }
        .spa-modal-overlay.active .spa-modal-content { transform: translateY(0); }
        .spa-modal-close {
            position: absolute; top: 15px; right: 20px; font-size: 1.5rem;
            color: var(--gray); cursor: pointer; transition: color 0.2s;
        }
        .spa-modal-close:hover { color: var(--primary); }

        /* Cart Drawer */
        .cart-drawer-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.5); z-index: 9998; display: none;
            opacity: 0; transition: opacity 0.3s ease;
        }
        .cart-drawer-overlay.active { display: block; opacity: 1; }
        .cart-drawer {
            position: fixed; top: 0; right: -450px; width: 100%; max-width: 450px; height: 100vh;
            background: var(--bg-dark); z-index: 9999; box-shadow: -5px 0 30px rgba(0,0,0,0.5);
            transition: right 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex; flex-direction: column; border-left: 1px solid #333;
        }
        .cart-drawer.active { right: 0; }
        .cart-drawer-header {
            padding: 20px 25px; border-bottom: 1px solid #222;
            display: flex; justify-content: space-between; align-items: center;
        }
        .cart-drawer-title { font-size: 1.2rem; font-weight: 800; margin: 0; }
        .cart-drawer-close { font-size: 1.2rem; color: var(--gray); cursor: pointer; }
        .cart-drawer-close:hover { color: var(--primary); }
        
        .cart-drawer-body {
            flex: 1; overflow-y: auto; padding: 20px 25px;
        }
        .cart-drawer-item {
            display: flex; gap: 15px; margin-bottom: 20px; padding-bottom: 20px;
            border-bottom: 1px solid #222;
        }
        .cart-drawer-item img { width: 70px; height: 70px; border-radius: 10px; object-fit: cover; }
        .cart-item-details { flex: 1; }
        .cart-item-title { font-size: 1rem; font-weight: 700; margin: 0 0 5px; }
        .cart-item-price { color: var(--primary); font-weight: 700; margin: 0 0 5px; }
        .cart-item-notes { font-size: 0.8rem; color: var(--gray); margin: 0 0 5px; }
        .cart-item-qty { font-size: 0.85rem; font-weight: 600; color: #fff; background: #222; padding: 2px 8px; border-radius: 5px; display: inline-block; }
        .cart-item-remove { background: transparent; border: none; color: var(--red); cursor: pointer; font-size: 0.85rem; padding: 0; margin-left: 10px; }
        .cart-item-remove:hover { text-decoration: underline; }
        
        .cart-drawer-footer {
            padding: 25px; border-top: 1px solid #222; background: var(--card-bg);
        }
        .cart-drawer-total {
            display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 800; margin-bottom: 20px;
        }
        .cart-drawer-checkout-btn {
            display: block; width: 100%; text-align: center; padding: 15px;
            background: var(--primary); color: white; border-radius: 12px; font-weight: 800;
            text-decoration: none; border: none; cursor: pointer; transition: all 0.2s;
        }
        .cart-drawer-checkout-btn:hover { background: #d62c42; transform: translateY(-2px); }

        /* Quick View Styles */
        .quick-view-header { border-bottom: 1px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
        .quick-view-title { font-size: 1.5rem; font-weight: 800; margin: 0 0 5px; font-family: 'Playfair Display', serif; }
        .quick-view-price { color: var(--primary); font-size: 1.2rem; font-weight: 700; margin: 0; }
        .qv-section { margin-bottom: 20px; }
        .qv-section label { display: block; font-weight: 700; margin-bottom: 10px; color: #ccc; }
        .qv-options { display: flex; flex-direction: column; gap: 8px; }
        .qv-option {
            display: flex; align-items: center; padding: 12px 15px;
            background: var(--bg-dark); border: 1px solid #333; border-radius: 10px; cursor: pointer;
        }
        .qv-option:hover { border-color: #555; }
        .qv-option input[type="radio"] { margin-right: 12px; accent-color: var(--primary); width: 18px; height: 18px; }
        .qv-options-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .qv-checkbox {
            display: flex; align-items: flex-start; padding: 12px 15px;
            background: var(--bg-dark); border: 1px solid #333; border-radius: 10px; cursor: pointer;
        }
        .qv-checkbox input[type="checkbox"] { margin-right: 10px; margin-top: 4px; accent-color: var(--primary); width: 16px; height: 16px; }
        .qv-qty-controls { display: flex; align-items: center; max-width: 150px; background: var(--bg-dark); border-radius: 10px; border: 1px solid #333; overflow: hidden; }
        .qv-qty-btn { background: transparent; border: none; color: #fff; padding: 12px 15px; cursor: pointer; transition: background 0.2s; }
        .qv-qty-btn:hover { background: #333; }
        .qv-qty-input { flex: 1; text-align: center; border: none; background: transparent; color: #fff; font-weight: 700; font-size: 1.1rem; width: 100%; outline: none; }
        .btn-add-to-cart-ajax {
            width: 100%; background: var(--primary); color: white; border: none; border-radius: 10px;
            padding: 15px; font-weight: 800; font-size: 1.1rem; cursor: pointer; margin-top: 10px; transition: 0.2s;
        }
        .btn-add-to-cart-ajax:hover { background: #d62c42; }

        /* Auth Form Styles */
        .spa-auth-title { font-size: 1.8rem; font-weight: 800; margin: 0 0 5px; font-family: 'Playfair Display', serif; text-align: center; }
        .spa-auth-subtitle { color: var(--gray); text-align: center; margin: 0 0 25px; }
        .spa-form-group { margin-bottom: 15px; }
        .spa-form-group label { display: block; margin-bottom: 5px; font-size: 0.9rem; color: #ccc; }
        .spa-form-group input { width: 100%; padding: 12px 15px; border-radius: 10px; border: 1px solid #333; background: var(--bg-dark); color: white; }
        .spa-form-group input:focus { outline: none; border-color: var(--primary); }
        .btn-spa-auth { width: 100%; background: var(--primary); color: white; padding: 12px; border-radius: 10px; border: none; font-weight: 700; margin-top: 10px; cursor: pointer; }
        .spa-auth-toggle { text-align: center; margin-top: 20px; font-size: 0.9rem; color: var(--gray); }
        .spa-auth-toggle a { color: var(--primary); text-decoration: none; font-weight: 700; cursor: pointer; }

        /* Greeting Modal specific */
        .greeting-options { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 25px; }
        .greeting-option {
            background: var(--bg-dark); border: 2px solid #333; border-radius: 15px; padding: 25px 15px;
            text-align: center; cursor: pointer; transition: all 0.2s;
        }
        .greeting-option:hover { border-color: var(--primary); background: rgba(232,48,74,0.05); transform: translateY(-5px); }
        .greeting-option i { font-size: 2.5rem; color: var(--primary); margin-bottom: 15px; display: block; }
        .greeting-option h3 { margin: 0 0 5px; font-size: 1.2rem; }
        .greeting-option p { margin: 0; font-size: 0.85rem; color: var(--gray); }

        /* Brand logo & typography styling */
        .logo {
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand-logo-img {
            max-height: 40px;
            width: auto;
            object-fit: contain;
            border-radius: 6px;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .logo:hover .brand-logo-img {
            transform: scale(1.1) rotate(3deg);
        }
        .brand-text-beautified {
            font-family: 'Playfair Display', serif;
            font-weight: 900;
            font-size: 1.6rem;
            letter-spacing: 1px;
            color: #ffffff;
            text-transform: uppercase;
            transition: all 0.3s ease;
            display: inline-block;
        }
        .logo:hover .brand-text-beautified {
            text-shadow: 0 0 15px var(--hover-glow, rgba(232, 48, 74, 0.6)), 0 0 30px var(--hover-glow, rgba(232, 48, 74, 0.3));
            transform: translateY(-1px);
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="top-progress-bar" id="topProgressBar"></div>
    <div class="noise-overlay"></div>

    
    <div class="content-wrapper">
    <nav class="navbar">
        @php
            $storeLogo = \App\Models\Setting::get('store_logo');
            $storeName = \App\Models\Setting::get('store_name', 'PIZZARIA');
        @endphp
        <a href="{{ route('client.guest.catalog') }}" class="logo" style="--hover-glow: {{ $brandColorSecond }}80;">
            @if($storeLogo)
                <img src="{{ Storage::url($storeLogo) }}" alt="{{ $storeName }}" class="brand-logo-img">
            @endif
            <span class="brand-text-beautified">
                <span style="color: {{ $brandColorFirst }};">{{ $storeNameFirst }}</span><span style="color: {{ $brandColorSecond }};">{{ $storeNameSecond }}</span>
            </span>
        </a>
        <div class="nav-links">
            @if(session('table_id'))
                <span class="mode-badge-nav" style="color: var(--gold); border: 1px solid var(--gold); border-radius: 15px; padding: 4px 12px; font-size: 0.85rem; display: flex; align-items: center; gap: 5px;">
                    <i class="fa-solid fa-chair"></i> Meja {{ session('table_number') }}
                </span>
            @elseif(session('order_mode'))
                <span class="mode-badge-nav" style="color: var(--primary); border: 1px solid var(--primary); border-radius: 15px; padding: 4px 12px; font-size: 0.85rem; display: flex; align-items: center; gap: 5px; text-transform: capitalize;">
                    <i class="fa-solid {{ session('order_mode') == 'delivery' ? 'fa-motorcycle' : 'fa-bag-shopping' }}"></i> {{ session('order_mode') }}
                </span>
            @endif
            <a href="{{ route('client.guest.catalog') }}" class="nav-item">Menu</a>
            <a href="{{ route('about') }}" class="nav-item" id="nav-about">About</a>
            @if(!session('table_id'))
                <a href="{{ route('client.online.landing') }}" class="btn-pesan nav-item"><i class="fa-solid fa-motorcycle"></i> Pesan Online</a>
            @endif
            @if(session('recent_order_id'))
                <a href="{{ route('client.online.orders.show', session('recent_order_id')) }}" class="nav-item" style="color: var(--primary);">Orders</a>
            @endif
            
            <div class="cart-icon-wrapper">
                @if(!session('table_id'))
                    @auth
                        <a href="{{ route('client.online.profile') }}" class="nav-item btn-outline"><i class="fa-regular fa-user" style="margin-right: 5px;"></i> Akun</a>
                    @else
                        <a href="javascript:void(0)" onclick="openAuthModal()" class="nav-item btn-outline"><i class="fa-solid fa-arrow-right-to-bracket" style="margin-right: 5px;"></i> Masuk</a>
                    @endauth
                @endif
                <a href="javascript:void(0)" onclick="openCartDrawer()" class="nav-item" style="margin-left: 5px;">
                    <i class="fa-solid fa-basket-shopping" style="font-size: 1.2rem;"></i>
                    <span class="cart-badge">{{ array_sum(array_column(session('cart', []), 'qty')) }}</span>
                </a>
            </div>
        </div>
    </nav>
    
    @hasSection('full-width-content')
        @yield('full-width-content')
    @else
        <div class="container" style="padding-top: 100px;">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            
            @yield('content')
        </div>
    @endif
    
    <footer class="footer">
        <div class="footer-grid">
            <div>
                @php
                    $storeLogo = \App\Models\Setting::get('store_logo');
                    $storeName = \App\Models\Setting::get('store_name', 'PIZZARIA');
                @endphp
                <h3 style="margin-bottom: 10px; display: flex; align-items: center; gap: 12px; --hover-glow: {{ $brandColorSecond }}80;">
                    @if($storeLogo)
                        <img src="{{ Storage::url($storeLogo) }}" alt="{{ $storeName }}" class="brand-logo-img" style="max-height: 35px;">
                    @endif
                    <span class="brand-text-beautified" style="font-size: 1.4rem;">
                        <span style="color: {{ $brandColorFirst }};">{{ $storeNameFirst }}</span><span style="color: {{ $brandColorSecond }};">{{ $storeNameSecond }}</span>
                    </span>
                </h3>
                <p>Authentic Italian trattoria meets modern fine dining. Baked perfectly every time.</p>
                <div class="footer-socials" style="margin-top: 20px;">
                    <a href="https://www.instagram.com/pizzaria.pwt?igsh=empuejdlaXU0c2pw" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>
            <div>
                <h3>Explore</h3>
                <p><a href="{{ route('client.guest.catalog') }}" style="color:var(--gray); text-decoration:none;">Our Menu</a></p>
                <p><a href="{{ route('faq') }}" style="color:var(--gray); text-decoration:none;">FAQ</a></p>
                <p><a href="{{ route('terms') }}" style="color:var(--gray); text-decoration:none;">Syarat & Ketentuan</a></p>
                <p><a href="{{ route('privacy') }}" style="color:var(--gray); text-decoration:none;">Kebijakan Privasi</a></p>
            </div>
            <div>
                <h3>Contact</h3>
                @php
                    $storeAddress = \App\Models\Setting::get('store_address', 'Jl. Jenderal Sudirman No. 10, Purwokerto');
                    $storePhone = \App\Models\Setting::get('store_phone', '+62 811 2233 4455');
                    $storeEmail = \App\Models\Setting::get('store_email', 'ciao@pizzaria.com');
                @endphp
                <p><i class="fa-solid fa-location-dot"></i> {{ $storeAddress }}</p>
                <p><i class="fa-solid fa-phone"></i> {{ $storePhone }}</p>
                <p><i class="fa-solid fa-envelope"></i> {{ $storeEmail }}</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 {{ $storeName }}. All rights reserved. <span style="color:var(--gold)">Crafted with passion.</span></p>
        </div>
    </footer>
    
    <!-- Script to add shadow on scroll for navbar -->
    <script>
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('.navbar');
            if(window.scrollY > 50) {
                nav.style.background = 'rgba(10, 10, 10, 0.9)';
            } else {
                nav.style.background = 'rgba(10, 10, 10, 0.75)';
            }
        });
    </script>
    
    <!-- Floating Instagram -->
    <a href="https://www.instagram.com/pizzaria.pwt?igsh=empuejdlaXU0c2pw" target="_blank" class="floating-ig">
        <span class="ig-tooltip">Ikuti Instagram Kami!</span>
        <i class="fa-brands fa-instagram"></i>
    </a>
    
    </div> <!-- end content-wrapper -->

    <!-- ── SPA UI ELEMENTS ── -->

    <!-- Cart Drawer -->
    <div class="cart-drawer-overlay" id="cartDrawerOverlay" onclick="closeCartDrawer()"></div>
    <div class="cart-drawer" id="cartDrawer">
        <div class="cart-drawer-header">
            <h3 class="cart-drawer-title"><i class="fa-solid fa-cart-shopping"></i> Keranjang</h3>
            <span class="cart-drawer-close" onclick="closeCartDrawer()"><i class="fa-solid fa-xmark"></i></span>
        </div>
        <div class="cart-drawer-body" id="cartDrawerBody">
            <div style="text-align:center; padding: 40px 20px; color: var(--gray);">Memuat keranjang...</div>
        </div>
        <div class="cart-drawer-footer">
            <div class="cart-drawer-discount" id="cartDrawerDiscountContainer" style="display:none; justify-content: space-between; font-size: 1rem; font-weight: 700; color: var(--primary); margin-bottom: 10px;">
                <span>Diskon <span id="cartDrawerPromoCode"></span></span>
                <span id="cartDrawerDiscount">- Rp 0</span>
            </div>
            <div class="cart-drawer-total">
                <span>Total</span>
                <span id="cartDrawerTotal" style="font-size: 1.4rem;">Rp 0</span>
            </div>
            <!-- Check auth status for checkout logic -->
            @if(Session::has('table_id'))
                {{-- Dine-In: checkout langsung ke halaman dinein checkout --}}
                <a href="{{ route('client.guest.dinein.checkout') }}" class="cart-drawer-checkout-btn" style="background: var(--gold); color: #000; text-align: center; display: block; text-decoration: none;">
                    <i class="fa-solid fa-utensils"></i> Pesan Sekarang (Meja {{ Session::get('table_number') }})
                </a>
            @elseif(in_array(session('order_mode'), ['delivery', 'pickup']))
                @auth
                    <a href="{{ route('client.online.checkout') }}" class="cart-drawer-checkout-btn" style="display:block; text-align:center;">Checkout Online</a>
                @else
                    <button onclick="openAuthModal()" class="cart-drawer-checkout-btn">Checkout</button>
                @endauth
            @else
                <button onclick="document.getElementById('greetingModalOverlay').classList.add('active');" class="cart-drawer-checkout-btn">Checkout</button>
            @endif
        </div>
    </div>

    <!-- Global Modal Container for Quick View -->
    <div class="spa-modal-overlay" id="quickViewModalOverlay" onclick="if(event.target===this) closeQuickViewModal()">
        <div class="spa-modal-content" id="quickViewModalContent">
            <!-- Loaded via AJAX -->
        </div>
    </div>

    <!-- Auth Modal -->
    <div class="spa-modal-overlay" id="authModalOverlay" onclick="if(event.target===this) closeAuthModal()">
        <div class="spa-modal-content">
            <span class="spa-modal-close" onclick="closeAuthModal()"><i class="fa-solid fa-xmark"></i></span>
            <h2 class="spa-auth-title">Masuk</h2>
            <p class="spa-auth-subtitle">Silakan login untuk melanjutkan checkout pesanan Anda.</p>
            <form id="spaLoginForm" onsubmit="submitSpaLogin(event)">
                @csrf
                <div class="spa-form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="spa-form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn-spa-auth">Masuk</button>
                <div id="spaLoginError" style="color:var(--red); font-size:0.85rem; margin-top:10px; text-align:center;"></div>
            </form>
            <div class="spa-auth-toggle">
                Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
            </div>
        </div>
    </div>

    <!-- Greeting Modal (Order Mode) -->
    <div class="spa-modal-overlay" id="greetingModalOverlay">
        <div class="spa-modal-content" style="max-width: 600px;">
            <h2 class="spa-auth-title" style="margin-bottom:10px;">Selamat Datang!</h2>
            <p class="spa-auth-subtitle">Bagaimana Anda ingin menikmati {{ $storeName }} hari ini?</p>
            <div class="greeting-options">
                <div class="greeting-option" onclick="setOrderMode('delivery')">
                    <i class="fa-solid fa-motorcycle"></i>
                    <h3>Delivery</h3>
                    <p>Antar ke rumah</p>
                </div>
                <div class="greeting-option" onclick="setOrderMode('pickup')">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <h3>Pickup</h3>
                    <p>Ambil sendiri</p>
                </div>
            </div>
            <div style="text-align:center; margin-top: 20px;">
                <a href="#" onclick="document.getElementById('greetingModalOverlay').classList.remove('active');" style="color:var(--gray); font-size:0.9rem;">Tutup / Lihat Menu Saja</a>
            </div>
        </div>
    </div>

    <!-- Dine-In Name Modal -->
    <div class="spa-modal-overlay" id="dineInNameModalOverlay" onclick="if(event.target===this) closeDineInNameModal()">
        <div class="spa-modal-content">
            <span class="spa-modal-close" onclick="closeDineInNameModal()"><i class="fa-solid fa-xmark"></i></span>
            <h2 class="spa-auth-title" style="margin-bottom:10px;">Atas Nama Siapa?</h2>
            <p class="spa-auth-subtitle">Masukkan nama Anda untuk pesanan ini agar kami mudah mengantarkannya ke meja.</p>
            <form id="dineInNameForm" onsubmit="submitDineInCheckout(event)">
                <div class="spa-form-group">
                    <label>Nama Lengkap / Panggilan</label>
                    <input type="text" id="dineInCustomerName" name="customer_name" required placeholder="Contoh: Budi">
                </div>
                <button type="submit" class="btn-spa-auth">Proses Pesanan</button>
            </form>
        </div>
    </div>

    <!-- ── SPA CORE JS ── -->
    <script>
        // Check order mode on load
        document.addEventListener('DOMContentLoaded', () => {
            const currentMode = "{{ session('order_mode') }}";
            const tableId = "{{ session('table_id') }}";
            const isCatalogPage = window.location.pathname === '/' || window.location.pathname === '/client/catalog' || window.location.pathname.startsWith('/client/menu');
            
            if (!currentMode && !tableId && isCatalogPage) {
                // if no mode is set, show greeting modal (but delay slightly for effect)
                setTimeout(() => {
                    document.getElementById('greetingModalOverlay').classList.add('active');
                }, 500);
            }
        });

        function setOrderMode(mode) {
            fetch('{{ route("client.guest.api.mode") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ mode: mode })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    document.getElementById('greetingModalOverlay').classList.remove('active');
                    window.location.reload();
                }
            });
        }

        function openDineInNameModal() {
            closeCartDrawer();
            document.getElementById('dineInNameModalOverlay').classList.add('active');
            setTimeout(() => document.getElementById('dineInCustomerName').focus(), 100);
        }

        function closeDineInNameModal() {
            document.getElementById('dineInNameModalOverlay').classList.remove('active');
        }

        // Dine-In Checkout Form Submitter
        function submitDineInCheckout(event) {
            event.preventDefault();
            let name = document.getElementById('dineInCustomerName').value;
            if (name.trim() === '') name = "Guest";

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("client.guest.checkout") }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const orderType = document.createElement('input');
            orderType.type = 'hidden';
            orderType.name = 'order_type';
            orderType.value = 'dine_in';
            
            const custName = document.createElement('input');
            custName.type = 'hidden';
            custName.name = 'customer_name';
            custName.value = name;
            
            form.appendChild(csrfToken);
            form.appendChild(orderType);
            form.appendChild(custName);
            
            document.body.appendChild(form);
            form.submit();
        }

        // Cart Drawer Functions
        function openCartDrawer() {
            document.getElementById('cartDrawerOverlay').classList.add('active');
            document.getElementById('cartDrawer').classList.add('active');
            fetchCart();
        }
        function closeCartDrawer() {
            document.getElementById('cartDrawerOverlay').classList.remove('active');
            document.getElementById('cartDrawer').classList.remove('active');
        }

        function formatRp(num) {
            return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function fetchCart() {
            fetch('{{ route("client.guest.api.cart") }}')
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    updateCartBadge(data.count);
                    renderCartItems(data.cart);
                    
                    if(data.discount > 0) {
                        document.getElementById('cartDrawerDiscountContainer').style.display = 'flex';
                        document.getElementById('cartDrawerPromoCode').innerText = '(' + data.promoCode + ')';
                        document.getElementById('cartDrawerDiscount').innerText = '- ' + formatRp(data.discount);
                    } else {
                        document.getElementById('cartDrawerDiscountContainer').style.display = 'none';
                    }
                    
                    document.getElementById('cartDrawerTotal').innerText = formatRp(data.grandTotal);
                }
            });
        }

        function renderCartItems(cartItems) {
            const body = document.getElementById('cartDrawerBody');
            if(cartItems.length === 0) {
                body.innerHTML = '<div style="text-align:center; padding: 40px 20px; color: var(--gray);"><i class="fa-solid fa-cart-arrow-down" style="font-size:3rem;margin-bottom:15px;opacity:0.3"></i><br>Keranjang Anda masih kosong.</div>';
                return;
            }

            let html = '';
            cartItems.forEach(item => {
                let notesHtml = '';
                if(item.customization_notes) {
                    Object.values(item.customization_notes).forEach(note => {
                        notesHtml += `<p class="cart-item-notes">• ${note}</p>`;
                    });
                }
                
                html += `
                <div class="cart-drawer-item">
                    <img src="${item.image_url ? '/storage/'+item.image_url : '/images/default_pizza.jpg'}" alt="${item.menu_name}">
                    <div class="cart-item-details">
                        <h4 class="cart-item-title">${item.menu_name}</h4>
                        <p class="cart-item-price">${formatRp(item.price)}</p>
                        ${notesHtml}
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                            <span class="cart-item-qty">${item.qty}x</span>
                            <button class="cart-item-remove" onclick="removeCartItem('${item.cart_id}')"><i class="fa-solid fa-trash"></i> Hapus</button>
                        </div>
                    </div>
                </div>
                `;
            });
            body.innerHTML = html;
        }

        function removeCartItem(cartId) {
            fetch('{{ route("client.guest.cart.remove") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ cart_id: cartId })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    fetchCart(); // refresh drawer
                }
            });
        }

        function updateCartBadge(count) {
            const badge = document.querySelector('.cart-badge');
            if(badge) badge.innerText = count;
        }

        // Quick View Modal
        function openQuickViewModal(menuId) {
            fetch(`/client/api/quick-view/${menuId}`)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    document.getElementById('quickViewModalContent').innerHTML = `<span class="spa-modal-close" onclick="closeQuickViewModal()"><i class="fa-solid fa-xmark"></i></span>` + data.html;
                    document.getElementById('quickViewModalOverlay').classList.add('active');
                    initQuickViewLogic();
                }
            });
        }
        function closeQuickViewModal() {
            document.getElementById('quickViewModalOverlay').classList.remove('active');
        }

        function initQuickViewLogic() {
            const form = document.getElementById('quick-view-form');
            if(!form) return;
            
            const btnMinus = form.querySelector('.minus');
            const btnPlus = form.querySelector('.plus');
            const inputQty = form.querySelector('.qv-qty-input');
            
            if(btnMinus && btnPlus && inputQty) {
                btnMinus.addEventListener('click', () => {
                    let v = parseInt(inputQty.value);
                    if(v > 1) inputQty.value = v - 1;
                });
                btnPlus.addEventListener('click', () => {
                    let v = parseInt(inputQty.value);
                    inputQty.value = v + 1;
                });
            }
        }

        function submitQuickViewForm() {
            const form = document.getElementById('quick-view-form');
            const formData = new FormData(form);
            
            fetch('{{ route("client.guest.cart.add") }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    closeQuickViewModal();
                    updateCartBadge(data.cart_count);
                    openCartDrawer(); // open drawer to show it was added
                }
            });
        }

        // Direct Add to Cart (for items without customizations)
        function quickAddToCart(menuId) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('menu_id', menuId);
            formData.append('qty', 1);
            
            fetch('{{ route("client.guest.cart.add") }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    updateCartBadge(data.cart_count);
                    openCartDrawer();
                }
            });
        }

        // Auth Modal
        function openAuthModal() {
            document.getElementById('authModalOverlay').classList.add('active');
        }
        function closeAuthModal() {
            document.getElementById('authModalOverlay').classList.remove('active');
        }
        function submitSpaLogin(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const errDiv = document.getElementById('spaLoginError');
            errDiv.innerText = "Memproses...";
            
            fetch('{{ route("client.guest.api.login") }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    window.location.href = '{{ route("client.online.checkout") }}';
                } else {
                    errDiv.innerText = data.error || "Login gagal. Periksa kembali email & password Anda.";
                }
            })
            .catch(err => {
                errDiv.innerText = "Terjadi kesalahan sistem.";
            });
        }

        // Progress Bar Simulation
        document.addEventListener("DOMContentLoaded", () => {
            const bar = document.getElementById("topProgressBar");
            if (bar) {
                bar.style.width = "30%";
                setTimeout(() => { bar.style.width = "70%"; }, 300);
                setTimeout(() => { bar.style.width = "100%"; }, 600);
                setTimeout(() => { bar.style.opacity = "0"; }, 900);
            }
            


            // Intersection Observer for Entrance Animations
            const observerOptions = { threshold: 0.1, rootMargin: "0px 0px -50px 0px" };
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if(entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.observe-me').forEach(el => observer.observe(el));
        });
    </script>
    @stack('scripts')
</body>
</html>
