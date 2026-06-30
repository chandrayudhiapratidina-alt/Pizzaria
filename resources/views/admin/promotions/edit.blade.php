@extends('admin.layouts.app')
@section('title', 'Edit Promo')
@section('content')
<div style="max-width: 750px;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.promotions.index') }}" style="color: var(--text-muted); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Promo
        </a>
    </div>

    <div class="card">
        <div style="padding: 20px 25px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="font-size: 1.3rem; font-weight: 800; color: var(--text-main);">Edit Promosi</h2>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 5px;">Perbarui detail promo <strong>{{ $promotion->code }}</strong>.</p>
            </div>
            <div style="font-family: 'Courier New', monospace; font-weight: 800; font-size: 1.1rem; color: var(--primary); background: #fef0f0; padding: 6px 16px; border-radius: 8px; letter-spacing: 1px;">
                {{ $promotion->code }}
            </div>
        </div>
        
        <form action="{{ route('admin.promotions.update', $promotion->id) }}" method="POST" enctype="multipart/form-data" style="padding: 25px;">
            @csrf
            @method('PUT')
            
            @if($errors->any())
                <div style="background:#f8d7da; color:#721c24; margin-bottom: 20px; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb;">
                    <ul style="margin:0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h3 style="margin-bottom: 15px; color: var(--text-main); border-bottom: 2px solid var(--border-color); padding-bottom: 10px;">Informasi Dasar Promo</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Kode Promo <span style="color: var(--primary);">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $promotion->code) }}" required style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: 'Courier New', monospace; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px;">
                </div>

                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Tipe Diskon <span style="color: var(--primary);">*</span></label>
                    <select name="discount_type" id="discountType" required style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem; background: white;">
                        <option value="percentage" {{ (old('discount_type') ?? $promotion->discount_type) == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                        <option value="fixed" {{ (old('discount_type') ?? $promotion->discount_type) == 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                    </select>
                </div>

                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Deskripsi (Internal)</label>
                    <input type="text" name="description" value="{{ old('description', $promotion->description) }}" style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                </div>
                
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Nilai Diskon <span style="color: var(--primary);">*</span></label>
                    <input type="number" name="discount_value" value="{{ old('discount_value', $promotion->discount_value) }}" required step="0.01" min="0" style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                </div>

                <div id="maxDiscountGroup">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Maks. Diskon (Rp)</label>
                    <input type="number" name="max_discount" value="{{ old('max_discount', $promotion->max_discount) }}" min="0" style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                </div>

                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Min. Order (Rp)</label>
                    <input type="number" name="min_order_amount" value="{{ old('min_order_amount', $promotion->min_order_amount) }}" min="0" style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                </div>

                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Batas Pemakaian</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit', $promotion->usage_limit) }}" placeholder="Kosongkan = tak terbatas" min="1" style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">Sudah digunakan: <strong>{{ $promotion->used_count }}x</strong></p>
                </div>

                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Mulai Berlaku</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $promotion->starts_at ? $promotion->starts_at->format('Y-m-d\TH:i') : '') }}" style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                </div>

                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Berakhir Pada</label>
                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $promotion->expires_at ? $promotion->expires_at->format('Y-m-d\TH:i') : '') }}" style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                </div>

                <div style="grid-column: span 2;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 15px; border: 2px solid var(--border-color); border-radius: 8px; background: #fafafa; margin-bottom: 10px;">
                        <input type="checkbox" name="is_first_order_only" value="1" {{ old('is_first_order_only', $promotion->is_first_order_only) ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: var(--primary);">
                        <div>
                            <div style="font-weight: 700; color: var(--text-main);">Khusus Pengguna Baru (Welcome Promo)</div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">Jika dicentang, promo ini hanya bisa digunakan oleh pelanggan yang belum pernah memesan sebelumnya.</div>
                        </div>
                    </label>

                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 15px; border: 2px solid var(--border-color); border-radius: 8px; background: #fafafa;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $promotion->is_active) ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: var(--primary);">
                        <div>
                            <div style="font-weight: 700; color: var(--text-main);">Promo Aktif</div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">Nonaktifkan jika promo belum siap digunakan.</div>
                        </div>
                    </label>
                </div>
            </div>

            <h3 style="margin-bottom: 15px; color: var(--text-main); border-bottom: 2px solid var(--border-color); padding-bottom: 10px;"><i class="fa-solid fa-palette"></i> Pengaturan Tema (Thematic Campaign)</h3>
            <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 20px;">Kustomisasi bagaimana promo ini akan ditampilkan di halaman pelanggan.</p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px dashed var(--border-color);">
                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Judul Banner Promosi</label>
                    <input type="text" name="banner_title" value="{{ old('banner_title', $promotion->banner_title) }}" placeholder="e.g., PESTA BOLA 2026!" style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                </div>
                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Sub-judul / Tagline</label>
                    <input type="text" name="banner_subtitle" value="{{ old('banner_subtitle', $promotion->banner_subtitle) }}" placeholder="e.g., Nobar makin seru ditemani Pizza hangat..." style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                </div>
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Warna Tema (Hex Code)</label>
                    <input type="color" name="theme_color" value="{{ old('theme_color', $promotion->theme_color ?? '#E8304A') }}" style="width: 100%; height: 50px; padding: 5px; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer;">
                </div>
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Ikon (FontAwesome Class)</label>
                    <input type="text" name="icon" value="{{ old('icon', $promotion->icon ?? 'fa-bolt') }}" placeholder="e.g., fa-futbol" style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">Contoh: fa-futbol, fa-cake-candles, fa-star.</p>
                </div>
                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Gambar Latar (Background Image)</label>
                    @if($promotion->background_image)
                        <div style="margin-bottom: 10px;">
                            <img src="{{ $promotion->background_image }}" alt="Theme Background" style="height: 100px; border-radius: 8px; border: 1px solid var(--border-color); object-fit: cover;">
                        </div>
                    @endif
                    <input type="file" name="background_image" accept="image/*" style="width: 100%; padding: 10px; border: 2px solid var(--border-color); border-radius: 8px; background: white;">
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">Kosongkan jika tidak ingin mengubah gambar.</p>
                </div>
            </div>
            
            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn-primary" style="flex: 1; padding: 14px; font-size: 1.05rem;"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
                <a href="{{ route('admin.promotions.index') }}" style="flex: 1; text-align: center; background: #e5e7eb; color: var(--text-main); text-decoration: none; padding: 14px; border-radius: 8px; font-weight: 600;">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('discountType').addEventListener('change', function() {
        document.getElementById('maxDiscountGroup').style.display = this.value === 'percentage' ? 'block' : 'none';
    });
    document.getElementById('maxDiscountGroup').style.display = document.getElementById('discountType').value === 'percentage' ? 'block' : 'none';
</script>
@endsection
