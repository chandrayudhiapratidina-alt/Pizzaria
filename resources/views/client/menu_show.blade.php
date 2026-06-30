@extends('client.layouts.app')
@section('title', 'Kustomisasi - ' . $menu->name)
@section('content')
<style>
    .custom-container {
        max-width: 650px;
        margin: 0 auto;
        background: var(--card-bg);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        padding-bottom: 110px;
        position: relative;
        border: 1px solid #333;
    }
    .custom-header {
        position: relative;
        width: 100%;
        background: var(--bg-dark);
        text-align: center;
        padding-top: 20px;
    }
    .custom-img {
        width: 100%;
        max-width: 300px;
        height: auto;
        object-fit: cover;
        margin: 0 auto;
        border-radius: 12px;
    }
    .gallery-container {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        gap: 15px;
        padding: 20px;
        scrollbar-width: thin;
        scrollbar-color: var(--primary) transparent;
    }
    .gallery-item {
        flex: 0 0 auto;
        width: 80%;
        max-width: 300px;
        scroll-snap-align: center;
        border-radius: 12px;
        overflow: hidden;
    }
    .gallery-item img {
        width: 100%;
        display: block;
    }
    .review-section {
        margin-top: 40px;
        border-top: 1px solid #333;
        padding-top: 30px;
    }
    .review-card {
        background: var(--bg-dark);
        border: 1px solid #333;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .review-stars { color: var(--gold); font-size: 0.9rem; margin-bottom: 8px; }
    .review-user { font-weight: bold; color: #fff; font-size: 0.9rem; margin-bottom: 4px; }
    .review-text { color: var(--gray); font-size: 0.85rem; line-height: 1.5; }
    .custom-content {
        padding: 30px;
        background: var(--card-bg);
    }
    .c-title {
        font-family: 'Playfair Display', serif;
        font-size: 2rem;
        font-weight: 800;
        margin: 0 0 10px 0;
        color: #fff;
        letter-spacing: -0.5px;
    }
    .c-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    .c-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.8rem;
        font-weight: 800;
        color: #fff;
        margin: 0;
    }
    .c-desc {
        color: var(--gray);
        line-height: 1.7;
        margin-bottom: 35px;
        font-size: 1.05rem;
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #fff;
    }
    .req-badge {
        background: rgba(232, 48, 74, 0.1);
        color: var(--primary);
        border: 1px solid rgba(232, 48, 74, 0.3);
        font-size: 0.7rem;
        padding: 4px 8px;
        border-radius: 4px;
        font-weight: bold;
    }
    
    .option-box {
        border: 1px solid #333;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 15px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
        background: var(--bg-dark);
    }
    .option-box:hover {
        border-color: #555;
        background: #151515;
    }
    .option-box:has(input:checked) {
        border-color: var(--primary);
        background: rgba(232, 48, 74, 0.05);
        box-shadow: 0 4px 15px rgba(232, 48, 74, 0.1);
    }
    .option-box input[type="radio"], .option-box input[type="checkbox"] {
        accent-color: var(--primary);
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
    .opt-details {
        flex: 1;
        margin-left: 15px;
    }
    .opt-name { font-weight: 700; display: block; }
    .opt-price { font-weight: 800; color: var(--primary); }
    
    /* grid for crusts */
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
    .box-center {
        text-align: center;
        flex-direction: column;
        justify-content: center;
    }
    .box-center input { margin-bottom: 10px; }
    .box-center .opt-details { margin-left: 0; }
    
    /* Bottom Bar */
    .bottom-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background: rgba(10, 10, 10, 0.95);
        backdrop-filter: blur(15px);
        border-top: 1px solid #222;
        padding: 15px 5%;
        display: flex;
        justify-content: center;
        z-index: 100;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.5);
    }
    .bar-content {
        max-width: 600px;
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .total-label { color: var(--gray); font-size: 0.8rem; font-weight: 700; margin-bottom: 5px; }
    .total-price { font-size: 1.5rem; font-weight: 800; color: #fff; margin: 0; }
    
    .qty-selector {
        display: inline-flex;
        align-items: center;
        background: var(--bg-dark);
        border: 1px solid #333;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 30px;
        padding: 4px;
    }
    .qty-btn {
        background: var(--card-bg);
        border: 1px solid #333;
        width: 40px;
        height: 40px;
        border-radius: 6px;
        font-size: 1.2rem;
        cursor: pointer;
        font-weight: bold;
        color: #fff;
        transition: 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .qty-btn:hover {
        background: #222;
    }
    .qty-input {
        width: 60px;
        text-align: center;
        border: none;
        font-size: 1.1rem;
        font-weight: 700;
        background: transparent;
        color: #fff;
        pointer-events: none;
    }
</style>

<div class="custom-container">
    <div class="custom-header">
        @if(isset($images) && $images->count() > 0)
            <div class="gallery-container">
                @foreach($images as $img)
                    <div class="gallery-item">
                        <img src="{{ asset($img->image_url) }}" alt="{{ $menu->name }}">
                    </div>
                @endforeach
            </div>
        @elseif($menu->image_url)
            <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" class="custom-img" style="margin-top:20px; margin-bottom:20px;">
        @else
            <div class="custom-img" style="background:#222; display:flex; align-items:center; justify-content:center; font-weight:bold; color:#555; font-size:1.5rem; height: 200px; margin-top:20px; margin-bottom:20px;">No Image</div>
        @endif
    </div>
    
    <div class="custom-content">
        <h1 class="c-title">{{ $menu->name }}</h1>
        <p class="c-desc">{{ $menu->description }}</p>
        
        <form action="{{ route('client.guest.cart.add') }}" method="POST" id="addToCartForm">
            @csrf
            <input type="hidden" name="action" id="actionType" value="add_only">
            <input type="hidden" name="menu_id" value="{{ $menu->id }}">
            <input type="hidden" name="base_price" value="{{ $menu->base_price }}">
            <input type="hidden" name="total_price" id="hiddenTotalPrice" value="{{ $menu->base_price }}">
            
            <div class="section-title">Jumlah Pesanan</div>
            <div class="qty-selector">
                <button type="button" class="qty-btn" id="btnMinus">-</button>
                <input type="number" name="qty" id="qtyInput" class="qty-input" value="1" readonly>
                <button type="button" class="qty-btn" id="btnPlus">+</button>
            </div>

            <!-- Sizes -->
            @if($sizes->count() > 0)
            <div class="mb-4">
                <div class="section-title">
                    <span>Pilih Ukuran</span>
                    <span class="req-badge">WAJIB</span>
                </div>
                @foreach($sizes as $index => $size)
                @php
                    $isAvailable = $size->isAvailable();
                @endphp
                <label class="option-box" style="{{ !$isAvailable ? 'opacity: 0.5; cursor: not-allowed; background: #222;' : '' }}">
                    <input type="radio" name="size" value="{{ $size->id }}" data-price="{{ $size->additional_price }}" data-name="{{ $size->name }}" {{ $index == 0 && $isAvailable ? 'checked' : '' }} {{ !$isAvailable ? 'disabled' : '' }} onchange="calculateTotal()">
                    <div class="opt-details">
                        <span class="opt-name">{{ $size->name }} {!! !$isAvailable ? '<span style="color:red; font-size:0.8rem;">(Habis)</span>' : '' !!}</span>
                    </div>
                    <span class="opt-price">+ Rp {{ number_format($size->additional_price, 0, ',', '.') }}</span>
                </label>
                @endforeach
                <input type="hidden" name="size_price" id="hiddenSizePrice" value="{{ $sizes[0]->additional_price ?? 0 }}">
                <input type="hidden" name="size_name" id="hiddenSizeName" value="{{ $sizes[0]->name ?? '' }}">
            </div>
            @endif

            <!-- Temperatures -->
            @if(isset($temperatures) && $temperatures->count() > 0)
            <div class="mb-4">
                <div class="section-title">
                    <span>Pilih Suhu</span>
                    <span class="req-badge">WAJIB</span>
                </div>
                @foreach($temperatures as $index => $temp)
                @php
                    $isAvailable = $temp->isAvailable();
                @endphp
                <label class="option-box" style="{{ !$isAvailable ? 'opacity: 0.5; cursor: not-allowed; background: #222;' : '' }}">
                    <input type="radio" name="temperature" value="{{ $temp->id }}" data-price="{{ $temp->additional_price }}" data-name="{{ $temp->name }}" {{ $index == 0 && $isAvailable ? 'checked' : '' }} {{ !$isAvailable ? 'disabled' : '' }} onchange="calculateTotal()">
                    <div class="opt-details">
                        <span class="opt-name">{{ $temp->name }} {!! !$isAvailable ? '<span style="color:red; font-size:0.8rem;">(Habis)</span>' : '' !!}</span>
                    </div>
                    <span class="opt-price">+ Rp {{ number_format($temp->additional_price, 0, ',', '.') }}</span>
                </label>
                @endforeach
                <input type="hidden" name="temperature_price" id="hiddenTempPrice" value="{{ $temperatures[0]->additional_price ?? 0 }}">
                <input type="hidden" name="temperature_name" id="hiddenTempName" value="{{ $temperatures[0]->name ?? '' }}">
            </div>
            @endif

            <!-- Crusts -->
            @if($crusts->count() > 0)
            <div class="mb-4">
                <div class="section-title">
                    <span>Pilih Pinggiran (Crust)</span>
                </div>
                <div class="grid-2">
                    @foreach($crusts as $index => $crust)
                    @php
                        $isAvailable = $crust->isAvailable();
                    @endphp
                    <label class="option-box box-center" style="{{ !$isAvailable ? 'opacity: 0.5; cursor: not-allowed; background: #222;' : '' }}">
                        <input type="radio" name="crust" value="{{ $crust->id }}" data-price="{{ $crust->additional_price }}" data-name="{{ $crust->name }}" {{ $index == 0 && $isAvailable ? 'checked' : '' }} {{ !$isAvailable ? 'disabled' : '' }} onchange="calculateTotal()">
                        <div class="opt-details">
                            <span class="opt-name" style="font-size:0.9rem;">{{ $crust->name }} {!! !$isAvailable ? '<span style="color:red; font-size:0.75rem;"><br>(Habis)</span>' : '' !!}</span>
                            <span class="opt-price" style="font-size:0.8rem; display:block; margin-top:5px;">+ Rp {{ number_format($crust->additional_price, 0, ',', '.') }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
                <input type="hidden" name="crust_price" id="hiddenCrustPrice" value="{{ $crusts[0]->additional_price ?? 0 }}">
                <input type="hidden" name="crust_name" id="hiddenCrustName" value="{{ $crusts[0]->name ?? '' }}">
            </div>
            @endif

            <!-- Toppings -->
            @if($toppings->count() > 0)
            <div class="mb-4">
                <div class="section-title">
                    <span>Tambah Topping</span>
                </div>
                @foreach($toppings as $topping)
                @php
                    $isAvailable = $topping->isAvailable();
                @endphp
                <label class="option-box" style="{{ !$isAvailable ? 'opacity: 0.5; cursor: not-allowed; background: #222;' : '' }}">
                    <input type="checkbox" name="toppings[]" value="{{ $topping->id }}" class="topping-cb" data-price="{{ $topping->additional_price }}" data-name="{{ $topping->name }}" {{ !$isAvailable ? 'disabled' : '' }} onchange="calculateTotal()">
                    <div class="opt-details">
                        <span class="opt-name">{{ $topping->name }} {!! !$isAvailable ? '<span style="color:red; font-size:0.8rem;">(Habis)</span>' : '' !!}</span>
                    </div>
                    <span class="opt-price">+ Rp {{ number_format($topping->additional_price, 0, ',', '.') }}</span>
                </label>
                @endforeach
            </div>
            @endif
            
            <div id="toppingInputsContainer"></div>

        </form>

        <!-- Ulasan Pelanggan -->
        <div class="review-section">
            <h3 style="color:#fff; font-family:'Playfair Display', serif; margin-bottom:20px;">Ulasan Pelanggan</h3>
            @if(isset($reviews) && $reviews->count() > 0)
                <div style="margin-bottom: 15px; font-size:0.9rem; color:var(--gray);">
                    Total {{ $menu->reviews_count }} ulasan | Rata-rata: 
                    <span style="color:var(--gold); font-weight:bold;">{{ number_format($menu->rating_avg, 1) }} ★</span>
                </div>
                @foreach($reviews as $rev)
                    <div class="review-card">
                        <div class="review-stars">
                            @for($i=1; $i<=5; $i++)
                                @if($i <= $rev->rating)
                                    <i class="fa-solid fa-star"></i>
                                @else
                                    <i class="fa-regular fa-star" style="color:#555;"></i>
                                @endif
                            @endfor
                        </div>
                        <div class="review-user">{{ $rev->user->name ?? 'Pelanggan' }} <span style="color:#666; font-size:0.75rem; font-weight:normal;">• {{ $rev->created_at->diffForHumans() }}</span></div>
                        @if($rev->comment)
                            <div class="review-text">{{ $rev->comment }}</div>
                        @endif
                    </div>
                @endforeach
            @else
                <p style="color:var(--gray); font-size:0.9rem; text-align:center; padding: 20px 0; border:1px dashed #333; border-radius:8px;">Belum ada ulasan untuk menu ini.</p>
            @endif
        </div>
    </div>
</div>

<div class="bottom-bar">
    <div class="bar-content">
        <div>
            <div class="total-label">TOTAL HARGA</div>
            <div class="total-price" id="displayTotalPrice">Rp {{ number_format($menu->base_price, 0, ',', '.') }}</div>
        </div>
        <div style="display: flex; gap: 12px;">
            <button type="button" onclick="document.getElementById('actionType').value='add_only'; document.getElementById('addToCartForm').submit()" class="btn" style="padding: 12px 16px; font-size: 0.95rem; border-radius: 8px; background: transparent; color: white; border: 1px solid #555; font-weight: 700; transition: all 0.3s;"><i class="fa-solid fa-cart-plus" style="margin-right: 5px;"></i> Tambah</button>
            <button type="button" onclick="document.getElementById('actionType').value='checkout'; document.getElementById('addToCartForm').submit()" class="btn btn-primary" style="padding: 12px 24px; font-size: 1.05rem; border-radius: 8px; font-weight: 800; box-shadow: 0 4px 12px rgba(255, 71, 87, 0.2);">Beli <i class="fa-solid fa-arrow-right" style="margin-left: 5px;"></i></button>
        </div>
    </div>
</div>

<script>
    const basePrice = {{ $menu->base_price }};
    
    function calculateTotal() {
        let total = basePrice;
        let qty = parseInt(document.getElementById('qtyInput').value);
        
        // Size
        const sizeRadio = document.querySelector('input[name="size"]:checked');
        if (sizeRadio) {
            let p = parseFloat(sizeRadio.dataset.price);
            total += p;
            document.getElementById('hiddenSizePrice').value = p;
            document.getElementById('hiddenSizeName').value = sizeRadio.dataset.name;
        }
        
        // Crust
        const crustRadio = document.querySelector('input[name="crust"]:checked');
        if (crustRadio) {
            let p = parseFloat(crustRadio.dataset.price);
            total += p;
            document.getElementById('hiddenCrustPrice').value = p;
            document.getElementById('hiddenCrustName').value = crustRadio.dataset.name;
        }

        // Temperature
        const tempRadio = document.querySelector('input[name="temperature"]:checked');
        if (tempRadio) {
            let p = parseFloat(tempRadio.dataset.price);
            total += p;
            document.getElementById('hiddenTempPrice').value = p;
            document.getElementById('hiddenTempName').value = tempRadio.dataset.name;
        }
        
        // Toppings
        const toppingCbs = document.querySelectorAll('.topping-cb:checked');
        const container = document.getElementById('toppingInputsContainer');
        container.innerHTML = '';
        
        toppingCbs.forEach(cb => {
            let p = parseFloat(cb.dataset.price);
            total += p;
            
            let iprice = document.createElement('input');
            iprice.type = 'hidden';
            iprice.name = 'topping_prices[]';
            iprice.value = p;
            container.appendChild(iprice);
            
            let iname = document.createElement('input');
            iname.type = 'hidden';
            iname.name = 'topping_names[]';
            iname.value = cb.dataset.name;
            container.appendChild(iname);
        });
        
        total = total * qty;
        
        document.getElementById('hiddenTotalPrice').value = total;
        document.getElementById('displayTotalPrice').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    }
    
    document.getElementById('btnMinus').addEventListener('click', function() {
        let input = document.getElementById('qtyInput');
        let val = parseInt(input.value);
        if (val > 1) {
            input.value = val - 1;
            calculateTotal();
        }
    });
    
    const maxStock = {{ $menu->daily_stock > 0 ? $menu->daily_stock : 999 }};

    document.getElementById('btnPlus').addEventListener('click', function() {
        let input = document.getElementById('qtyInput');
        let val = parseInt(input.value);
        if (val < maxStock) {
            input.value = val + 1;
            calculateTotal();
        } else {
            alert('Maaf, sisa stok hari ini hanya ' + maxStock + ' porsi.');
        }
    });
    
    // Init calc
    calculateTotal();
</script>
@endsection
