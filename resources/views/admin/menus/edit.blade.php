@extends('admin.layouts.app')
@section('title', 'Edit Menu')
@section('content')
<div style="max-width: 800px;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.menus.index') }}" style="color: var(--text-muted); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-left"></i> Back to Menu List
        </a>
    </div>

    <div class="card">
        <div style="padding: 20px 25px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="font-size: 1.3rem; font-weight: 800; color: var(--text-main);">Edit Menu Item</h2>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 5px;">Update details for {{ $menu->name }}.</p>
            </div>
            @if($menu->image_url)
                <div style="width: 60px; height: 60px; border-radius: 10px; overflow: hidden; border: 2px solid var(--border-color);">
                    <img src="{{ asset('storage/' . $menu->image_url) }}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            @endif
        </div>
        
        <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data" style="padding: 25px;">
            @csrf
            @method('PUT')
            
            @if($errors->any())
                <div class="alert alert-danger" style="background:#f8d7da; color:#721c24; margin-bottom: 20px; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb;">
                    <ul style="margin:0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Menu Name</label>
                    <input type="text" name="name" value="{{ old('name', $menu->name) }}" required style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                </div>

                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Description</label>
                    <textarea name="description" rows="3" style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem; resize: vertical;">{{ old('description', $menu->description) }}</textarea>
                </div>
                
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Base Price (Rp)</label>
                    <input type="number" name="base_price" value="{{ old('base_price', $menu->base_price) }}" required style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                </div>
                
                <div style="grid-column: span 2; border: 1px solid var(--primary); padding: 20px; border-radius: 8px; background: rgba(230, 57, 70, 0.03);">
                    <h4 style="margin-top:0; margin-bottom:15px; color: var(--primary); font-size: 1.1rem;"><i class="fa-solid fa-tags"></i> Pengaturan Diskon Spesifik (Opsional)</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        
                        <!-- Baris 1: Tipe & Nilai -->
                        <div>
                            <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Tipe Diskon</label>
                            <select name="discount_type" id="discount_type" style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; background: white; font-size: 0.95rem;">
                                <option value="fixed" {{ old('discount_type', $menu->discount_type ?? 'fixed') == 'fixed' ? 'selected' : '' }}>Harga Final (Rp)</option>
                                <option value="percentage" {{ old('discount_type', $menu->discount_type ?? 'fixed') == 'percentage' ? 'selected' : '' }}>Potongan Persen (%)</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Nilai Diskon</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="number" id="discount_value" name="discount_value" value="{{ old('discount_value', $menu->discount_type == 'percentage' ? $menu->discount_value : $menu->discount_price) }}" placeholder="Contoh: 20 atau 80000" style="flex: 1; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.95rem;">
                                <div id="discount_calculated" style="display: flex; align-items: center; justify-content: center; font-weight: bold; color: white; padding: 0 15px; background: var(--primary); border-radius: 6px; font-size: 0.9rem; min-width: 140px; box-shadow: 0 2px 5px rgba(230,57,70,0.3);">
                                    Final: -
                                </div>
                            </div>
                            <!-- discount_price will be calculated in backend -->
                        </div>

                        <!-- Baris 2: Waktu (Opsional) -->
                        <div>
                            <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Berlaku Mulai <small style="font-weight:normal; color:var(--text-muted);">(Opsional)</small></label>
                            <input type="datetime-local" name="discount_start" value="{{ old('discount_start', $menu->discount_start ? $menu->discount_start->format('Y-m-d\TH:i') : '') }}" style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.95rem;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Berakhir Pada <small style="font-weight:normal; color:var(--text-muted);">(Opsional)</small></label>
                            <input type="datetime-local" name="discount_end" value="{{ old('discount_end', $menu->discount_end ? $menu->discount_end->format('Y-m-d\TH:i') : '') }}" style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.95rem;">
                        </div>

                    </div>
                </div>
                
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Stok Harian</label>
                    <input type="number" name="daily_stock" value="{{ old('daily_stock', $menu->daily_stock) }}" placeholder="0" min="0" style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                </div>
                
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Category</label>
                    <select name="category_id" required style="width: 100%; padding: 12px 15px; border: 2px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem; background: white;">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (old('category_id') ?? $menu->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Update Menu Image <small style="font-weight:400; color:var(--text-muted);">(Leave blank to keep current image)</small></label>
                    <div style="border: 2px dashed var(--border-color); border-radius: 8px; padding: 20px; text-align: center; background: #fafafa;">
                        <input type="file" name="image" accept="image/*" style="display: block; width: 100%; max-width: 300px; margin: 0 auto; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; background: white;">
                    </div>
                </div>

                <div style="grid-column: span 2;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 15px; border: 2px solid var(--border-color); border-radius: 8px; background: #fafafa;">
                        <input type="checkbox" name="is_available" value="1" {{ old('is_available', $menu->is_available) ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: var(--primary);">
                        <div>
                            <div style="font-weight: 700; color: var(--text-main);">Menu is Available</div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">Uncheck this if the menu is currently sold out.</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- BOM / Recipe Section --}}
            <div style="border: 2px solid var(--border-color); border-radius: 12px; padding: 20px; margin-bottom: 20px; background: #fafbfc;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <div>
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-flask" style="color: var(--primary);"></i> Resep / Bill of Materials
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">Tentukan bahan baku dan jumlah yang dibutuhkan.</p>
                    </div>
                    <button type="button" id="addIngredientRow" style="background: var(--primary); color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: inherit; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-plus"></i> Tambah Bahan
                    </button>
                </div>
                <div id="ingredientRows">
                    {{-- Pre-filled rows from existing recipe --}}
                </div>
                <div id="emptyBom" style="text-align: center; padding: 20px; color: var(--text-muted); font-size: 0.9rem; {{ $menu->ingredients->count() > 0 ? 'display:none;' : '' }}">
                    <i class="fa-solid fa-box-open" style="font-size: 1.5rem; color: #dfe4ea; margin-bottom: 8px; display: block;"></i>
                    Belum ada bahan baku. Klik "Tambah Bahan" untuk menambahkan resep.
                </div>
            </div>
            
            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn-primary" style="flex: 1; padding: 14px; font-size: 1.05rem;"><i class="fa-solid fa-save"></i> Save Changes</button>
                <a href="{{ route('admin.menus.index') }}" style="flex: 1; text-align: center; background: #e5e7eb; color: var(--text-main); text-decoration: none; padding: 14px; border-radius: 8px; font-weight: 600;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    const ingredientsData = @json($ingredients);
    const existingRecipe = @json($menu->ingredients->map(fn($i) => ['id' => $i->id, 'qty' => $i->pivot->qty_needed]));

    function createIngredientRow(selectedId = '', qty = '') {
        const container = document.getElementById('ingredientRows');
        const emptyMsg = document.getElementById('emptyBom');
        emptyMsg.style.display = 'none';

        const row = document.createElement('div');
        row.style.cssText = 'display: flex; gap: 10px; align-items: center; margin-bottom: 10px; background: white; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color);';
        row.innerHTML = `
            <select name="ingredient_ids[]" style="flex: 2; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; font-family: inherit;" required>
                <option value="">-- Pilih Bahan --</option>
                ${ingredientsData.map(i => `<option value="${i.id}" ${i.id == selectedId ? 'selected' : ''}>${i.name} (${i.unit})</option>`).join('')}
            </select>
            <input type="number" name="qty_needed[]" placeholder="Jumlah" value="${qty}" step="0.01" min="0.01" required style="flex: 1; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; font-family: inherit;">
            <button type="button" onclick="this.parentElement.remove(); checkEmpty();" style="background: #f8d7da; color: #721c24; border: none; width: 38px; height: 38px; border-radius: 6px; cursor: pointer; font-size: 1rem; flex-shrink: 0;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        `;
        container.appendChild(row);
    }

    function checkEmpty() {
        const container = document.getElementById('ingredientRows');
        const emptyMsg = document.getElementById('emptyBom');
        emptyMsg.style.display = container.children.length === 0 ? 'block' : 'none';
    }

    document.getElementById('addIngredientRow').addEventListener('click', function() {
        createIngredientRow();
    });

    // Pre-fill existing recipe rows
    existingRecipe.forEach(r => createIngredientRow(r.id, r.qty));

    // Kalkulator Diskon UI
    const basePriceInput = document.querySelector('input[name="base_price"]');
    const discountTypeSelect = document.getElementById('discount_type');
    const discountValueInput = document.getElementById('discount_value');
    const discountCalculated = document.getElementById('discount_calculated');

    function calculateDiscount() {
        const basePrice = parseFloat(basePriceInput.value) || 0;
        const discountType = discountTypeSelect.value;
        const discountValue = parseFloat(discountValueInput.value) || 0;

        let finalPrice = basePrice;
        if (discountValue > 0) {
            if (discountType === 'percentage') {
                finalPrice = Math.max(0, basePrice - (basePrice * (discountValue / 100)));
            } else {
                finalPrice = discountValue; // Fixed price
            }
            discountCalculated.innerHTML = 'Final: Rp ' + finalPrice.toLocaleString('id-ID');
            discountCalculated.style.color = '#ffffff';
        } else {
            discountCalculated.innerHTML = 'Final: -';
            discountCalculated.style.color = '#ffffff';
        }
    }

    basePriceInput.addEventListener('input', calculateDiscount);
    discountTypeSelect.addEventListener('change', calculateDiscount);
    discountValueInput.addEventListener('input', calculateDiscount);

    // Initial calc
    calculateDiscount();
</script>
@endsection
