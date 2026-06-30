@extends('admin.layouts.app')
@section('title', 'Edit Ingredient')
@section('content')
<div style="max-width: 600px;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.ingredients.index') }}" style="color: var(--text-muted); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-left"></i> Back to Inventory
        </a>
    </div>

    <div class="card">
        <div style="padding: 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="font-size: 1.2rem; font-weight: 700; color: var(--text-main);">Edit Ingredient</h2>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">Update details for {{ $ingredient->name }}.</p>
            </div>
            <div style="background: #f1f2f6; padding: 10px; border-radius: 8px; text-align: center;">
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Current Stock</div>
                <div style="font-size: 1.3rem; font-weight: 800; color: var(--text-main);">{{ $ingredient->stock_qty }} <span style="font-size: 0.9rem;">{{ $ingredient->unit }}</span></div>
            </div>
        </div>
        
        <form action="{{ route('admin.ingredients.update', $ingredient->id) }}" method="POST" style="padding: 20px;">
            @csrf
            @method('PUT')
            
            @if($errors->any())
                <div class="alert alert-danger" style="background:#ffeaa7; color:#d63031; margin-bottom: 20px; padding: 15px; border-radius: 8px;">
                    <ul style="margin:0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Ingredient Name</label>
                <input type="text" name="name" value="{{ old('name', $ingredient->name) }}" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Category</label>
                <select name="category" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem; background: white;">
                    <option value="menu_base" {{ (old('category') ?? $ingredient->category) == 'menu_base' ? 'selected' : '' }}>Bahan Baku Utama (Menu)</option>
                    <option value="topping" {{ (old('category') ?? $ingredient->category) == 'topping' ? 'selected' : '' }}>Bahan Baku Topping</option>
                    <option value="beverage" {{ (old('category') ?? $ingredient->category) == 'beverage' ? 'selected' : '' }}>Bahan Minuman</option>
                    <option value="other" {{ (old('category') ?? $ingredient->category) == 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Update Stock Quantity</label>
                    <input type="number" name="stock_qty" value="{{ old('stock_qty', $ingredient->stock_qty) }}" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Unit of Measurement</label>
                    <select name="unit" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem; background: white;">
                        <option value="g" {{ (old('unit') ?? $ingredient->unit) == 'g' ? 'selected' : '' }}>Grams (g)</option>
                        <option value="ml" {{ (old('unit') ?? $ingredient->unit) == 'ml' ? 'selected' : '' }}>Milliliters (ml)</option>
                        <option value="pcs" {{ (old('unit') ?? $ingredient->unit) == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                    </select>
                </div>
            </div>
            
            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Minimum Stock Alert Level</label>
                <input type="number" name="minimum_stock_alert" value="{{ old('minimum_stock_alert', $ingredient->minimum_stock_alert) }}" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                <small style="color: var(--text-muted); display: block; margin-top: 5px;">System will alert you if stock falls below this number.</small>
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%; font-size: 1.05rem; padding: 14px;">Update Ingredient</button>
        </form>
    </div>
</div>
@endsection
