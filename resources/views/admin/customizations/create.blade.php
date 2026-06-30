@extends('admin.layouts.app')
@section('title', 'Tambah Kustomisasi')
@section('content')
<div style="max-width: 800px;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.customizations.index') }}" style="color: var(--text-muted); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card" style="padding: 25px;">
        <div style="border-bottom: 1px solid var(--border-color); margin-bottom: 25px; padding-bottom: 15px;">
            <h2 style="font-size: 1.3rem; font-weight: 800; color: var(--text-main);">Tambah Opsi Kustomisasi Baru</h2>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 5px;">Tambahkan pilihan topping, ukuran, atau crust untuk menu tertentu.</p>
        </div>
        
        <form action="{{ route('admin.customizations.store') }}" method="POST">
            @csrf
            @if($errors->any())
                <div style="background:#f8d7da; color:#721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <ul style="margin:0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Berlaku Untuk (Target Kustomisasi)</label>
                    <div style="display: flex; gap: 20px;">
                        <label style="display: flex; align-items: center; gap: 5px;">
                            <input type="radio" name="target_type" value="global" onchange="toggleTarget()" {{ old('target_type') == 'global' ? 'checked' : '' }} required> Semua Menu (Global)
                        </label>
                        <label style="display: flex; align-items: center; gap: 5px;">
                            <input type="radio" name="target_type" value="category" onchange="toggleTarget()" {{ old('target_type') == 'category' ? 'checked' : '' }}> Kategori Tertentu
                        </label>
                        <label style="display: flex; align-items: center; gap: 5px;">
                            <input type="radio" name="target_type" value="menu" onchange="toggleTarget()" {{ old('target_type', 'menu') == 'menu' ? 'checked' : '' }}> Menu Spesifik
                        </label>
                    </div>
                </div>

                <div id="category_select" style="display: none;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Pilih Kategori</label>
                    <select name="category_id" style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem; background: white;">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="menu_select" style="display: none;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Pilih Menu</label>
                    <select name="menu_id" style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem; background: white;">
                        <option value="">-- Pilih Menu --</option>
                        @foreach($menus as $menu)
                            <option value="{{ $menu->id }}" {{ old('menu_id') == $menu->id ? 'selected' : '' }}>{{ $menu->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Tipe Kustomisasi</label>
                    <select name="type" required style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem; background: white;">
                        <option value="topping" {{ old('type') == 'topping' ? 'selected' : '' }}>Topping</option>
                        <option value="size" {{ old('type') == 'size' ? 'selected' : '' }}>Size (Ukuran)</option>
                        <option value="crust" {{ old('type') == 'crust' ? 'selected' : '' }}>Crust (Pinggiran)</option>
                    </select>
                </div>

                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Nama Pilihan (contoh: Extra Cheese, Large)</label>
                    <input type="text" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                </div>

                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Tambahan Harga (Rp)</label>
                    <input type="number" name="additional_price" value="{{ old('additional_price', 0) }}" required min="0" style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                </div>
                
                <div style="grid-column: span 2; background: #fafafa; padding: 20px; border: 1px solid var(--border-color); border-radius: 8px;">
                    <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-main); margin-bottom: 15px;">Pengaturan Stok Porsi</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 15px;">Masukkan jumlah porsi kustomisasi/topping yang tersedia saat ini.</p>
                    
                    <div>
                        <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Jumlah Stok</label>
                        <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" placeholder="Misal: 100" style="width: 100%; max-width: 300px; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                    </div>
                    
                    <div style="margin-top: 15px;">
                        <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Potong Bahan Baku (Opsional)</label>
                        <select name="deduct_ingredient_id" style="width: 100%; max-width: 300px; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem; background: white;">
                            <option value="">-- Tidak Ada --</option>
                            @foreach($ingredients as $ingredient)
                                <option value="{{ $ingredient->id }}" {{ old('deduct_ingredient_id') == $ingredient->id ? 'selected' : '' }}>{{ $ingredient->name }} ({{ $ingredient->unit }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-top: 15px;">
                        <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Jumlah Potongan per Porsi</label>
                        <input type="number" step="0.01" name="deduct_qty" value="{{ old('deduct_qty', 0) }}" min="0" placeholder="Misal: 0.5" style="width: 100%; max-width: 300px; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn-primary" style="margin-top: 25px; width: 100%; padding: 14px; font-size: 1.05rem;"><i class="fa-solid fa-save"></i> Simpan Kustomisasi</button>
        </form>
    </div>
</div>
</div>
<script>
function toggleTarget() {
    const type = document.querySelector('input[name="target_type"]:checked').value;
    document.getElementById('category_select').style.display = type === 'category' ? 'block' : 'none';
    document.getElementById('menu_select').style.display = type === 'menu' ? 'block' : 'none';
}
// Init on load
document.addEventListener('DOMContentLoaded', toggleTarget);
</script>
@endsection
