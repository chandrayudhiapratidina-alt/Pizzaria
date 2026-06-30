@extends('admin.layouts.app')
@section('title', 'Inventory Management')
@section('content')
<style>
    .inventory-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .inventory-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .inventory-subtitle {
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
    
    .inventory-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .inventory-table th {
        background: #f8f9fa;
        padding: 18px 20px;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--border-color);
    }
    .inventory-table td {
        padding: 18px 20px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }
    .inventory-table tr:hover {
        background: #fafafa;
    }
    
    .item-name {
        font-weight: 700;
        color: var(--text-main);
        font-size: 1.05rem;
    }
    
    .stock-qty {
        font-size: 1.2rem;
        font-weight: 800;
    }
    
    .unit-badge {
        background: #f1f2f6;
        color: var(--text-muted);
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: lowercase;
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
    .status-good {
        background: #e6f4ea;
        color: var(--green);
        border: 1px solid #c2f0d5;
    }
    .status-critical {
        background: #ffeaa7;
        color: #d63031;
        border: 1px solid #fdcb6e;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(214, 48, 49, 0.4); }
        70% { box-shadow: 0 0 0 6px rgba(214, 48, 49, 0); }
        100% { box-shadow: 0 0 0 0 rgba(214, 48, 49, 0); }
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

<div class="inventory-header">
    <div>
        <div class="inventory-title">
            <i class="fa-solid fa-boxes-stacked" style="color: var(--primary);"></i>
            Inventory Management
        </div>
        <div class="inventory-subtitle">Monitor your raw materials and stock levels in real-time.</div>
    </div>
    <a href="{{ route('admin.ingredients.create') }}" class="btn-primary" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-plus"></i> Add New Item
    </a>
</div>

<div class="table-container">
    @php
        $categories = [
            'menu_base' => ['icon' => 'fa-pizza-slice', 'title' => 'Bahan Baku Utama (Menu)'],
            'topping' => ['icon' => 'fa-cheese', 'title' => 'Bahan Baku Topping'],
            'beverage' => ['icon' => 'fa-mug-hot', 'title' => 'Bahan Minuman'],
            'other' => ['icon' => 'fa-box', 'title' => 'Lainnya']
        ];
    @endphp

    @forelse($categories as $catKey => $catMeta)
        @if(isset($ingredientsByCategory[$catKey]) && $ingredientsByCategory[$catKey]->count() > 0)
        <div style="margin-bottom: 40px;">
            <h3 style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px; font-size: 1.25rem; color: var(--text-main); font-weight: 700; padding-left: 5px;">
                <i class="fa-solid {{ $catMeta['icon'] }}" style="color: var(--primary);"></i>
                {{ $catMeta['title'] }}
            </h3>
            
            <div class="table-container" style="border-top: 3px solid var(--primary);">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Current Stock</th>
                            <th>Unit</th>
                            <th>Alert Level</th>
                            <th>Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ingredientsByCategory[$catKey] as $ing)
                        <tr>
                            <td>
                                <div class="item-name">{{ $ing->name }}</div>
                            </td>
                            <td>
                                <span class="stock-qty" style="color: {{ $ing->isLowStock() ? '#d63031' : 'var(--text-main)' }}">{{ $ing->stock_qty }}</span>
                            </td>
                            <td><span class="unit-badge">{{ $ing->unit }}</span></td>
                            <td style="color: var(--text-muted); font-weight: 600;">{{ $ing->minimum_stock_alert }}</td>
                            <td>
                                @if($ing->isLowStock())
                                    <div class="status-badge status-critical">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Low Stock
                                    </div>
                                @else
                                    <div class="status-badge status-good">
                                        <i class="fa-solid fa-check-circle"></i> Good
                                    </div>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('admin.ingredients.edit', $ing->id) }}" class="action-btn btn-edit" style="margin-right: 5px;">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </a>
                                <form action="{{ route('admin.ingredients.destroy', $ing->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus item inventory ini?');">
                                    @csrf @method('DELETE')
                                    <button class="action-btn btn-delete"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @empty
        <!-- Empty logic not hit for categories iteration, handled below -->
    @endforelse

    @if($ingredientsByCategory->isEmpty())
        <div class="table-container" style="text-align: center; padding: 50px; color: var(--text-muted);">
            <i class="fa-solid fa-box-open" style="font-size: 3rem; margin-bottom: 15px; color: #dfe4ea;"></i>
            <p style="font-weight: 600; font-size: 1.1rem; color: var(--text-main);">Gudang masih kosong</p>
            <p>Belum ada data bahan baku yang ditambahkan.</p>
        </div>
    @endif
@endsection
