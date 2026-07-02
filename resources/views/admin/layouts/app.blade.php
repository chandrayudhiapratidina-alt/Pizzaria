<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - {{ $storeName }}</title>
    <meta name="author" content="Chandra Yudhia Pratidina">
    <meta name="developer" content="Chandra Yudhia Pratidina">
    <meta name="copyright" content="© 2026 Chandra Yudhia Pratidina. All Rights Reserved.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,900;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #c00a27;
            --primary-hover: #e81036;
            --bg-color: #f7f9fc;
            --sidebar-bg: #111827;
            --sidebar-text: #9ca3af;
            --sidebar-text-hover: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --green: #10b981;
            --yellow: #f59e0b;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-soft: 0 10px 40px -10px rgba(0,0,0,0.08);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-color); color: var(--text-main); display: flex; height: 100vh; overflow: hidden; }
        
        /* Sidebar Styling (Dark Theme) */
        .sidebar { width: 260px; background: var(--sidebar-bg); border-right: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column; color: var(--sidebar-text); box-shadow: 4px 0 20px rgba(0,0,0,0.05); z-index: 10; transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1); margin-left: 0; }
        .sidebar.collapsed { margin-left: -260px; }
        .sidebar-brand { padding: 30px 25px; font-size: 1.4rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 12px; letter-spacing: -0.5px; }
        .sidebar-brand i { color: var(--primary); font-size: 1.5rem; }
        .nav-links { flex: 1; padding: 15px 0; overflow-y: auto; }
        .nav-links a { display: flex; align-items: center; gap: 15px; padding: 14px 25px; color: var(--sidebar-text); text-decoration: none; font-weight: 500; font-size: 0.95rem; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-left: 3px solid transparent; margin-bottom: 2px; }
        .nav-links a:hover { color: var(--sidebar-text-hover); background: rgba(255, 255, 255, 0.05); transform: translateX(5px); border-left-color: rgba(192, 10, 39, 0.5); }
        .nav-links a.active { background: linear-gradient(90deg, rgba(192, 10, 39, 0.15) 0%, rgba(192, 10, 39, 0) 100%); color: #fff; border-left-color: var(--primary); font-weight: 600; }
        .nav-links a.active i { color: var(--primary); }
        .nav-links a i { width: 20px; text-align: center; font-size: 1.1rem; transition: color 0.3s ease; }
        


        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: var(--bg-color); }
        
        /* Top Navbar */
        .top-navbar { height: 70px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; padding: 0 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); z-index: 5; }
        .nav-left { display: flex; align-items: center; gap: 25px; }
        .menu-toggle { color: var(--text-main); font-size: 1.2rem; cursor: pointer; padding: 8px; border-radius: 8px; transition: background 0.2s; }
        .menu-toggle:hover { background: var(--border-color); }
        .status-badge { background: #d1fae5; color: #065f46; padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display: flex; align-items: center; gap: 6px; letter-spacing: 0.5px; box-shadow: inset 0 0 0 1px rgba(16, 185, 129, 0.2); }
        .status-badge::before { content: ''; width: 8px; height: 8px; background: var(--green); border-radius: 50%; box-shadow: 0 0 5px var(--green); }
        
        .nav-right { display: flex; align-items: center; gap: 25px; }
        .nav-icon { color: var(--text-muted); font-size: 1.2rem; cursor: pointer; transition: color 0.2s; position: relative; }
        .nav-icon:hover { color: var(--primary); }
        .nav-icon::after { content: ''; position: absolute; top: -3px; right: -3px; width: 8px; height: 8px; background: var(--primary); border-radius: 50%; border: 2px solid white; }
        .user-profile { display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 5px 10px; border-radius: 30px; transition: background 0.2s; }
        .user-profile:hover { background: #f3f4f6; }
        .user-avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #111827, #374151); color: white; display: flex; justify-content: center; align-items: center; font-weight: 700; font-size: 0.9rem; }
        .user-info { display: flex; flex-direction: column; }
        .user-name { font-weight: 600; font-size: 0.9rem; color: var(--text-main); line-height: 1.2; }
        .user-role { font-size: 0.75rem; color: var(--text-muted); }
        .logout-btn { color: var(--primary); text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; border: none; background: rgba(192, 10, 39, 0.1); padding: 8px 16px; border-radius: 20px; cursor: pointer; font-family: inherit; font-size: 0.9rem; transition: all 0.2s; }
        .logout-btn:hover { background: var(--primary); color: white; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(192, 10, 39, 0.2); }
        
        .content-area { flex: 1; padding: 40px; overflow-y: auto; animation: fadeIn 0.4s ease-out forwards; }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Utility overrides */
        .card { background: white; border-radius: 16px; border: 1px solid rgba(229, 231, 235, 0.5); box-shadow: var(--shadow-soft); transition: transform 0.2s, box-shadow 0.2s; }
        .card:hover { box-shadow: var(--shadow-lg); }
        .btn-primary { background: var(--primary); color: white; border: none; padding: 12px 24px; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 10px rgba(192, 10, 39, 0.2); font-family: inherit; }
        .btn-primary:hover { background: var(--primary-hover); transform: translateY(-2px); box-shadow: 0 6px 15px rgba(192, 10, 39, 0.3); }
        
        .alert { padding: 16px 20px; border-radius: 12px; margin-bottom: 25px; font-weight: 500; display: flex; align-items: center; gap: 10px; box-shadow: var(--shadow-sm); }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
        
        /* Scrollbar styles for modern look */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand" style="justify-content: flex-start; padding: 20px 15px; display: flex; align-items: center; gap: 10px;">
            @php
                $storeLogo = \App\Models\Setting::get('store_logo');
                $storeName = \App\Models\Setting::get('store_name', 'Pizzaria');
            @endphp
            @if($storeLogo)
                <img src="{{ Storage::url($storeLogo) }}" alt="{{ $storeName }}" style="max-height: 35px; width: auto; object-fit: contain; border-radius: 4px;">
            @else
                <i class="fa-solid fa-pizza-slice" style="color: {{ $brandColorSecond }};"></i>
            @endif
            <span class="brand-text-beautified" style="font-size: 1.25rem; font-family: 'Playfair Display', serif; font-weight: 900; letter-spacing: 0.5px; text-transform: uppercase;">
                <span style="color: {{ $brandColorFirst }};">{{ $storeNameFirst }}</span><span style="color: {{ $brandColorSecond }};">{{ $storeNameSecond }}</span>
            </span>
        </div>
        <div class="nav-links">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-border-all"></i> Dashboard
            </a>
            <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt"></i> Pesanan
            </a>
            <a href="{{ route('admin.menus.index') }}" class="{{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                <i class="fa-solid fa-pen-to-square"></i> Menu Editor
            </a>
            <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i> Kategori
            </a>
            <a href="{{ route('admin.customizations.index') }}" class="{{ request()->routeIs('admin.customizations.*') ? 'active' : '' }}">
                <i class="fa-solid fa-list-ul"></i> Kustomisasi Menu
            </a>
            <a href="{{ route('admin.tables.index') }}" class="{{ request()->routeIs('admin.tables.*') ? 'active' : '' }}">
                <i class="fa-solid fa-qrcode"></i> Meja & QR Code
            </a>

            <a href="{{ route('admin.staff.index') }}" class="{{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i> Staff
            </a>
            <a href="{{ route('admin.ingredients.index') }}" class="{{ request()->routeIs('admin.ingredients.*') ? 'active' : '' }}">
                <i class="fa-solid fa-carrot"></i> Bahan Baku
            </a>
            <a href="{{ route('admin.analytics.index') }}" class="{{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i> Analitik & Laporan
            </a>
            <a href="{{ route('admin.store_reviews.index') }}" class="{{ request()->routeIs('admin.store_reviews.*') ? 'active' : '' }}">
                <i class="fa-solid fa-comments"></i> Ulasan Restoran
            </a>
            <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i> Pengaturan
            </a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="top-navbar">
            <div class="nav-left">
                <i class="fa-solid fa-bars menu-toggle"></i>
                <div class="status-badge">SYSTEM STATUS: MAIN HQ</div>
            </div>
            <div class="nav-right">
                <div style="position: relative;" id="notificationContainer">
                    <a href="javascript:void(0)" style="color: inherit; text-decoration: none; position: relative; display: flex; align-items: center;" id="notificationLink">
                        <i class="fa-solid fa-bell nav-icon" title="Pusat Peringatan Dini"></i>
                        <span id="notificationBadge" style="display: none; position: absolute; top: -5px; right: -5px; background: #e81036; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; font-weight: bold; border: 2px solid #1f2937; box-shadow: 0 2px 5px rgba(0,0,0,0.2); transition: transform 0.3s ease;">0</span>
                    </a>
                    
                    <!-- Notification Dropdown -->
                    <div id="notificationDropdown" style="display: none; position: absolute; right: 0; top: calc(100% + 15px); width: 340px; background: #111827; border: 1px solid #374151; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.8); z-index: 1000; overflow: hidden; transform-origin: top right; transition: all 0.2s ease-out; opacity: 0; transform: scale(0.95);">
                        <div style="padding: 15px 20px; border-bottom: 1px solid #374151; background: #1f2937; display: flex; justify-content: space-between; align-items: center;">
                            <h4 style="margin: 0; color: #fff; font-size: 0.95rem; font-weight: 600;">Pusat Peringatan</h4>
                            <span id="notificationCountText" style="background: #e81036; color: white; font-size: 0.7rem; padding: 3px 8px; border-radius: 12px; font-weight: bold;">0 Baru</span>
                        </div>
                        <div id="notificationList" style="max-height: 380px; overflow-y: auto;">
                            <!-- Notif items injected via JS -->
                            <div style="padding: 30px 20px; text-align: center; color: #9ca3af; font-size: 0.85rem;">
                                <i class="fa-regular fa-bell-slash" style="font-size: 2rem; margin-bottom: 10px; opacity: 0.5;"></i><br>
                                Tidak ada peringatan sistem saat ini.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="user-profile">
                    <div class="user-avatar">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</div>
                    <div class="user-info">
                        <span class="user-name">{{ Auth::user()->name ?? 'Administrator' }}</span>
                        <span class="user-role">Super Admin</span>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </form>
            </div>
        </div>
        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif
            @yield('content')
        </div>
    </div>

    <!-- Developer Credit Bar -->
    <div style="background: #0a0a0a; border-top: 1px solid rgba(255,255,255,0.05); text-align: center; padding: 8px; font-size: 0.72rem; color: #4b5563; font-family: 'Inter', sans-serif; position: fixed; bottom: 0; width: 100%; z-index: 99;">
        &copy; {{ date('Y') }} {{ $storeName }} &nbsp;|&nbsp; Designed &amp; Developed by
        <strong style="color: #c00a27;">Chandra Yudhia Pratidina</strong>
        &nbsp;|&nbsp; Hak Cipta Dilindungi Undang-Undang No. 28 Tahun 2014
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Sidebar Toggle
            const menuToggle = document.querySelector('.menu-toggle');
            const sidebar = document.querySelector('.sidebar');
            if(menuToggle && sidebar) {
                menuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                });
            }

            // Dropdown Toggle
            const notifLink = document.getElementById('notificationLink');
            const notifDropdown = document.getElementById('notificationDropdown');
            
            notifLink.addEventListener('click', function(e) {
                e.preventDefault();
                if(notifDropdown.style.display === 'none') {
                    notifDropdown.style.display = 'block';
                    setTimeout(() => {
                        notifDropdown.style.opacity = '1';
                        notifDropdown.style.transform = 'scale(1)';
                    }, 10);
                } else {
                    notifDropdown.style.opacity = '0';
                    notifDropdown.style.transform = 'scale(0.95)';
                    setTimeout(() => { notifDropdown.style.display = 'none'; }, 200);
                }
            });
            
            // Close if clicked outside
            document.addEventListener('click', function(e) {
                if(!notifLink.contains(e.target) && !notifDropdown.contains(e.target)) {
                    notifDropdown.style.opacity = '0';
                    notifDropdown.style.transform = 'scale(0.95)';
                    setTimeout(() => { notifDropdown.style.display = 'none'; }, 200);
                }
            });

            // Hover effect for list items using delegated event
            document.getElementById('notificationList').addEventListener('mouseover', function(e) {
                const item = e.target.closest('.notif-item');
                if(item) item.style.background = 'rgba(255, 255, 255, 0.05)';
            });
            document.getElementById('notificationList').addEventListener('mouseout', function(e) {
                const item = e.target.closest('.notif-item');
                if(item) item.style.background = 'transparent';
            });

            window.dismissNotif = function(event, id) {
                event.preventDefault();
                event.stopPropagation();
                let dismissed = JSON.parse(localStorage.getItem('dismissed_notifs') || '[]');
                if(!dismissed.includes(id)) {
                    dismissed.push(id);
                    localStorage.setItem('dismissed_notifs', JSON.stringify(dismissed));
                }
                fetchNotifications(); // re-render
            };

            let previousCount = 0;
            function fetchNotifications() {
                fetch('{{ route("admin.api.notifications") }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.ok ? response.json() : {notifications: []})
                .then(data => {
                    const badge = document.getElementById('notificationBadge');
                    const list = document.getElementById('notificationList');
                    const countText = document.getElementById('notificationCountText');
                    
                    let dismissed = JSON.parse(localStorage.getItem('dismissed_notifs') || '[]');
                    let activeNotifs = data.notifications.filter(item => {
                        let id = item.title + '|' + item.message;
                        return !dismissed.includes(id);
                    });
                    
                    let count = activeNotifs.length;

                    if (count > 0) {
                        badge.style.display = 'block';
                        badge.innerText = count;
                        countText.innerText = count + ' Peringatan';
                        
                        if(count > previousCount) {
                            badge.style.transform = 'scale(1.4)';
                            setTimeout(() => { badge.style.transform = 'scale(1)'; }, 300);
                        }
                        
                        let html = '';
                        activeNotifs.forEach(item => {
                            let id = item.title + '|' + item.message;
                            html += `
                            <a href="${item.link}" class="notif-item" style="display: flex; position: relative; padding: 15px 20px; border-bottom: 1px solid #374151; text-decoration: none; transition: background 0.2s;">
                                <div style="font-size: 1.3rem; margin-right: 15px; display: flex; align-items: flex-start; margin-top: 2px;">${item.icon}</div>
                                <div style="flex: 1; padding-right: 20px;">
                                    <div style="color: #fff; font-size: 0.85rem; font-weight: 600; margin-bottom: 3px;">${item.title}</div>
                                    <div style="color: #9ca3af; font-size: 0.8rem; line-height: 1.4;">${item.message}</div>
                                    <div style="color: #6b7280; font-size: 0.7rem; margin-top: 6px;"><i class="fa-regular fa-clock"></i> ${item.time}</div>
                                </div>
                                <button onclick="dismissNotif(event, '${id.replace(/'/g, "\\'")}')" title="Hapus Notifikasi" style="position: absolute; right: 15px; top: 15px; background: transparent; border: none; color: #6b7280; cursor: pointer; font-size: 1.1rem; transition: color 0.2s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#6b7280'"><i class="fa-solid fa-times"></i></button>
                            </a>
                            `;
                        });
                        list.innerHTML = html;
                        
                    } else {
                        badge.style.display = 'none';
                        badge.innerText = '0';
                        countText.innerText = 'Aman';
                        list.innerHTML = `
                            <div style="padding: 30px 20px; text-align: center; color: #9ca3af; font-size: 0.85rem;">
                                <i class="fa-solid fa-shield-check" style="font-size: 2rem; margin-bottom: 10px; color: #10b981;"></i><br>
                                Sistem beroperasi normal.<br>Tidak ada peringatan kritis.
                            </div>
                        `;
                    }
                    previousCount = data.count;
                })
                .catch(error => console.error("Error fetching notifications:", error));
            }
            
            // Initial fetch
            fetchNotifications();
            
            // Poll every 15 seconds
            setInterval(fetchNotifications, 15000);
        });
    </script>
</body>
</html>
