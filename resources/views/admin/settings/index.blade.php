@extends('admin.layouts.app')
@section('title', 'Pengaturan Pembayaran & Finansial')
@section('header', 'Pengaturan Pembayaran & Finansial')

@section('content')
<div class="settings-container">

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- KARTU UTAMA: PROFIL RESTORAN & OPERASIONAL -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-bottom: 30px;">
            <!-- SUB-KARTU 1.1: INFORMASI UMUM -->
            <div class="card" style="padding: 25px;">
                <h3 style="margin-bottom: 20px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-store" style="color: var(--primary);"></i> Profil & Jam Operasional Restoran
                </h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Nama Bagian Depan / Utama</label>
                        <input type="text" name="store_name_first" value="{{ old('store_name_first', $storeNameFirst) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;" required placeholder="Contoh: PIZZA">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Nama Bagian Belakang / Highlight</label>
                        <input type="text" name="store_name_second" value="{{ old('store_name_second', $storeNameSecond) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;" placeholder="Contoh: RIA">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Warna Nama Depan</label>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="color" id="brand_color_first" name="brand_color_first" value="{{ old('brand_color_first', $brandColorFirst) }}" style="width: 45px; height: 45px; border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; padding: 2px; background: white;" oninput="document.getElementById('brand_color_first_text').value = this.value">
                            <input type="text" id="brand_color_first_text" value="{{ old('brand_color_first', $brandColorFirst) }}" style="flex: 1; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; text-transform: uppercase;" placeholder="#FFFFFF" oninput="document.getElementById('brand_color_first').value = this.value">
                        </div>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Warna Nama Belakang (Highlight)</label>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="color" id="brand_color_second" name="brand_color_second" value="{{ old('brand_color_second', $brandColorSecond) }}" style="width: 45px; height: 45px; border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; padding: 2px; background: white;" oninput="document.getElementById('brand_color_second_text').value = this.value">
                            <input type="text" id="brand_color_second_text" value="{{ old('brand_color_second', $brandColorSecond) }}" style="flex: 1; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; text-transform: uppercase;" placeholder="#C00A27" oninput="document.getElementById('brand_color_second').value = this.value">
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr; margin-bottom: 15px;">
                    <small style="color: var(--text-muted); line-height: 1.4;">
                        <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i> <strong>Panduan Nama & Warna:</strong><br>
                        • Jika nama toko digabung seperti <strong>PIZZARIA</strong>, ketik <strong>PIZZA</strong> (warna depan) dan <strong>RIA</strong> (warna belakang).<br>
                        • Jika ingin menggunakan spasi seperti <strong>Pizza Mania</strong>, ketik <strong>Pizza </strong> (dengan spasi) dan <strong>Mania</strong>.<br>
                        • Kombinasi warna ini akan langsung digunakan pada judul navigasi, sidebar admin, kasir, struk belanja, dan halaman login/register.
                    </small>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Jam Buka / Operasional</label>
                        <input type="text" name="store_opening_hours" value="{{ old('store_opening_hours', $storeOpeningHours) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;" placeholder="Contoh: 10:00 - 22:00" required>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">No WhatsApp / Telepon</label>
                        <input type="text" name="store_phone" value="{{ old('store_phone', $storePhone) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;" required>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Email Toko</label>
                        <input type="email" name="store_email" value="{{ old('store_email', $storeEmail) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;" required>
                    </div>
                </div>
                
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Alamat Lengkap Restoran</label>
                    <textarea name="store_address" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; height: 100px; resize: vertical;" required>{{ old('store_address', $storeAddress) }}</textarea>
                </div>
            </div>

            <!-- KOLOM KANAN: LOGO & ESTIMASI WAKTU -->
            <div style="display: flex; flex-direction: column; gap: 30px;">
                <!-- SUB-KARTU 1.2: LOGO TOKO -->
                <div class="card" style="padding: 25px; display: flex; flex-direction: column; justify-content: space-between; height: fit-content;">
                    <div>
                        <h3 style="margin-bottom: 20px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                            <i class="fa-solid fa-image" style="color: #3b82f6;"></i> Logo Toko
                        </h3>
                        
                        <div style="text-align: center; margin-bottom: 10px; padding: 15px; background: #fafafa; border: 1px dashed var(--border-color); border-radius: 12px; height: 120px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            @if($storeLogo)
                                <img src="{{ Storage::url($storeLogo) }}" alt="Logo Restoran" style="max-height: 100px; max-width: 100%; object-fit: contain;">
                            @else
                                <div style="color: var(--text-muted); font-size: 0.9rem; display: flex; flex-direction: column; align-items: center; gap: 5px;">
                                    <i class="fa-solid fa-pizza-slice" style="font-size: 2rem; color: #ddd;"></i>
                                    <span>Logo Default (Teks)</span>
                                </div>
                            @endif
                        </div>

                        @if($storeLogo)
                            <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 8px; justify-content: center;">
                                <input type="checkbox" name="delete_logo" id="delete_logo" value="1" style="width: 16px; height: 16px; accent-color: var(--primary); cursor: pointer;">
                                <label for="delete_logo" style="color: var(--primary); font-weight: 600; cursor: pointer; font-size: 0.85rem; display: flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-trash-can"></i> Hapus Logo Saat Ini
                                </label>
                            </div>
                        @endif
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Unggah Logo Baru</label>
                        <input type="file" name="store_logo" accept="image/*" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.85rem;">
                        <small style="color: var(--text-muted); display: block; margin-top: 5px; font-size: 0.75rem;">Format JPG, PNG, atau SVG. Max 2MB.</small>
                    </div>
                </div>

                <!-- SUB-KARTU 1.3: ESTIMASI WAKTU -->
                <div class="card" style="padding: 25px; height: fit-content;">
                    <h3 style="margin-bottom: 20px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-clock" style="color: #f59e0b;"></i> Estimasi Waktu Proses
                    </h3>
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Makan di Tempat (Dine-in)</label>
                        <input type="text" name="estimated_time_dinein" value="{{ old('estimated_time_dinein', $estimatedTimeDinein) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;" placeholder="Contoh: 15 - 20 Menit" required>
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Pesanan Online / Takeaway</label>
                        <input type="text" name="estimated_time_online" value="{{ old('estimated_time_online', $estimatedTimeOnline) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;" placeholder="Contoh: 30 - 45 Menit" required>
                    </div>
                    <small style="color: var(--text-muted); display: block; margin-top: 10px; font-size: 0.8rem;">
                        Estimasi ini akan ditampilkan kepada pelanggan saat mereka melakukan pesanan.
                    </small>
                </div>

                <!-- SUB-KARTU 1.4: OTOMATISASI STATUS -->
                <div class="card" style="padding: 25px; height: fit-content;">
                    <h3 style="margin-bottom: 20px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-robot" style="color: #8b5cf6;"></i> Otomatisasi Status (Auto)
                    </h3>
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Batas Waktu Pembayaran (Menit)</label>
                        <input type="number" name="auto_payment_expiry_minutes" value="{{ old('auto_payment_expiry_minutes', $autoPaymentExpiryMinutes) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;" required>
                        <small style="color: var(--text-muted); display: block; margin-top: 5px; font-size: 0.75rem;">Waktu maksimal pesanan online dibayar sebelum otomatis batal.</small>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Waktu Tunggu ke "Diproses" (Detik)</label>
                        <input type="number" name="auto_process_seconds" value="{{ old('auto_process_seconds', $autoProcessSeconds) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;" required>
                        <small style="color: var(--text-muted); display: block; margin-top: 5px; font-size: 0.75rem;">Waktu dari pesanan Lunas hingga status otomatis jadi Diproses. (Isi 0 untuk manual)</small>
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Waktu Tunggu ke "Selesai" (Detik)</label>
                        <input type="number" name="auto_complete_seconds" value="{{ old('auto_complete_seconds', $autoCompleteSeconds) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;" required>
                        <small style="color: var(--text-muted); display: block; margin-top: 5px; font-size: 0.75rem;">Waktu dari Diproses hingga otomatis Selesai. (Isi 0 untuk manual)</small>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
            <!-- KARTU 1: TRANSFER BANK MANUAL & QRIS -->
            <div class="card" style="padding: 25px;">
                <h3 style="margin-bottom: 20px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-money-bill-transfer" style="color: var(--primary);"></i> Transfer Bank & QRIS
                </h3>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Nama Bank (Misal: BCA)</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $bankName) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Nomor Rekening</label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $bankAccountNumber) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Atas Nama (Pemilik Rekening)</label>
                    <input type="text" name="bank_account_owner" value="{{ old('bank_account_owner', $bankAccountOwner) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                </div>
                
                <div style="border-top: 1px dashed var(--border-color); padding-top: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 10px;">Gambar QRIS (Opsional)</label>
                    @if($qrisImage)
                        <div style="margin-bottom: 15px;">
                            <img src="{{ Storage::url($qrisImage) }}" alt="QRIS" style="max-width: 200px; border: 1px solid #ddd; border-radius: 10px; padding: 5px;">
                        </div>
                    @else
                        <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border: 1px dashed #ddd; border-radius: 8px; color: var(--text-muted); font-size: 0.9rem;">
                            Belum ada QRIS.
                        </div>
                    @endif
                    <input type="file" name="qris_image" accept="image/png, image/jpeg, image/jpg" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.9rem;">
                </div>
            </div>

            <!-- KARTU 2: MIDTRANS PAYMENT GATEWAY -->
            <div class="card" style="padding: 25px;">
                <h3 style="margin-bottom: 20px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-credit-card" style="color: #3b82f6;"></i> Payment Gateway (Midtrans)
                </h3>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Environment Mode</label>
                    <select name="midtrans_environment" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; font-weight: 600;">
                        <option value="sandbox" {{ $midtransEnvironment == 'sandbox' ? 'selected' : '' }}>Sandbox (Testing Mode)</option>
                        <option value="production" {{ $midtransEnvironment == 'production' ? 'selected' : '' }}>Production (Live / Asli)</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Server Key</label>
                    <input type="text" name="midtrans_server_key" value="{{ old('midtrans_server_key', $midtransServerKey) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; font-family: monospace;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Client Key</label>
                    <input type="text" name="midtrans_client_key" value="{{ old('midtrans_client_key', $midtransClientKey) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; font-family: monospace;">
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin-bottom: 30px;">
            <!-- KARTU 3: PAJAK -->
            <div class="card" style="padding: 25px;">
                <h3 style="margin-bottom: 20px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-file-invoice-dollar" style="color: #f59e0b;"></i> Pajak (Tax)
                </h3>
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Besaran PPN (%)</label>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <input type="number" step="0.01" min="0" max="100" name="tax_rate" value="{{ old('tax_rate', $taxRate) }}" style="width: 120px; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; font-weight: bold;">
                    <span style="font-weight: 600; color: var(--text-muted);">%</span>
                </div>
                <small style="display: block; color: var(--text-muted); margin-top: 8px;">Pajak akan otomatis dihitung pada setiap struk checkout.</small>
            </div>

            <!-- KARTU 4: INTEGRASI BITESHIP -->
            <div class="card" style="padding: 25px;">
                <h3 style="margin-bottom: 20px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-motorcycle" style="color: #9b59b6;"></i> Integrasi Biteship & Ongkos Kirim
                </h3>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Biteship API Key</label>
                    <input type="text" name="biteship_api_key" value="{{ old('biteship_api_key', $biteshipApiKey) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; font-family: monospace;" placeholder="biteship_test.eyJhbGci...">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Biaya Dasar (Rp)</label>
                        <input type="number" name="delivery_base_fee" value="{{ old('delivery_base_fee', \App\Models\Setting::get('delivery_base_fee', 5000)) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                        <small style="color: var(--text-muted);">Biaya tetap untuk jarak dasar.</small>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Jarak Dasar (km)</label>
                        <input type="number" name="delivery_base_distance_km" value="{{ old('delivery_base_distance_km', \App\Models\Setting::get('delivery_base_distance_km', 3)) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                        <small style="color: var(--text-muted);">Jarak tanpa biaya tambahan per km.</small>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Biaya per km (Rp)</label>
                        <input type="number" name="delivery_fee_per_km" value="{{ old('delivery_fee_per_km', \App\Models\Setting::get('delivery_fee_per_km', 2000)) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                        <small style="color: var(--text-muted);">Tambahan biaya per km setelah jarak dasar (fallback jika Biteship gagal).</small>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Maksimal Jarak (km)</label>
                        <input type="number" name="delivery_max_distance_km" value="{{ old('delivery_max_distance_km', \App\Models\Setting::get('delivery_max_distance_km', 20)) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                        <small style="color: var(--text-muted);">Di luar jarak ini tidak bisa delivery.</small>
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Biaya Penanganan / Markup (Rp)</label>
                    <input type="number" name="delivery_markup_fee" value="{{ old('delivery_markup_fee', $deliveryMarkupFee) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                    <small style="color: var(--text-muted);">Biaya tambahan (packing, dll) di atas tarif Biteship.</small>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Titik Restoran (Latitude)</label>
                        <input type="text" name="store_latitude" value="{{ old('store_latitude', $storeLatitude) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Titik Restoran (Longitude)</label>
                        <input type="text" name="store_longitude" value="{{ old('store_longitude', $storeLongitude) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                    </div>
                </div>
                <small style="display: block; margin-top: 10px; color: var(--text-muted);">Titik asal penjemputan pesanan. (Contoh: -6.8797, 109.1256)</small>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr; gap: 30px; margin-bottom: 30px;">
            <!-- KARTU 5: TARGET ANALITIK -->
            <div class="card" style="padding: 25px;">
                <h3 style="margin-bottom: 20px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-bullseye" style="color: #ef4444;"></i> Target Analitik & Penjualan
                </h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Target Harian (Rp)</label>
                        <input type="number" name="target_daily" value="{{ old('target_daily', $targetDaily) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Target Mingguan (Rp)</label>
                        <input type="number" name="target_weekly" value="{{ old('target_weekly', $targetWeekly) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Target Bulanan (Rp)</label>
                        <input type="number" name="target_monthly" value="{{ old('target_monthly', $targetMonthly) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                    </div>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr; gap: 30px; margin-bottom: 30px;">
            <!-- KARTU 6: BANNER PROMO UTAMA (POP-UP CAMPAIGN) -->
            <div class="card" style="padding: 25px;">
                <h3 style="margin-bottom: 20px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-bullhorn" style="color: var(--primary);"></i> Pengaturan Banner Promo Utama (Pop-up Campaign)
                </h3>
                
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px;">
                    Banner pop-up interaktif yang akan muncul di halaman depan untuk menampilkan promo kampanye terbaik Anda. Banner ini diatur agar muncul <strong>hanya sekali</strong> setiap sesi agar tidak mengganggu kenyamanan pengguna.
                </p>

                <div style="margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 15px; border: 2px solid var(--border-color); border-radius: 8px; background: #fafafa;">
                        <input type="checkbox" name="welcome_promo_active" value="1" {{ $welcomePromoActive == '1' ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: var(--primary);">
                        <div>
                            <div style="font-weight: 700; color: var(--text-main);">Aktifkan Banner Promo Utama</div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">Centang untuk memunculkan iklan promo pilihan ini di halaman depan (Landing Page).</div>
                        </div>
                    </label>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Ketik / Pilih Kode Promo yang Diiklankan</label>
                    <input type="text" name="welcome_promo_code" list="promoList" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;" placeholder="Pilih yang ada atau KETIK KODE BARU (misal: BUKAPUASA)" value="{{ $welcomePromoId ? \App\Models\Promotion::find($welcomePromoId)?->code : '' }}">
                    <datalist id="promoList">
                        @foreach($promotions as $promo)
                            <option value="{{ $promo->code }}">Diskon {{ $promo->discount_type == 'percentage' ? $promo->discount_value.'%' : 'Rp '.number_format($promo->discount_value) }}</option>
                        @endforeach
                    </datalist>
                    <small style="color: var(--text-muted); display: block; margin-top: 5px;">
                        <i class="fa-solid fa-magic" style="color: var(--primary);"></i> 
                        <strong>Fitur Smart Auto-Create:</strong> Jika Anda mengetik kode yang belum ada, sistem akan <strong>Otomatis Menciptakannya</strong> untuk Anda! Gunakan form "Quick Edit" di bawah untuk mengatur besaran diskonnya.
                    </small>
                </div>

                <div style="margin-bottom: 20px; border-left: 4px solid var(--primary); background: rgba(230, 57, 70, 0.05); padding: 15px; border-radius: 0 8px 8px 0;">
                    <h4 style="margin-top: 0; color: var(--primary); font-size: 0.95rem; margin-bottom: 5px;"><i class="fa-solid fa-bolt"></i> Quick Edit Promo & Tema <small style="font-weight:normal; color:var(--text-muted);">(Otomatis tersimpan ke Promo)</small></h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 15px;">Ubah besaran diskon, batas waktu, dan tampilan tema promo tanpa perlu pindah ke menu Promosi.</p>
                    
                    @php
                        $activePromoObj = $welcomePromoId ? \App\Models\Promotion::find($welcomePromoId) : null;
                    @endphp

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.85rem;">Tipe Diskon</label>
                            <select name="quick_promo_discount_type" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px;">
                                <option value="">-- Biarkan Sesuai Aslinya --</option>
                                <option value="percentage">Potongan Persen (%)</option>
                                <option value="fixed">Harga Potongan (Rp)</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.85rem;">Nilai Diskon</label>
                            <input type="number" name="quick_promo_discount_value" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px;" placeholder="Kosongkan jika tidak diubah">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.85rem;">Batas Waktu (Berakhir Pada)</label>
                            <input type="datetime-local" name="quick_promo_expires_at" value="{{ $activePromoObj && $activePromoObj->expires_at ? $activePromoObj->expires_at->format('Y-m-d\TH:i') : '' }}" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.85rem;">Minimal Belanja (Rp)</label>
                            <input type="number" name="quick_promo_min_order_amount" value="{{ $activePromoObj->min_order_amount ?? 0 }}" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px;" min="0">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.85rem;">Warna Tema (Hex Code)</label>
                            <input type="color" name="quick_promo_theme_color" value="{{ $activePromoObj->theme_color ?? '#E8304A' }}" style="width: 100%; height: 40px; padding: 2px; border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.85rem;">Ikon Tema (FontAwesome)</label>
                            <input type="text" name="quick_promo_icon" value="{{ $activePromoObj->icon ?? 'fa-bolt' }}" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px;" placeholder="e.g., fa-futbol">
                        </div>
                        <div style="grid-column: span 2;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.85rem;">Gambar Latar Belakang (Background Image)</label>
                            @if($activePromoObj && $activePromoObj->background_image)
                                <div style="margin-bottom: 10px;">
                                    <img src="{{ $activePromoObj->background_image }}" alt="Theme Background" style="height: 60px; border-radius: 6px; border: 1px solid var(--border-color); object-fit: cover;">
                                </div>
                            @endif
                            <input type="file" name="quick_promo_background_image" accept="image/*" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px; background: white; font-size: 0.85rem;">
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Judul Banner</label>
                        <input type="text" name="welcome_promo_title" value="{{ old('welcome_promo_title', $welcomePromoTitle) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;" placeholder="Contoh: Dapatkan Diskon 20%">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Sub-judul Banner</label>
                        <input type="text" name="welcome_promo_subtitle" value="{{ old('welcome_promo_subtitle', $welcomePromoSubtitle) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;" placeholder="Contoh: Pesan sekarang untuk pesanan pertama Anda.">
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-primary" style="padding: 15px 30px; font-size: 1.1rem; border-radius: 10px; display: inline-flex; align-items: center; gap: 10px; margin-bottom: 50px;">
            <i class="fa-solid fa-save"></i> Simpan Semua Pengaturan
        </button>
    </form>
</div>
@endsection
