@extends('admin.layouts.app')
@section('title', 'Menu Management')
@section('content')
<style>
    .menu-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .menu-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .menu-subtitle {
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
    
    .menu-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .menu-table th {
        background: #f8f9fa;
        padding: 18px 20px;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--border-color);
    }
    .menu-table td {
        padding: 18px 20px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }
    .menu-table tr:hover {
        background: #fafafa;
    }
    
    .menu-img-container {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        overflow: hidden;
        background: #dfe4ea;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .menu-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .menu-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .menu-name {
        font-weight: 700;
        color: var(--text-main);
        font-size: 1.1rem;
        margin-bottom: 4px;
    }
    
    .price-tag {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--text-main);
    }
    
    .category-badge {
        background: #f1f2f6;
        color: var(--text-muted);
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .status-available {
        background: #e6f4ea;
        color: var(--green);
        border: 1px solid #c2f0d5;
    }
    .status-unavailable {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
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
</style>

<div class="menu-header">
    <div>
        <div class="menu-title">
            <i class="fa-solid fa-pizza-slice" style="color: var(--primary);"></i>
            Menu Management
        </div>
        <div class="menu-subtitle">Atur daftar menu restoran Anda, ketersediaan, dan harga.</div>
    </div>
    <a href="{{ route('admin.menus.create') }}" class="btn-primary" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-plus"></i> Add New Menu
    </a>
</div>

<div class="table-container">
    <table class="menu-table">
        <thead>
            <tr>
                <th>Menu Item</th>
                <th>Category</th>
                <th>Price</th>
                <th>Status</th>
                <th>Sisa Stok</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($menus as $menu)
            <tr>
                <td>
                    <div class="menu-info">
                        <div class="menu-img-container">
                            @if($menu->image_url)
                                <img src="{{ asset('storage/' . $menu->image_url) }}" alt="{{ $menu->name }}">
                            @else
                                <i class="fa-solid fa-image" style="color: #aaa; font-size: 1.5rem;"></i>
                            @endif
                        </div>
                        <div>
                            <div class="menu-name">{{ $menu->name }}</div>
                            <div style="font-size: 0.85rem; color: var(--text-muted); max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $menu->description ?: 'No description' }}
                            </div>
                        </div>
                    </div>
                </td>
                <td><span class="category-badge">{{ $menu->category->name ?? 'Uncategorized' }}</span></td>
                <td>
                    @if($menu->hasActiveDiscount())
                        <div style="text-decoration: line-through; color: var(--text-muted); font-size: 0.85rem;">Rp {{ number_format($menu->base_price, 0, ',', '.') }}</div>
                        <span class="price-tag" style="color: var(--primary);">Rp {{ number_format($menu->discount_price, 0, ',', '.') }}</span>
                    @else
                        <span class="price-tag">Rp {{ number_format($menu->base_price, 0, ',', '.') }}</span>
                    @endif
                </td>
                <td>
                    @if($menu->is_available)
                        <div class="status-badge status-available">
                            <i class="fa-solid fa-check-circle"></i> Available
                        </div>
                    @else
                        <div class="status-badge status-unavailable">
                            <i class="fa-solid fa-times-circle"></i> Sold Out
                        </div>
                    @endif
                </td>
                <td>
                    <div style="font-weight: 700; color: {{ $menu->daily_stock > 0 ? 'var(--text-main)' : 'var(--red)' }};">
                        {{ $menu->daily_stock }} <span style="font-size: 0.8rem; font-weight: 400; color: var(--text-muted);">tersedia</span>
                    </div>
                </td>
                <td style="text-align: right;">
                    <a href="{{ route('admin.menus.edit', $menu->id) }}" class="action-btn btn-edit" style="margin-right: 5px;">
                        <i class="fa-solid fa-pen"></i> Edit
                    </a>
                    <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus menu ini secara permanen?');">
                        @csrf @method('DELETE')
                        <button class="action-btn btn-delete"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
            
            @if($menus->count() == 0)
            <tr>
                <td colspan="5" style="text-align: center; padding: 50px; color: var(--text-muted);">
                    <i class="fa-solid fa-pizza-slice" style="font-size: 3rem; margin-bottom: 15px; color: #dfe4ea;"></i>
                    <p style="font-weight: 600; font-size: 1.1rem; color: var(--text-main);">Daftar menu kosong</p>
                    <p>Belum ada menu yang didaftarkan. Klik tombol Add New Menu untuk mulai.</p>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection
