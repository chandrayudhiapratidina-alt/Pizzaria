# 🔍 Audit E-Commerce Pizzaria — QR (Dine-In) vs Web (Online)

## Executive Summary

Secara arsitektural, sistem Pizzaria **sudah memiliki pemisahan yang sangat baik** antara alur Dine-In (QR Code) dan Online (Web Delivery/Pickup). Namun ada **7 temuan kritis** dan **5 temuan minor** yang perlu diperbaiki untuk menjadikan sistem ini siap produksi.

---

## ✅ Yang Sudah Benar (Perbedaan QR vs Web)

| Aspek | QR / Dine-In | Web / Online |
|-------|-------------|-------------|
| **Entry Point** | `?table=X` → set `session('table_id')` | `/client/online` → pilih Delivery/Pickup |
| **Checkout Controller** | `ClientMenuController@checkout` | `OnlineOrderController@processCheckout` |
| **Pembayaran** | ✅ Post-paid (bayar di kasir setelah makan) | ✅ Pre-paid (wajib bayar via Midtrans) |
| **Status Awal** | `order_status: cooking`, `payment: unpaid` | `order_status: pending_payment`, `payment: pending` |
| **Add-on Order** | ✅ Bisa tambah pesanan ke meja yang sama | ❌ Tidak ada (sesuai — setiap order terpisah) |
| **Expiry Timer** | ❌ Tidak ada (benar — bayar nanti) | ✅ 15 menit countdown |
| **Stepper** | Pesanan → Dimasak → Disajikan | Bayar → Dimasak → Dikirim/Diambil |
| **Halaman Status** | `order_status.blade.php` | `online_order_status.blade.php` |
| **Pembatalan** | ✅ Hanya jika belum dimasak DAN belum bayar | ✅ Hanya jika belum bayar |

> [!NOTE]
> Arsitektur dua-controller (ClientMenuController + OnlineOrderController) sudah sangat tepat. Validasi (CheckoutRequest vs OnlineCheckoutRequest) juga sudah terpisah dengan benar.

---

## 🚨 Temuan Kritis

### 1. Cart Drawer Checkout Tidak Sadar Mode (Severity: HIGH)

**File:** [app.blade.php](file:///d:/EXAMPP/htdocs/Pizzaria/resources/views/client/layouts/app.blade.php#L566-L579)

**Masalah:** Tombol checkout di cart drawer **selalu mengarah ke `client.online.checkout`** (checkout online), bahkan ketika pelanggan sedang dalam mode **Dine-In (QR)**. Pelanggan yang scan QR code dan sudah punya `session('table_id')` seharusnya diarahkan ke alur Dine-In checkout, bukan Online checkout.

```php
// Saat ini (SALAH — selalu ke online checkout):
@auth
    @if(auth()->user()->role == 'client')
        <a href="{{ route('client.online.checkout') }}" class="cart-drawer-checkout-btn">Checkout Online</a>
    @endif
@else
    <button onclick="openAuthModal()" class="cart-drawer-checkout-btn">Checkout</button>
@endauth
```

**Seharusnya:**
```php
@if(session('table_id'))
    {{-- Dine-In: checkout langsung tanpa login --}}
    <button onclick="submitDineInCheckout()" class="cart-drawer-checkout-btn">
        🪑 Pesan (Meja {{ session('table_number') }})
    </button>
@elseif(in_array(session('order_mode'), ['delivery', 'pickup']))
    @auth
        <a href="{{ route('client.online.checkout') }}" class="cart-drawer-checkout-btn">Checkout Online</a>
    @else
        <button onclick="openAuthModal()" class="cart-drawer-checkout-btn">Checkout</button>
    @endauth
@else
    <button onclick="showModeSelection()" class="cart-drawer-checkout-btn">Checkout</button>
@endif
```

> [!CAUTION]
> Ini adalah bug **paling serius**. Pelanggan QR Dine-In tidak bisa checkout dari cart drawer!

---

### 2. Dine-In Checkout Tidak Ada Form/UI (Severity: HIGH)

**Masalah:** Route `client.guest.checkout` ada di `ClientMenuController@checkout`, tapi **tidak ada halaman/form checkout untuk Dine-In** di views. Checkout Dine-In langsung `POST` tapi tidak ada view yang memuat form (nama, nomor meja, dll). 

Sistem saat ini mengandalkan JavaScript `submitDineInCheckout()` yang melakukan POST secara langsung, namun fungsi itu **tidak ada** di [app.blade.php](file:///d:/EXAMPP/htdocs/Pizzaria/resources/views/client/layouts/app.blade.php).

**Rekomendasi:** Tambahkan fungsi JavaScript `submitDineInCheckout()` di layout yang melakukan POST ke `{{ route('client.guest.checkout') }}` dengan data `customer_name`, `order_type: dine_in`, dan `table_id` dari session.

---

### 3. Greeting Modal Muncul di Semua Halaman (Severity: MEDIUM)

**File:** [app.blade.php:640-648](file:///d:/EXAMPP/htdocs/Pizzaria/resources/views/client/layouts/app.blade.php#L640-L648)

**Masalah:** Greeting modal (pilih Delivery/Pickup) muncul di **setiap halaman** yang belum punya `order_mode`, termasuk halaman About, FAQ, Privacy, dll. Seharusnya hanya muncul di halaman Catalog.

```js
// Saat ini: SELALU muncul di semua halaman
document.addEventListener('DOMContentLoaded', () => {
    const currentMode = "{{ session('order_mode') }}";
    if (!currentMode) {
        setTimeout(() => {
            document.getElementById('greetingModalOverlay').classList.add('active');
        }, 500);
    }
});
```

---

### 4. Dine-In Cancel Logic Terlalu Ketat (Severity: MEDIUM)

**File:** [ClientMenuController.php:491-512](file:///d:/EXAMPP/htdocs/Pizzaria/app/Http/Controllers/ClientMenuController.php#L491-L512)

**Masalah:** Untuk pesanan Dine-In, status `cooking` masuk ke `$disallowedStatuses`, artinya **pesanan Dine-In TIDAK PERNAH bisa dibatalkan** karena statusnya langsung `cooking` saat checkout. 

```php
$disallowedStatuses = ['cooking', 'ready', 'served', 'on_delivery', 'completed', 'cancelled'];
```

Padahal seharusnya pesanan Dine-In yang baru saja dikirim (status `cooking` baru beberapa detik) masih logis untuk bisa dibatalkan sebelum dapur mulai memasak.

**Rekomendasi:** Tambahkan grace period (misal 2 menit) untuk pembatalan Dine-In, atau pisahkan logika cancel berdasarkan order_type.

---

### 5. `payment_status` Inkonsisten antara QR dan Web (Severity: MEDIUM)

| Status | Dine-In | Online |
|--------|---------|--------|
| Belum bayar | `unpaid` | `pending` |
| Dibatalkan | Bisa jadi `cancelled` | Bisa jadi `cancelled` |

**Masalah:** Dine-In menggunakan `unpaid` sedangkan Online menggunakan `pending`. Ini menyebabkan query-query yang filter berdasarkan payment_status harus selalu mempertimbangkan kedua string ini. Contoh di [order_status.blade.php:308](file:///d:/EXAMPP/htdocs/Pizzaria/resources/views/client/order_status.blade.php#L308):

```php
if (in_array($order->payment_status, ['pending', 'unpaid']) && ...)
```

**Rekomendasi:** Standardisasi `payment_status` — gunakan `unpaid` untuk semua yang belum bayar, dan `pending` hanya untuk yang sedang dalam proses pembayaran (menunggu verifikasi gateway).

---

### 6. Order Status Page Dine-In Referensi Route Yang Tidak Ada (Severity: HIGH)

**File:** [order_status.blade.php:566](file:///d:/EXAMPP/htdocs/Pizzaria/resources/views/client/order_status.blade.php#L566)

```php
'{{ route('client.guest.orders.success', $order->id) }}'
```

Route `client.guest.orders.success` **tidak ada** di `web.php`. Ini akan menyebabkan error 500 saat halaman dimuat jika order memiliki snap_token dan payment pending. Route yang benar adalah `client.guest.orders.payment-success`.

---

### 7. Dine-In Checkout Tidak Mendeduksi Stok (Severity: MEDIUM)

**File:** [ClientMenuController.php:303-476](file:///d:/EXAMPP/htdocs/Pizzaria/app/Http/Controllers/ClientMenuController.php#L303-L476)

**Masalah:** Checkout Dine-In **memverifikasi** stok tapi **tidak mendeduksi** `daily_stock` dan `ingredient.stock_qty` setelah order berhasil. Ini berarti stok tetap utuh meskipun sudah dipesan. 

`OnlineOrderController` juga memiliki masalah yang sama — verifikasi ada, deduksi tidak.

Deduksi stok kemungkinan dilakukan di tempat lain (Kitchen controller saat start-cooking?), tapi ini berisiko **overselling** jika banyak order masuk bersamaan sebelum dapur sempat klik "Start Cooking".

---

## ⚠️ Temuan Minor

### M1. CTA Banner Bahasa Campuran
**File:** [catalog.blade.php:604](file:///d:/EXAMPP/htdocs/Pizzaria/resources/views/client/catalog.blade.php#L604)
```html
<h2>Order Now & Get 20% Off Your First Order</h2>
```
Seluruh UI menggunakan Bahasa Indonesia, tapi CTA banner berbahasa Inggris.

### M2. Footer Menggunakan Alamat Placeholder
**File:** [app.blade.php:524](file:///d:/EXAMPP/htdocs/Pizzaria/resources/views/client/layouts/app.blade.php#L524)
```html
<p>Jl. Senopati No. 88, Jakarta</p>
```
Sedangkan koordinat restoran di OnlineOrderController menunjuk ke **Tegal** (lat: -6.8797, lng: 109.1256). Alamat tidak konsisten.

### M3. Greeting Modal Hanya Menawarkan Delivery/Pickup
**File:** [app.blade.php:619-630](file:///d:/EXAMPP/htdocs/Pizzaria/resources/views/client/layouts/app.blade.php#L619-L630)

Modal greeting hanya menampilkan opsi Delivery dan Pickup, **tanpa opsi Dine-In**. Pelanggan yang membuka web tanpa QR code tidak memiliki cara untuk memilih mode Dine-In selain scan QR. Ini sebenarnya benar secara bisnis (Dine-In hanya via QR), tapi bisa membingungkan pelanggan yang sudah duduk tapi belum scan.

### M4. `showWelcomeAd` Selalu True untuk QR
**File:** [ClientMenuController.php:30](file:///d:/EXAMPP/htdocs/Pizzaria/app/Http/Controllers/ClientMenuController.php#L30)
```php
$showWelcomeAd = true; // Selalu tampilkan iklan untuk testing
```
Komentar menunjukkan ini untuk testing. Di produksi, seharusnya mengecek apakah pelanggan sudah pernah melihat iklan (via session flag).

### M5. Tidak Ada Indikator Mode Aktif di UI
Setelah pelanggan memilih mode (Delivery/Pickup/Dine-In), **tidak ada indikator visual** di navbar atau halaman catalog yang menunjukkan mode aktif. Pelanggan bisa bingung apakah mereka dalam mode Delivery atau Pickup.

**Rekomendasi:** Tambahkan badge kecil di navbar seperti "🚗 Delivery Mode" atau "🪑 Meja 5".

---

## 📋 Prioritas Perbaikan

| # | Temuan | Severity | Effort |
|---|--------|----------|--------|
| 1 | Cart Drawer checkout tidak sadar mode | 🔴 HIGH | Medium |
| 2 | Dine-In checkout tidak ada UI/fungsi JS | 🔴 HIGH | Medium |
| 6 | Route `orders.success` tidak ada | 🔴 HIGH | Low |
| 4 | Cancel Dine-In tidak mungkin | 🟡 MEDIUM | Low |
| 5 | `payment_status` inkonsisten | 🟡 MEDIUM | Medium |
| 7 | Stok tidak dideduksi saat checkout | 🟡 MEDIUM | Medium |
| 3 | Greeting modal di semua halaman | 🟡 MEDIUM | Low |
| M5 | Tidak ada indikator mode aktif | 🟢 MINOR | Low |
| M1 | CTA bahasa campuran | 🟢 MINOR | Trivial |
| M2 | Alamat footer tidak konsisten | 🟢 MINOR | Trivial |

---

> [!IMPORTANT]
> **Temuan #1, #2, dan #6 harus diperbaiki segera** karena dapat menyebabkan error atau membuat pelanggan QR Dine-In tidak bisa menyelesaikan pesanan mereka.

Apakah Anda ingin saya mulai memperbaiki temuan-temuan ini?
