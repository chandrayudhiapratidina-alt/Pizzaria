<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;0,900;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --bg-dark: #111111;
            --primary: #E8304A;
            --text-warm: #F5F0E8;
            --gold: #C9A84C;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-warm);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
            position: relative;
        }
        /* Background decorative elements */
        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(232, 48, 74, 0.15) 0%, rgba(232, 48, 74, 0) 70%);
            top: -150px;
            left: -150px;
            border-radius: 50%;
            z-index: 0;
        }
        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(201, 168, 76, 0.1) 0%, rgba(201, 168, 76, 0) 70%);
            bottom: -100px;
            right: -100px;
            border-radius: 50%;
            z-index: 0;
        }

        .error-container {
            position: relative;
            z-index: 1;
            max-width: 600px;
            padding: 40px;
        }
        .error-code {
            font-family: 'Playfair Display', serif;
            font-size: 8rem;
            font-weight: 900;
            color: var(--primary);
            margin: 0;
            line-height: 1;
            text-shadow: 0 10px 30px rgba(232, 48, 74, 0.3);
        }
        .error-icon {
            font-size: 4rem;
            color: var(--gold);
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 2rem;
            font-weight: 700;
            margin: 10px 0 20px;
        }
        .error-desc {
            color: #aaa;
            font-size: 1.1rem;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        .btn-home {
            display: inline-block;
            background: var(--primary);
            color: #fff;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(232, 48, 74, 0.3);
        }
        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(232, 48, 74, 0.5);
            background: #ff4757;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon"><i class="fa-solid fa-pizza-slice"></i></div>
        <h1 class="error-code">404</h1>
        <div class="error-title">Oops! Halaman Tidak Ditemukan</div>
        <p class="error-desc">Maaf, sepertinya sepotong pizza yang Anda cari telah habis atau halaman ini sudah dipindahkan ke oven lain.</p>
        <a href="{{ route('client.guest.catalog') }}" class="btn-home">Kembali ke Menu Utama</a>
    </div>
</body>
</html>
