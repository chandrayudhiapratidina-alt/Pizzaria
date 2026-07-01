# DOKUMENTASI SISTEM KEAMANAN PIZZARIA

Dokumen ini merangkum fitur keamanan yang tersedia di kode sumber Pizzaria berdasarkan implementasi saat ini.

## 1. Otentikasi dan Password

1. **Hash Password**
   - Password pengguna disimpan menggunakan `Hash::make(...)`.
   - Implementasi ditemukan di `app/Http/Controllers/AuthController.php`, `app/Http/Controllers/Admin/StaffController.php`, dan `app/Http/Controllers/Client/CustomerProfileController.php`.

2. **Login dengan Laravel Auth**
   - Autentikasi menggunakan `Auth::attempt(...)` untuk memeriksa email dan password.
   - Jika login berhasil, session di-regenerate untuk mencegah *session fixation*.
   - Ditemukan di `app/Http/Controllers/AuthController.php`.

3. **Logout yang Aman**
   - Proses logout memanggil `Auth::logout()`, `session()->invalidate()`, dan `session()->regenerateToken()`.
   - Ini memastikan token CSRF lama tidak dapat digunakan kembali.

## 2. Otorisasi Role-Based Access Control (RBAC)

1. **Middleware Role**
   - `app/Http/Middleware/RoleMiddleware.php` memeriksa apakah pengguna memiliki role yang sesuai.
   - Jika tidak terautentikasi, pengguna diarahkan ke `/login`.
   - Jika role tidak sesuai, aplikasi mengembalikan `abort(403, 'Unauthorized Access')`.

2. **Route Proteksi**
   - Route admin dilindungi oleh `middleware(['auth', 'role:admin'])`.
   - Route cashier dilindungi oleh `middleware(['auth', 'role:cashier'])`.
   - Route online checkout terproteksi dengan `middleware('auth')`.
   - Ditemukan di `routes/web.php`.

## 3. Proteksi CSRF

1. **CSRF Token pada Form**
   - Hampir semua formulir Blade menggunakan `@csrf`.
   - Termasuk halaman login, register, profile, checkout, admin form, dan delete action.

2. **Token CSRF di AJAX**
   - AJAX request menggunakan header `'X-CSRF-TOKEN': '{{ csrf_token() }}'` atau menyisipkan token lewat form data.
   - Ditemukan pada view seperti `resources/views/cashier/pos/index.blade.php` dan `resources/views/client/layouts/app.blade.php`.

## 4. Validasi Input dan Sanitasi

1. **Validasi Request**
   - Banyak controller menggunakan `$request->validate([...])` untuk memeriksa tipe dan batas nilai input.
   - Ditemukan di `app/Http/Controllers/AuthController.php` dan banyak controller lain seperti `ClientMenuController`, `Adminood Controllers`, dan `CheckoutRequest`.

2. **FormRequest / Custom Validation**
   - Beberapa alur checkout menggunakan `FormRequest` untuk memberlakukan aturan validasi terpusat.
   - Ini membantu menjaga konsistensi validasi dan meminimalkan input berbahaya.

## 5. Keamanan Pembayaran dan Webhook

1. **Validasi Signature Midtrans**
   - `app/Http/Controllers/Api/MidtransWebhookController.php` menghitung signature dengan:
     `hash('sha512', order_id . status_code . gross_amount . server_key)`.
   - Jika signature tidak sesuai, webhook ditolak dengan respons 403.

2. **Audit & Logging**
   - Webhook Midtrans mencatat event masuk, pesanan tidak ditemukan, signature invalid, atau perubahan status.
   - Ini berguna untuk investigasi keamanan dan audit transaksi.

3. **Fallback Order Lookup Aman**
   - Webhook mencari order berdasarkan `order_number` terlebih dahulu, lalu menggunakan format legacy.
   - Ini mencegah proses pembayaran diproses untuk order yang tidak ada.

## 6. Keamanan Session dan Cookie

1. **Regenerasi Session setelah Login**
   - Session pengguna dibuat ulang setelah login berhasil.
   - Ini membantu melindungi dari serangan *session fixation*.

2. **Regenerasi CSRF Token setelah Logout**
   - Setelah logout, token CSRF di-regenerate agar tidak dapat dipakai kembali.

## 7. Pengelolaan Kunci Rahasia dan Konfigurasi

1. **Konfigurasi Rahasia di `.env`**
   - Aplikasi menggunakan nilai `APP_KEY`, `MIDTRANS_SERVER_KEY`, dan kredensial lainnya dari environment.
   - `.env` harus dijaga agar tidak termasukkan ke repo publik.

2. **Midtrans dan Biteship Key**
   - Kunci server/klien Midtrans diambil dari `config('midtrans.server_key')` atau `Setting::get(...)`.
   - Ini menjaga kredensial sensitif tetap berada di konfigurasi lingkungan.

## 8. Perlindungan Tambahan yang Terlihat

1. **CSRF untuk Form Delete**
   - Form penghapusan menggunakan `@csrf @method('DELETE')`.

2. **Proteksi Akses Halaman**
   - Route admin, route cashier, dan beberapa route client yang sensitif memakai middleware `auth`.

## 9. Catatan Kelemahan / Area untuk Diperbaiki

1. **Akses Order Tamu Publik**
   - Route publik untuk `client/orders/{id}`, `orders/{order_number}/receipt`, dan `api/orders/{id}/status` tampak tidak memiliki validasi kepemilikan order.
   - Ini dapat menyebabkan data status pesanan bocor jika ID dapat ditebak.

2. **Rate Limiting Tidak Terlihat**
   - Tidak ada bukti langsung penggunaan rate limiter untuk login atau API publik di kode yang ditinjau.
   - Menambahkan rate limiting akan membantu melindungi dari brute-force dan scraping.

3. **Otorisasi Fine-Grained**
   - Saat ini, beberapa route `auth` mungkin perlu diperkuat dengan pemeriksaan bahwa user benar-benar berhak mengakses resource tertentu (misal data order milik user sendiri).

---

Dokumen ini bertujuan memberi gambaran keseluruhan mekanisme keamanan yang sudah ada di kode sumber Pizzaria. Untuk pengembangan lebih lanjut, fokuskan pada validasi kepemilikan resource dan rate limiting API.
