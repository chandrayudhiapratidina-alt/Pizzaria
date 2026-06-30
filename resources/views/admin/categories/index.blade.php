@extends('admin.layouts.app')
@section('title', 'Kategori Menu')
@section('content')
<style>
    .cat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .cat-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .cat-subtitle {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin-top: 5px;
    }
    .table-container {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    .cat-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .cat-table th {
        background: #f8f9fa;
        padding: 18px 20px;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--border-color);
    }
    .cat-table td {
        padding: 18px 20px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }
    .cat-table tr:hover {
        background: #fafafa;
    }
    .cat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: linear-gradient(135deg, #fef0f0, #fde8e8);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 1.1rem;
    }
    .cat-name {
        font-weight: 700;
        color: var(--text-main);
        font-size: 1.05rem;
    }
    .cat-slug {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-family: monospace;
        margin-top: 2px;
    }
    .menu-count {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f2f6;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--text-main);
    }
    .sort-badge {
        background: #e9ecef;
        color: var(--text-muted);
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .action-btn {
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: 0.2s;
        border: none;
        cursor: pointer;
        font-family: inherit;
    }
    .btn-edit { background: #f1f2f6; color: var(--text-main); }
    .btn-edit:hover { background: #e5e7eb; }
    .btn-delete { background: white; color: var(--primary); border: 1px solid var(--border-color); }
    .btn-delete:hover { background: #fff0f0; border-color: var(--primary); }
    
    .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
</style>

@if(session('error'))
    <div class="alert-error">{{ session('error') }}</div>
@endif

<div class="cat-header">
    <div>
        <div class="cat-title">
            <i class="fa-solid fa-layer-group" style="color: var(--primary);"></i>
            Kategori Menu
        </div>
        <div class="cat-subtitle">Kelola kategori untuk mengorganisir menu restoran Anda.</div>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn-primary" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-plus"></i> Tambah Kategori
    </a>
</div>

<div class="table-container">
    <table class="cat-table">
        <thead>
            <tr>
                <th>Urutan</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th>Jumlah Menu</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr>
                <td><span class="sort-badge">#{{ $category->sort_order }}</span></td>
                <td>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div class="cat-icon">
                            <i class="{{ $category->icon ?? 'fa-solid fa-tag' }}"></i>
                        </div>
                        <div>
                            <div class="cat-name">{{ $category->name }}</div>
                            <div class="cat-slug">{{ $category->slug }}</div>
                        </div>
                    </div>
                </td>
                <td style="color: var(--text-muted); max-width: 250px;">{{ Str::limit($category->description, 60) ?: '-' }}</td>
                <td>
                    <span class="menu-count">
                        <i class="fa-solid fa-utensils" style="font-size: 0.8rem;"></i>
                        {{ $category->menus_count }} menu
                    </span>
                </td>
                <td style="text-align: right;">
                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="action-btn btn-edit" style="margin-right: 5px;">
                        <i class="fa-solid fa-pen"></i> Edit
                    </a>
                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus kategori ini?');">
                        @csrf @method('DELETE')
                        <button class="action-btn btn-delete"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
            
            @if($categories->count() == 0)
            <tr>
                <td colspan="5" style="text-align: center; padding: 50px; color: var(--text-muted);">
                    <i class="fa-solid fa-layer-group" style="font-size: 3rem; margin-bottom: 15px; color: #dfe4ea;"></i>
                    <p style="font-weight: 600; font-size: 1.1rem; color: var(--text-main);">Belum ada kategori</p>
                    <p>Tambahkan kategori pertama dengan tombol di atas.</p>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection
