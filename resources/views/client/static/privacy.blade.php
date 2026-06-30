@extends('client.layouts.app')
@section('title', 'Kebijakan Privasi - ' . $storeName)
@section('content')
<style>
    .static-container {
        max-width: 800px;
        margin: 0 auto;
        background: var(--card-bg);
        border: 1px solid #333;
        border-radius: 16px;
        padding: 40px;
        color: #ccc;
        line-height: 1.7;
    }
    .static-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        color: #fff;
        margin-bottom: 30px;
        text-align: center;
    }
    .static-container h3 {
        color: #fff;
        margin-top: 30px;
        margin-bottom: 10px;
    }
</style>

<div class="static-container">
    <h1 class="static-title">Kebijakan Privasi</h1>
    
    <p>Di {{ $storeName }}, kami sangat menghargai privasi Anda dan berkomitmen untuk melindungi data pribadi Anda. Kebijakan ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan menjaga informasi Anda.</p>
    
    <h3>1. Informasi yang Kami Kumpulkan</h3>
    <p>Kami dapat mengumpulkan informasi pribadi seperti nama, alamat email, nomor telepon, dan alamat pengiriman saat Anda mendaftar akun atau melakukan pesanan. Kami juga mencatat informasi transaksi dan riwayat pesanan Anda.</p>
    
    <h3>2. Penggunaan Informasi</h3>
    <p>Informasi yang dikumpulkan digunakan untuk:</p>
    <ul>
        <li>Memproses dan mengirimkan pesanan Anda.</li>
        <li>Mengelola akun dan preferensi Anda.</li>
        <li>Menghubungi Anda terkait status pesanan atau pembaruan layanan.</li>
        <li>Meningkatkan layanan dan pengalaman pelanggan kami.</li>
    </ul>
    
    <h3>3. Keamanan Data</h3>
    <p>Kami menerapkan langkah-langkah keamanan yang sesuai untuk melindungi data pribadi Anda dari akses yang tidak sah, perubahan, atau penghancuran. Semua transaksi pembayaran diproses melalui gateway pembayaran yang aman dan mematuhi standar industri.</p>
    
    <h3>4. Berbagi Informasi</h3>
    <p>Kami tidak akan menjual atau menyewakan informasi pribadi Anda kepada pihak ketiga. Informasi hanya dapat dibagikan dengan mitra pengiriman pihak ketiga secara ketat untuk memfasilitasi pengiriman pesanan Anda.</p>
    
    <h3>5. Hak Anda</h3>
    <p>Anda memiliki hak untuk mengakses, memperbarui, atau menghapus informasi pribadi Anda kapan saja melalui dashboard akun Anda. Jika Anda memerlukan bantuan lebih lanjut, silakan hubungi tim dukungan kami.</p>
    
    <p style="margin-top:30px; font-style:italic;">Terakhir diperbarui: Juni 2026</p>
</div>
@endsection
