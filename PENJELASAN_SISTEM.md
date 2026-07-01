# PENJELASAN SISTEM PIZZARIA

## 1. Gambaran Umum Sistem
Sistem Pizzaria adalah sebuah aplikasi web terintegrasi yang menggabungkan fungsi *E-Commerce* dan *Point of Sales (POS)*. Sistem ini memungkinkan pelanggan untuk memesan pizza secara online dan memungkinkan staf restoran untuk memproses pesanan secara langsung di kasir.

Sistem terdiri dari tiga peran utama:
- **Admin**: Mengelola menu, bahan baku, promosi, staf, dan laporan penjualan.
- **Kasir**: Mengelola pesanan dine-in atau pesanan online yang telah dibayar, memperbarui status pesanan, dan mencetak struk.
- **Pelanggan**: Melihat katalog menu, menambahkan pesanan ke keranjang, checkout, dan melacak status pesanan.

## 2. Fitur Utama

### 2.1 Katalog Menu dan Kustomisasi
Pelanggan dapat melihat daftar produk menu berdasarkan kategori. Setiap menu dapat memiliki kustomisasi, seperti topping tambahan atau ukuran berbeda, yang mempengaruhi harga.

### 2.2 Keranjang Belanja dan Checkout
Pelanggan dapat menambahkan item ke keranjang dan memproses pembayaran. Sistem menghitung subtotal, diskon promosi, ongkos kirim (jika pengiriman), dan total akhir.

### 2.3 Otentikasi dan Peran Pengguna
Sistem menggunakan otentikasi Laravel untuk login dan registrasi. Setiap pengguna memiliki peran (`admin`, `cashier`, `client`) yang menentukan hak akses.

### 2.4 Status Pesanan dan Tracking
Setelah pesanan dibuat, pelanggan dapat melihat halaman status pesanan. Status pesanan dapat berubah dari `pending_payment` ke `confirmed`, `cooking`, dan `completed`.

### 2.5 Manajemen Inventaris dan Stok
Admin dan sistem secara otomatis memantau stok bahan baku. Ketika pesanan diproses, stok bahan baku berkurang sesuai dengan resep menu atau kustomisasi.

### 2.6 Integrasi Pembayaran dan Pengiriman
- **Pembayaran**: Sistem terhubung ke Midtrans untuk proses pembayaran online.
- **Pengiriman**: Sistem terhubung ke Biteship untuk menghitung ongkos kirim dan memesan kurir.

## 3. Arsitektur Sistem

### 3.1 Backend
Backend dibangun dengan **Laravel 12** menggunakan pola arsitektur **MVC (Model-View-Controller)**.
- **Model**: Menggunakan Eloquent ORM untuk interaksi database.
- **View**: Blade template menampilkan halaman HTML.
- **Controller**: Mengatur alur request, validasi, dan pemanggilan model/service.

### 3.2 Database
Database MySQL menyimpan data utama seperti pengguna, menu, pesanan, item pesanan, pembayaran, pengiriman, dan promosi.

### 3.3 External API
Sistem berkomunikasi dengan layanan eksternal:
- **Midtrans**: Untuk proses pembayaran.
- **Biteship**: Untuk layanan jasa pengiriman.

## 4. Alur Kerja Dasar

### 4.1 Alur Pelanggan
1. Pelanggan membuka katalog menu.
2. Pelanggan memilih menu dan kustomisasi.
3. Pelanggan menambahkan item ke keranjang.
4. Pelanggan mengisi data pemesanan dan memilih metode pembayaran.
5. Pelanggan melakukan pembayaran melalui Midtrans.
6. Jika pembayaran berhasil, pesanan masuk ke dapur/kasir.
7. Pelanggan dapat memantau status pesanan hingga selesai.

### 4.2 Alur Kasir
1. Kasir login ke dashboard POS.
2. Kasir melihat daftar pesanan yang sudah dibayar.
3. Kasir memproses pesanan dan mengurangi stok bahan baku.
4. Kasir menandai pesanan sebagai selesai dan mencetak struk.

### 4.3 Alur Admin
1. Admin login ke panel admin.
2. Admin mengelola menu, kategori, dan bahan baku.
3. Admin mengatur promosi dan melihat laporan penjualan.
4. Admin dapat menambah atau menghapus staf kasir.

## 5. Keamanan Sistem

### 5.1 Proteksi Autentikasi
Sistem menggunakan otentikasi Laravel untuk memastikan hanya pengguna terdaftar yang dapat mengakses halaman admin dan kasir.

### 5.2 Otorisasi Role-Based
Route admin dan kasir dilindungi oleh middleware `role`, sehingga hanya pengguna dengan peran yang tepat yang diberikan akses.

### 5.3 Perlindungan CSRF
Semua form yang mengubah data dilindungi oleh token CSRF untuk mencegah serangan *Cross-Site Request Forgery*.

### 5.4 Validasi Input
Semua input pengguna divalidasi menggunakan Laravel `Validator` agar data yang masuk ke database aman dan konsisten.

## 6. Kelebihan Sistem
- Integrasi pembayaran otomatis dengan Midtrans.
- Sistem manajemen stok yang terhubung langsung ke pesanan.
- Panel admin dan kasir terpisah untuk keamanan dan efisiensi.
- Fitur promosi dan voucher yang mendukung diskon otomatis.

## 7. Tantangan dan Catatan
- Fitur order tamu publik perlu disempurnakan untuk memastikan hanya pemilik pesanan yang dapat melihat status dan struk.
- Perlu diterapkan pelindung tambahan seperti rate limiting pada login dan API publik.

---

Dokumen ini menjelaskan sistem Pizzaria secara ringkas namun lengkap untuk memudahkan pemahaman fungsionalitas dan arsitektur aplikasi.
