@extends('admin.layouts.app')
@section('title', 'Add Ingredient')
@section('content')
<div style="max-width: 600px;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.ingredients.index') }}" style="color: var(--text-muted); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-left"></i> Back to Inventory
        </a>
    </div>

    <div class="card">
        <div style="padding: 20px; border-bottom: 1px solid var(--border-color);">
            <h2 style="font-size: 1.2rem; font-weight: 700; color: var(--text-main);">Add New Ingredient</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">Register a new raw material to your stock.</p>
        </div>
        
        <form action="{{ route('admin.ingredients.store') }}" method="POST" style="padding: 20px;">
            @csrf
            
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
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g., Mozzarella Cheese" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Category</label>
                <select name="category" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem; background: white;">
                    <option value="menu_base" {{ old('category') == 'menu_base' ? 'selected' : '' }}>Bahan Baku Utama (Menu)</option>
                    <option value="topping" {{ old('category') == 'topping' ? 'selected' : '' }}>Bahan Baku Topping</option>
                    <option value="beverage" {{ old('category') == 'beverage' ? 'selected' : '' }}>Bahan Minuman</option>
                    <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Initial Stock Quantity</label>
                    <input type="number" name="stock_qty" value="{{ old('stock_qty', 0) }}" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Unit of Measurement</label>
                    <select name="unit" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem; background: white;">
                        <option value="g" {{ old('unit') == 'g' ? 'selected' : '' }}>Grams (g)</option>
                        <option value="ml" {{ old('unit') == 'ml' ? 'selected' : '' }}>Milliliters (ml)</option>
                        <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                    </select>
                </div>
            </div>
            
            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Minimum Stock Alert Level</label>
                <input type="number" name="minimum_stock_alert" value="{{ old('minimum_stock_alert', 10) }}" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                <small style="color: var(--text-muted); display: block; margin-top: 5px;">System will alert you if stock falls below this number.</small>
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%; font-size: 1.05rem; padding: 14px;">Save Ingredient</button>
        </form>
    </div>
</div>
@endsection
