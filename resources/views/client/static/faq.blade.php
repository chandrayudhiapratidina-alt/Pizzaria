@extends('client.layouts.app')
@section('title', 'FAQ - ' . $storeName)
@section('content')
<style>
    .static-container {
        max-width: 800px;
        margin: 0 auto;
        background: var(--card-bg);
        border: 1px solid #333;
        border-radius: 16px;
        padding: 40px;
    }
    .static-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        color: #fff;
        margin-bottom: 30px;
        text-align: center;
    }
    .faq-item {
        margin-bottom: 20px;
        border-bottom: 1px solid #333;
        padding-bottom: 15px;
    }
    .faq-item h4 {
        color: #fff;
        margin: 0 0 10px;
        font-size: 1.1rem;
    }
    .faq-item p {
        color: var(--gray);
        line-height: 1.6;
        margin: 0;
    }
</style>

<div class="static-container">
    <h1 class="static-title">Frequently Asked Questions (FAQ)</h1>
    
    <div class="faq-item">
        <h4>1. Bagaimana cara memesan secara online?</h4>
        <p>Anda dapat memilih menu melalui katalog kami, tambahkan ke keranjang, dan pilih mode "Delivery" atau "Pickup" saat checkout. Pastikan Anda telah masuk ke akun Anda.</p>
    </div>
    
    <div class="faq-item">
        <h4>2. Apakah ada batas jarak untuk pengiriman (Delivery)?</h4>
        <p>Saat ini, jangkauan pengiriman kami adalah maksimal 10 km dari lokasi restoran pusat kami.</p>
    </div>
    
    <div class="faq-item">
        <h4>3. Berapa lama waktu yang dibutuhkan untuk pesanan sampai?</h4>
        <p>Untuk mode Delivery, perkiraan waktu tiba adalah 30-45 menit tergantung lalu lintas dan kondisi cuaca. Untuk mode Pickup, pesanan biasanya siap dalam 15-20 menit.</p>
    </div>
    
    <div class="faq-item">
        <h4>4. Metode pembayaran apa saja yang tersedia?</h4>
        <p>Kami menerima Bank Transfer, e-Wallet (GoPay, ShopeePay), dan pembayaran via QRIS untuk pesanan online. Pesanan Dine-in dapat dibayar langsung di kasir.</p>
    </div>
    
    <div class="faq-item">
        <h4>5. Bisakah saya membatalkan pesanan?</h4>
        <p>Pesanan online yang sudah dibayar dan masuk status "Cooking" tidak dapat dibatalkan. Harap hubungi customer service kami jika ada kendala mendesak.</p>
    </div>
</div>
@endsection
