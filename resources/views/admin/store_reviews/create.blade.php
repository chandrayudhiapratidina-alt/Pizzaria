@extends('admin.layouts.app')

@section('title', 'Tambah Ulasan Restoran')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin: 0;">
        <i class="fa-solid fa-plus-circle" style="color: var(--primary); margin-right: 10px;"></i> Tambah Ulasan Baru
    </h2>
    <a href="{{ route('admin.store_reviews.index') }}" style="background: #f1f5f9; color: var(--text-main); padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); transition: background 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card" style="background: white; border-radius: 16px; padding: 30px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);">
    <form action="{{ route('admin.store_reviews.store') }}" method="POST">
        @csrf

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-main);">Nama Pengulas (Guest Name) <span style="color: var(--primary);">*</span></label>
            <input type="text" name="guest_name" value="{{ old('guest_name') }}" style="width: 100%; padding: 12px 15px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 0.95rem; outline: none; transition: border-color 0.2s;" required placeholder="Contoh: Budi Santoso" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-main);">Rating <span style="color: var(--primary);">*</span></label>
                <select name="rating" style="width: 100%; padding: 12px 15px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 0.95rem; outline: none; background-color: white; cursor: pointer;" required onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">
                    <option value="5" {{ old('rating') == '5' ? 'selected' : '' }}>5 Bintang (Sempurna)</option>
                    <option value="4" {{ old('rating') == '4' ? 'selected' : '' }}>4 Bintang (Bagus)</option>
                    <option value="3" {{ old('rating') == '3' ? 'selected' : '' }}>3 Bintang (Biasa)</option>
                    <option value="2" {{ old('rating') == '2' ? 'selected' : '' }}>2 Bintang (Kurang)</option>
                    <option value="1" {{ old('rating') == '1' ? 'selected' : '' }}>1 Bintang (Sangat Buruk)</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-main);">Tipe Ulasan <span style="color: var(--primary);">*</span></label>
                <select name="review_type" style="width: 100%; padding: 12px 15px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 0.95rem; outline: none; background-color: white; cursor: pointer;" required onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">
                    <option value="general" {{ old('review_type') == 'general' ? 'selected' : '' }}>Pengalaman Umum</option>
                    <option value="service" {{ old('review_type') == 'service' ? 'selected' : '' }}>Layanan Restoran</option>
                    <option value="ambiance" {{ old('review_type') == 'ambiance' ? 'selected' : '' }}>Suasana & Tempat</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-main);">Komentar</label>
            <textarea name="comment" rows="4" style="width: 100%; padding: 12px 15px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 0.95rem; outline: none; resize: vertical;" placeholder="Tulis komentar ulasan di sini..." onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">{{ old('comment') }}</textarea>
        </div>

        <div style="margin-bottom: 30px; display: flex; align-items: center; gap: 10px; background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid var(--border-color);">
            <input type="checkbox" name="is_approved" id="is_approved" value="1" {{ old('is_approved', 1) ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary);">
            <label for="is_approved" style="font-weight: 600; cursor: pointer; color: var(--text-main); margin: 0; user-select: none;">Langsung Tampilkan di Halaman Utama (Approved)</label>
        </div>

        <button type="submit" style="background: var(--primary); color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.3s; box-shadow: 0 4px 10px rgba(192, 10, 39, 0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 15px rgba(192, 10, 39, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 10px rgba(192, 10, 39, 0.2)'">
            <i class="fa-solid fa-save"></i> Simpan Ulasan
        </button>
    </form>
</div>
@endsection
