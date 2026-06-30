<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - {{ $storeName }}</title>
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
            color: #fff;
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

        /* Abstract glowing circles */
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
            color: #ccc;
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
            overflow-y: auto;
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
            box-shadow: 0 0 0 4px rgba(232, 48, 74, 0.1);
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
            box-shadow: 0 8px 20px rgba(232, 48, 74, 0.25);
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

        @media (max-width: 900px) {
            .branding-panel { display: none; }
            .form-panel { padding: 2rem; }
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
                    @endif
                    <div style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 2.2rem; letter-spacing: -0.5px; text-transform: uppercase; line-height: 1;">
                        <span style="color: {{ $brandColorFirst }};">{{ $storeNameFirst }}</span><span style="color: {{ $brandColorSecond }};">{{ $storeNameSecond }}</span>
                    </div>
                </div>
                <p class="logo-subtitle">Bergabunglah bersama kami untuk pengalaman memesan pizza yang cepat, mudah, dan penuh diskon spesial.</p>
            </div>
        </div>
        
        <div class="form-panel">
            <div class="form-container">
                <div class="form-header">
                    <h2>Daftar Akun Baru</h2>
                    <p>Lengkapi formulir di bawah untuk mendaftar.</p>
                </div>

                @if($errors->any())
                    <div class="error-message">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="John Doe" required autofocus>
                    </div>

                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" required>
                    </div>

                    <div class="input-group">
                        <label>No WhatsApp (Opsional)</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}" placeholder="08123456789">
                    </div>
                    
                    <div class="input-group">
                        <label>Kata Sandi</label>
                        <input type="password" name="password" placeholder="Minimal 8 karakter" required>
                    </div>

                    <div class="input-group">
                        <label>Konfirmasi Kata Sandi</label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi kata sandi" required>
                    </div>

                    <button type="submit" class="btn-submit">Daftar Sekarang</button>
                </form>

                <div class="link-text">
                    Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
