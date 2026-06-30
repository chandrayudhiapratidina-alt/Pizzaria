# RENCANA UJI SISTEM BLACKBOX - PIZZARIA

## 1. Pendahuluan
Dokumen ini menyajikan rencana pengujian sistem dengan metode **Blackbox** untuk aplikasi Pizzaria. Pengujian dilakukan dari sisi pengguna tanpa melihat implementasi internal kode.

## 2. Tujuan Pengujian
- Memverifikasi bahwa fitur utama aplikasi berfungsi sesuai kebutuhan.
- Menguji alur sistem end-to-end dari tampilan pengguna hingga integrasi layanan eksternal.
- Menangkap cacat fungsionalitas berdasarkan keluaran yang terlihat dan input yang diberikan.

## 3. Lingkup Pengujian
Cakupan pengujian meliputi:
- Autentikasi dan otorisasi RBAC
- Manajemen staf dan menu
- Inventaris bahan baku
- Sistem pemesanan POS dan e-commerce
- Integrasi Biteship untuk pengiriman
- Pembayaran Midtrans dan alur checkout
- Notifikasi dan laporan admin

## 4. Lingkungan Pengujian
- Sistem: Laravel 12 / PHP 8.2
- Platform: Local Development XAMPP / staging
- Browser: Google Chrome, Firefox, atau Edge
- Koneksi: Internet untuk integrasi Midtrans dan Biteship

## 5. Data Pengujian / Akun Test
| Peran | Email | Password | Akses |
|---|---|---|---|
| Admin | admin@pizzaria.com | password | `/admin/dashboard` |
| Kasir | cashier@pizzaria.com | password | `/cashier/dashboard` |
| Customer | client@pizzaria.com | password | `/client/online/` |

## 6. Metode Blackbox
- Input diuji melalui form, URL, dan interaksi antarmuka.
- Keluaran dievaluasi dari response halaman, pesan validasi, status pesanan, dan tampilan data.
- Tidak menggunakan pengetahuan tentang struktur data internal atau kode aplikasi.

## 7. Kasus Uji Sistem

### 7.1 Autentikasi & Otorisasi
| ID | Skenario | Input | Hasil yang Diharapkan | Status |
|---|---|---|---|---|
| AUTH-01 | Login Admin valid | email/password Admin | Arahkan ke `/admin/dashboard` | [ ] |
| AUTH-02 | Login Kasir valid | email/password Kasir | Arahkan ke `/cashier/dashboard` | [ ] |
| AUTH-03 | Login gagal password salah | email valid + password salah | Muncul pesan error login | [ ] |
| AUTH-04 | Cek akses lintas peran | Login Kasir lalu buka `/admin/dashboard` | Akses ditolak / redirect | [ ] |
| AUTH-05 | Logout | Klik tombol logout | Kembali ke halaman login | [ ] |

### 7.2 Manajemen Staf
| ID | Skenario | Input | Hasil yang Diharapkan | Status |
|---|---|---|---|---|
| STF-01 | Tambah staf baru | Form data staf baru | Akun staf tampil di daftar | [ ] |
| STF-02 | Edit staf | Ubah nama atau email staf | Data staf terupdate | [ ] |
| STF-03 | Duplikasi email | Email sudah terdaftar | Error "Email sudah digunakan" | [ ] |
| STF-04 | Hapus staf lain | Delete akun staf non-admin | Akun terhapus dari daftar | [ ] |
| STF-05 | Hapus admin sendiri | Delete akun admin sendiri | Ditolak oleh sistem | [ ] |

### 7.3 Katalog Menu & Inventaris
| ID | Skenario | Input | Hasil yang Diharapkan | Status |
|---|---|---|---|---|
| MNU-01 | Tambah menu baru | Data menu lengkap | Menu muncul di katalog | [ ] |
| MNU-02 | Nonaktifkan menu | Set menu tidak tersedia | Tidak tampil di katalog pelanggan | [ ] |
| MNU-03 | Tambah bahan baku | Nama, stok, satuan | Bahan baku tersimpan | [ ] |
| MNU-04 | Tambah kustomisasi | Opsi tambah harga | Kustomisasi tampil di detail menu | [ ] |

### 7.4 Modul Kasir / POS
| ID | Skenario | Input | Hasil yang Diharapkan | Status |
|---|---|---|---|---|
| POS-01 | Buat pesanan baru | Pilih meja + menu + qty | Total harga dihitung benar | [ ] |
| POS-02 | Terapkan promo | Kode promo valid | Total berkurang sesuai diskon | [ ] |
| POS-03 | Bayar tunai | Bayar tunai, masukkan uang | Status `Paid`, kembalian benar | [ ] |
| POS-04 | Bayar QRIS | Metode QRIS | Status `Paid` tercatat | [ ] |
| POS-05 | Lihat riwayat transaksi | Buka halaman riwayat | Daftar transaksi tampil lengkap | [ ] |
| POS-06 | Cetak struk | Klik cetak | Tampilan struk lengkap | [ ] |

### 7.5 Manajemen Pesanan & Produksi
| ID | Skenario | Input | Hasil yang Diharapkan | Status |
|---|---|---|---|---|
| ORD-01 | Pesanan masuk antrean | Selesaikan checkout pesanan | Pesanan muncul di antrean | [ ] |
| ORD-02 | Proses pesanan | Klik `Proses Pesanan` | Status `Processing`, stok terpotong | [ ] |
| ORD-03 | Stok kustomisasi | Pesan dengan extra cheese | Stok bahan baku terpotong sesuai | [ ] |
| ORD-04 | Tandai siap | Klik `Ready` | Status `Ready` dan lanjut pengiriman | [ ] |

### 7.6 Pemesanan Online Customer
| ID | Skenario | Input | Hasil yang Diharapkan | Status |
|---|---|---|---|---|
| ONL-01 | Lihat katalog tanpa login | Buka katalog | Katalog tampil lengkap | [ ] |
| ONL-02 | Registrasi | Isi form pendaftaran | Akun `client` dibuat | [ ] |
| ONL-03 | Tambah kustomisasi | Pilih menu + opsi | Keranjang menampilkan tambahan | [ ] |
| ONL-04 | Klaim promo baru | Akun baru klaim promo | Promo berhasil terklaim | [ ] |
| ONL-05 | Kalkulasi ongkir | Checkout dengan alamat | Tarif Biteship tampil | [ ] |
| ONL-06 | Bayar via Midtrans | Checkout dengan pembayaran | Status `Paid` dan konfirmasi | [ ] |
| ONL-07 | Batalkan pesanan | Batalkan sebelum diproses | Status `Cancelled` | [ ] |
| ONL-08 | Beri ulasan | Submit rating & komentar | Ulasan tersimpan | [ ] |

### 7.7 Integrasi Biteship
| ID | Skenario | Input | Hasil yang Diharapkan | Status |
|---|---|---|---|---|
| SHP-01 | Request pickup | Pesanan `Ready` | Dapat `order_id` & `tracking_id` | [ ] |
| SHP-02 | Cetak waybill | Klik cetak label | Label pengiriman muncul | [ ] |
| SHP-03 | Tracking pelanggan | Buka detail pesanan | Timeline tracking tampil | [ ] |
| SHP-04 | Webhook delivery | Simulasi event delivery | Status berubah `Completed` | [ ] |

### 7.8 Analitik & Notifikasi
| ID | Skenario | Input | Hasil yang Diharapkan | Status |
|---|---|---|---|---|
| ADM-01 | Notifikasi stok kritis | Stok bahan baku di bawah batas | Notifikasi tampil | [ ] |
| ADM-02 | Moderasi ulasan | Ulasan 1 bintang | Admin dapat approve/hapus | [ ] |
| ADM-03 | Export PDF | Klik ekspor | File PDF terunduh | [ ] |
| ADM-04 | Export Excel | Klik ekspor | File XLSX terunduh | [ ] |
| ADM-05 | Target penjualan | Set target harian | Dashboard update | [ ] |

## 8. Pelaporan Hasil
Setiap kasus uji diisi `Passed` / `Failed` dan disertai catatan temuan.

## 9. Kesimpulan
Dokumen ini dapat digunakan sebagai panduan pelaksanaan uji sistem blackbox untuk proyek Pizzaria. Hasil pengujian direkomendasikan dicatat pada kolom `Status` dan `Catatan` jika ditemui bug atau perilaku tidak sesuai.
