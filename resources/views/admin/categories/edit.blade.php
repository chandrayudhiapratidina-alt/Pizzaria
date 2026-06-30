@extends('admin.layouts.app')
@section('title', 'Edit Kategori')
@section('content')
<div style="max-width: 700px;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.categories.index') }}" style="color: var(--text-muted); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Kategori
        </a>
    </div>

    <div class="card">
        <div style="padding: 20px 25px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="font-size: 1.3rem; font-weight: 800; color: var(--text-main);">Edit Kategori</h2>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 5px;">Perbarui detail untuk {{ $category->name }}.</p>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 10px; background: linear-gradient(135deg, #fef0f0, #fde8e8); display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.2rem;">
                <i class="{{ $category->icon ?? 'fa-solid fa-tag' }}"></i>
            </div>
        </div>
        
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" style="padding: 25px;">
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

            <div style="display: grid; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Nama Kategori <span style="color: var(--primary);">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                </div>

                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Pilih Icon</label>
                    <input type="hidden" name="icon" id="iconInput" value="{{ old('icon', $category->icon) }}">
                    
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;" id="iconGrid">
                        @php
                            $commonIcons = [
                                'fa-solid fa-pizza-slice', 'fa-solid fa-burger', 'fa-solid fa-hotdog', 
                                'fa-solid fa-ice-cream', 'fa-solid fa-mug-hot', 'fa-solid fa-martini-glass-citrus',
                                'fa-solid fa-bowl-food', 'fa-solid fa-cake-candles', 'fa-solid fa-cookie-bite',
                                'fa-solid fa-utensils', 'fa-solid fa-drumstick-bite', 'fa-solid fa-bowl-rice',
                                'fa-solid fa-fish', 'fa-solid fa-cheese', 'fa-solid fa-carrot'
                            ];
                            $currentIcon = old('icon', $category->icon ?? 'fa-solid fa-pizza-slice');
                        @endphp
                        
                        @foreach($commonIcons as $ic)
                            <div class="icon-option {{ $currentIcon == $ic ? 'selected' : '' }}" data-icon="{{ $ic }}" 
                                style="width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; cursor: pointer; transition: all 0.2s;
                                border: 2px solid {{ $currentIcon == $ic ? 'var(--primary)' : 'var(--border-color)' }};
                                background: {{ $currentIcon == $ic ? 'linear-gradient(135deg, #fef0f0, #fde8e8)' : 'white' }};
                                color: {{ $currentIcon == $ic ? 'var(--primary)' : 'var(--text-muted)' }};">
                                <i class="{{ $ic }}"></i>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Deskripsi</label>
                    <textarea name="description" rows="3" style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem; resize: vertical;">{{ old('description', $category->description) }}</textarea>
                </div>

                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Urutan Tampil</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0" style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                </div>
            </div>
            
            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn-primary" style="flex: 1; padding: 14px; font-size: 1.05rem;"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
                <a href="{{ route('admin.categories.index') }}" style="flex: 1; text-align: center; background: #e5e7eb; color: var(--text-main); text-decoration: none; padding: 14px; border-radius: 8px; font-weight: 600;">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.icon-option').forEach(item => {
        item.addEventListener('click', function() {
            // Reset all
            document.querySelectorAll('.icon-option').forEach(opt => {
                opt.style.borderColor = 'var(--border-color)';
                opt.style.background = 'white';
                opt.style.color = 'var(--text-muted)';
                opt.classList.remove('selected');
            });
            
            // Set selected
            this.style.borderColor = 'var(--primary)';
            this.style.background = 'linear-gradient(135deg, #fef0f0, #fde8e8)';
            this.style.color = 'var(--primary)';
            this.classList.add('selected');
            
            // Update input
            document.getElementById('iconInput').value = this.getAttribute('data-icon');
        });
    });
</script>
@endsection
