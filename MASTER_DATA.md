# MASTER DATA SISTEM PIZZARIA

Dokumen ini menjelaskan master data utama yang digunakan dalam sistem Pizzaria, meliputi entitas referensi dan konfigurasi yang menjadi dasar operasional aplikasi.

## 1. Master Data Utama

### 1.1 Users
Master data pengguna mencakup semua akun yang terdaftar dalam sistem, baik Admin, Kasir, maupun Pelanggan.
- `id`
- `name`
- `email`
- `password`
- `role` (`admin`, `cashier`, `client`)
- `phone_number`
- `created_at`
- `updated_at`

### 1.2 Categories
Kategori menu yang digunakan untuk mengelompokkan produk makanan.
- `id`
- `name`
- `slug`
- `description`
- `sort_order`
- `created_at`
- `updated_at`

### 1.3 Menus
Data menu produk yang ditampilkan di katalog dan dapat dipesan.
- `id`
- `category_id`
- `name`
- `description`
- `price`
- `is_available`
- `image_url`
- `created_at`
- `updated_at`

### 1.4 Ingredients
Bahan baku yang dimiliki restoran untuk pembuatan menu.
- `id`
- `name`
- `stock_qty`
- `unit` (contoh: gram, pcs)
- `minimum_stock_alert`
- `created_at`
- `updated_at`

### 1.5 Customizations
Opsi tambahan atau modifikasi yang bisa dipilih pelanggan untuk setiap menu.
- `id`
- `menu_id`
- `type` (contoh: topping, size, crust)
- `name`
- `additional_price`
- `ingredient_id` (opsional)
- `ingredient_qty`
- `created_at`
- `updated_at`

### 1.6 Promotions
Data promosi yang bisa diterapkan pada transaksi pembelian.
- `id`
- `code`
- `description`
- `discount_type` (`fixed`, `percentage`)
- `discount_value`
- `start_date`
- `end_date`
- `quota`
- `used_count`
- `is_active`
- `created_at`
- `updated_at`

### 1.7 Tables
Data meja untuk pemesanan dine-in.
- `id`
- `table_number`
- `status`
- `qr_code`
- `created_at`
- `updated_at`

### 1.8 Settings
Konfigurasi sistem yang bersifat global.
- `id`
- `key`
- `value`
- `created_at`
- `updated_at`

## 2. Entitas Master Tambahan

### 2.1 Product Reviews
Data ulasan menu oleh pelanggan.
- `id`
- `user_id`
- `menu_id`
- `rating`
- `comment`
- `is_approved`
- `created_at`
- `updated_at`

### 2.2 Store Reviews
Data ulasan terkait pengalaman umum restoran.
- `id`
- `user_id`
- `rating`
- `comment`
- `is_approved`
- `created_at`
- `updated_at`

### 2.3 Promotion Redemptions
Riwayat penggunaan kode promo.
- `id`
- `user_id`
- `promotion_id`
- `order_id`
- `discount_applied`
- `status`
- `created_at`
- `updated_at`

## 3. Hubungan Antar Master Data

- `categories` mengelompokkan `menus`.
- `menus` dapat memiliki banyak `customizations`.
- `ingredients` dapat terkait dengan `customizations` untuk mengurangi stok saat pesanan diproses.
- `promotions` dapat digunakan oleh `promotion_redemptions` untuk mencatat diskon yang diterapkan.
- `tables` digunakan oleh pesanan dine-in untuk lokasi fisik pelanggan.

## 4. Catatan Penting

- Master data `menus`, `categories`, dan `ingredients` adalah basis referensi bagi alur operasional restoran.
- Data promosi harus dijaga agar kuota, periode, dan status aktif selalu valid.
- Stok bahan baku (`ingredients`) harus konsisten dan diperbarui setiap kali pesanan diproses untuk mencegah overselling.

---

Dokumen ini dibuat untuk membantu tim pengembang dan manajemen memahami struktur master data dalam sistem Pizzaria.