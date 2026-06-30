@extends('client.layouts.app')
@section('title', 'Syarat dan Ketentuan - ' . $storeName)
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
    <h1 class="static-title">Syarat dan Ketentuan</h1>
    
    <p>Selamat datang di {{ $storeName }}. Dengan menggunakan layanan dan situs web kami, Anda menyetujui syarat dan ketentuan berikut ini. Harap baca dengan saksama.</p>
    
    <h3>1. Pemesanan dan Pembayaran</h3>
    <p>Semua pesanan yang dilakukan melalui platform online kami tunduk pada ketersediaan stok. Pembayaran harus dilakukan penuh pada saat pemesanan online, dan pesanan baru akan diproses (dimasak) setelah verifikasi pembayaran berhasil. Kami berhak menolak atau membatalkan pesanan jika terjadi kesalahan teknis atau dugaan penipuan.</p>
    
    <h3>2. Pengiriman dan Pengambilan</h3>
    <p>Waktu pengiriman dan pengambilan adalah estimasi dan dapat dipengaruhi oleh faktor eksternal seperti lalu lintas atau cuaca. Kami selalu berusaha untuk memenuhi jadwal, namun tidak bertanggung jawab atas keterlambatan di luar kendali kami. Untuk mode pengiriman, pelanggan harus memastikan alamat yang diberikan akurat dan dapat diakses.</p>
    
    <h3>3. Kebijakan Pengembalian dan Pembatalan (Return Policy)</h3>
    <p>Pesanan yang telah memasuki tahap persiapan (Cooking) tidak dapat dibatalkan atau dikembalikan dananya. Jika terdapat kesalahan pada pesanan Anda akibat kelalaian kami (misal: pesanan tertukar, kurang), silakan hubungi tim kami dalam waktu 1 jam sejak pesanan diterima untuk klaim kompensasi atau penggantian, dengan menyertakan bukti foto.</p>
    
    <h3>4. Ketersediaan Menu dan Harga</h3>
    <p>Harga dan ketersediaan menu dapat berubah sewaktu-waktu tanpa pemberitahuan sebelumnya. Kami berusaha untuk selalu memperbarui informasi pada situs web, namun jika pesanan Anda memuat item yang habis, kami akan menghubungi Anda untuk menawarkan alternatif atau pengembalian dana penuh untuk item tersebut.</p>
    
    <h3>5. Penggunaan Akun</h3>
    <p>Anda bertanggung jawab untuk menjaga kerahasiaan informasi akun dan kata sandi Anda. Semua aktivitas yang terjadi di bawah akun Anda adalah tanggung jawab Anda. Beritahu kami segera jika Anda mencurigai adanya penggunaan tidak sah atas akun Anda.</p>
    
    <p style="margin-top:30px; font-style:italic;">Terakhir diperbarui: Juni 2026</p>
</div>
@endsection
