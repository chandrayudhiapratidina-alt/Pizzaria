<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cashier Dashboard') - {{ $storeName }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,900;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #c00a27;
            --primary-hover: #a00820;
            --bg-color: #f4f5f7;
            --text-main: #1e1e1e;
            --text-muted: #6c757d;
            --green: #1b7339;
            --yellow: #ffc107;
            --border-color: #e5e7eb;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-color); color: var(--text-main); }
        
        .top-bar {
            background: white;
            padding: 0 30px;
            height: 65px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .top-bar-left {
            display: flex;
            align-items: center;
            gap: 25px;
        }
        .brand {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-tabs {
            display: flex;
            gap: 5px;
        }
        .nav-tab {
            padding: 10px 20px;
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }
        .nav-tab:hover { background: #f1f2f6; color: var(--text-main); }
        .nav-tab.active { background: var(--primary); color: white; }
        .nav-tab .badge {
            background: rgba(255,255,255,0.3);
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .nav-tab.active .badge { background: rgba(255,255,255,0.3); }
        .nav-tab:not(.active) .badge { background: #f1f2f6; color: var(--text-main); }
        
        .top-bar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .user-info {
            font-weight: 600;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .role-badge {
            background: var(--yellow);
            color: #000;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .logout-btn {
            background: none;
            border: 1px solid var(--border-color);
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
        }
        .logout-btn:hover { background: #fff0f0; border-color: var(--primary); }
        
        .container { max-width: 1200px; margin: 25px auto; padding: 0 25px; }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background: #e6f4ea; color: var(--green); border: 1px solid #c2f0d5; }
        
        .btn-primary { background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: inherit; transition: 0.2s; }
        .btn-primary:hover { background: var(--primary-hover); }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="top-bar-left">
            <div class="brand" style="display: flex; align-items: center; gap: 8px;">
                @php
                    $storeLogo = \App\Models\Setting::get('store_logo');
                    $storeName = \App\Models\Setting::get('store_name', 'Pizzaria');
                    $lightBgFirstColor = (in_array(strtolower($brandColorFirst), ['#ffffff', '#fff', '#fefefe', '#fafafa'])) ? 'var(--text-main)' : $brandColorFirst;
                @endphp
                @if($storeLogo)
                    <img src="{{ Storage::url($storeLogo) }}" alt="{{ $storeName }}" style="max-height: 30px; width: auto; object-fit: contain; border-radius: 4px;">
                @else
                    <i class="fa-solid fa-cash-register" style="color: {{ $brandColorSecond }};"></i>
                @endif
                <span class="brand-text-beautified" style="font-size: 1.15rem; font-family: 'Playfair Display', serif; font-weight: 900; letter-spacing: 0.5px; text-transform: uppercase; display: flex; align-items: center; gap: 5px;">
                    <span style="color: {{ $lightBgFirstColor }};">{{ $storeNameFirst }}</span><span style="color: {{ $brandColorSecond }};">{{ $storeNameSecond }}</span>
                    <span style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: none; font-family: 'Inter', sans-serif; margin-left: 2px;">Cashier</span>
                </span>
            </div>
            <nav class="nav-tabs">
                <a href="{{ route('cashier.dashboard') }}" class="nav-tab {{ request()->routeIs('cashier.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-list-check"></i> Antrian
                </a>
                <a href="{{ route('cashier.history') }}" class="nav-tab {{ request()->routeIs('cashier.history') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Hari Ini
                </a>
                <a href="{{ route('cashier.pos') }}" class="nav-tab {{ request()->routeIs('cashier.pos') ? 'active' : '' }}">
                    <i class="fa-solid fa-cash-register"></i> POS Terminal
                </a>
            </nav>
        </div>
        <div class="top-bar-right">
            <div class="user-info">
                <i class="fa-solid fa-user-circle" style="font-size: 1.3rem; color: var(--text-muted);"></i>
                {{ auth()->user()->name }}
                <span class="role-badge">Kasir</span>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </div>
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
        @endif
        @yield('content')
    </div>
</body>
</html>
