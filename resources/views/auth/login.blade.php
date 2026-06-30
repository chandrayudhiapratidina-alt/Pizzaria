<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - {{ $storeName }} Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #E8304A;
            --primary-hover: #c23616;
            --dark: #0D0D0D;
            --light: #1A1A1A;
            --text-gray: #718093;
            --border-color: #333;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--dark);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Split Screen Container */
        .login-wrapper {
            display: flex;
            width: 100%;
            height: 100vh;
        }

        /* Left Branding Panel */
        .branding-panel {
            flex: 1.2;
            background: linear-gradient(135deg, rgba(13,13,13,0.95), rgba(26,26,26,0.95)),
                        url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjMWUyNzJlIiAvPgo8cGF0aCBkPSJNMCAwTDggOFpNOCAwTDAgOFoiIHN0cm9rZT0iIzJmMzU0MiIgc3Ryb2tlLXdpZHRoPSIxIi8+Cjwvc3ZnPg==');
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 5rem;
            position: relative;
            overflow: hidden;
        }

        /* Abstract glowing circles evoking a pizza oven/warmth */
        .branding-panel::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(232, 48, 74, 0.15) 0%, rgba(232, 48, 74, 0) 70%);
            top: -200px;
            left: -200px;
            border-radius: 50%;
            z-index: 1;
        }
        
        .branding-panel::after {
            content: '';
            position: absolute;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(251, 197, 49, 0.1) 0%, rgba(251, 197, 49, 0) 70%);
            bottom: -300px;
            right: -200px;
            border-radius: 50%;
            z-index: 1;
        }

        .brand-content {
            position: relative;
            z-index: 2;
        }

        .logo-title {
            font-size: 4rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 1rem;
            letter-spacing: -1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-title span {
            color: var(--primary);
        }

        .logo-subtitle {
            font-size: 1.25rem;
            color: #dcdde1;
            font-weight: 300;
            line-height: 1.6;
            max-width: 400px;
        }

        /* Right Form Panel */
        .form-panel {
            flex: 1;
            background: var(--dark);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }

        .form-container {
            width: 100%;
            max-width: 420px;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-header {
            margin-bottom: 2.5rem;
        }

        .form-header h2 {
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: var(--text-gray);
            font-size: 1rem;
        }

        /* Inputs */
        .input-group {
            margin-bottom: 1.5rem;
        }

        .input-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: #ccc;
            margin-bottom: 0.5rem;
        }

        .input-group input {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            font-family: inherit;
            font-size: 1rem;
            color: #fff;
            background: var(--light);
            transition: all 0.3s ease;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--light);
            box-shadow: 0 0 0 4px rgba(232, 48, 74, 0.1);
        }

        .input-group input::placeholder {
            color: #a4b0be;
        }

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-gray);
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            accent-color: var(--primary);
            width: 16px;
            height: 16px;
        }

        .btn-submit {
            width: 100%;
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 1rem;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(232, 65, 24, 0.25);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .error-message {
            background: rgba(214, 48, 49, 0.1);
            color: #ff7675;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(214, 48, 49, 0.3);
        }

        .credentials {
            margin-top: 2rem;
            padding: 1.5rem;
            background: var(--light);
            border-radius: 12px;
            font-size: 0.85rem;
            color: var(--text-gray);
            border: 1px dashed var(--border-color);
        }

        .credentials h4 {
            color: #ccc;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .credentials ul {
            list-style: none;
            padding: 0;
            margin: 0;
            line-height: 1.6;
        }
        
        .credentials span {
            font-weight: 600;
            color: #fff;
        }

        .link-text {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: var(--text-gray);
        }
        .link-text a {
            color: var(--primary);
            text-decoration: none;
            font-weight: bold;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .branding-panel {
                display: none;
            }
            .form-panel {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="branding-panel">
            <div class="brand-content">
                <div class="logo-title" style="display: flex; flex-direction: column; align-items: flex-start; gap: 15px;">
                    @php
                        $storeLogo = \App\Models\Setting::get('store_logo');
                        $storeName = \App\Models\Setting::get('store_name', 'PIZZARIA');
                    @endphp
                    @if($storeLogo)
                        <img src="{{ Storage::url($storeLogo) }}" alt="{{ $storeName }}" style="max-height: 80px; max-width: 250px; object-fit: contain; display: block; border-radius: 8px;">
                    @else
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"></path>
                            <path d="M12 2v10l5.5 5.5"></path>
                            <circle cx="14.5" cy="8.5" r="1.5" fill="var(--primary)"></circle>
                            <circle cx="8.5" cy="10.5" r="1.5" fill="var(--primary)"></circle>
                            <circle cx="10.5" cy="15.5" r="1.5" fill="var(--primary)"></circle>
                        </svg>
                    @endif
                    <div style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 2.2rem; letter-spacing: -0.5px; text-transform: uppercase; line-height: 1;">
                        <span style="color: {{ $brandColorFirst }};">{{ $storeNameFirst }}</span><span style="color: {{ $brandColorSecond }};">{{ $storeNameSecond }}</span>
                    </div>
                </div>
                <p class="logo-subtitle">Sistem Manajemen Terpadu untuk pengalaman mengelola restoran yang lebih baik, efisien, dan modern.</p>
            </div>
        </div>
        
        <div class="form-panel">
            <div class="form-container">
                <div class="form-header">
                    <h2>Selamat Datang</h2>
                    <p>Silakan masuk ke akun Anda.</p>
                </div>

                @if($errors->any())
                    <div class="error-message">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="contoh: admin@pizzaria.com" required autofocus>
                    </div>
                    
                    <div class="input-group">
                        <label for="password">Kata Sandi</label>
                        <input type="password" id="password" name="password" placeholder="Masukkan kata sandi Anda" required>
                    </div>

                    <div class="options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember"> Ingat Saya
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">
                        Masuk
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </button>
                </form>

                <div class="link-text">
                    Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
                </div>

                <div class="credentials">
                    <h4>Informasi Kredensial (Demo)</h4>
                    <ul>
                        <li><span>Admin:</span> admin@pizzaria.com</li>
                        <li><span>Kasir:</span> cashier@pizzaria.com</li>
                        <li><span>Sandi:</span> password</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
