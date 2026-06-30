<div class="quick-view-content">
    <div class="quick-view-header">
        <h2 class="quick-view-title">{{ $menu->name }}</h2>
        @if($menu->hasActiveDiscount())
            <p class="quick-view-price" style="display: flex; flex-direction: column; gap: 2px;">
                <span style="text-decoration: line-through; color: #888; font-size: 0.9rem;">Rp {{ number_format($menu->base_price, 0, ',', '.') }}</span>
                <span style="color: var(--primary);">Rp {{ number_format($menu->final_price, 0, ',', '.') }}</span>
            </p>
        @else
            <p class="quick-view-price">Rp {{ number_format($menu->base_price, 0, ',', '.') }}</p>
        @endif
    </div>
    
    <div class="quick-view-body">
        <form id="quick-view-form">
            @csrf
            <input type="hidden" name="menu_id" value="{{ $menu->id }}">
            <input type="hidden" name="action" value="cart">

            @if($sizes->count() > 0)
            <div class="qv-section">
                <label>Ukuran</label>
                <div class="qv-options">
                    @foreach($sizes as $size)
                    <label class="qv-option">
                        <input type="radio" name="size" value="{{ $size->id }}" {{ $loop->first ? 'checked' : '' }}>
                        <span>{{ $size->name }} 
                            @if($size->additional_price > 0) (+Rp {{ number_format($size->additional_price, 0, ',', '.') }}) @endif
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            @if($crusts->count() > 0)
            <div class="qv-section">
                <label>Pinggiran (Crust)</label>
                <div class="qv-options">
                    @foreach($crusts as $crust)
                    <label class="qv-option">
                        <input type="radio" name="crust" value="{{ $crust->id }}">
                        <span>{{ $crust->name }}
                            @if($crust->additional_price > 0) (+Rp {{ number_format($crust->additional_price, 0, ',', '.') }}) @endif
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            @if($toppings->count() > 0)
            <div class="qv-section">
                <label>Extra Topping</label>
                <div class="qv-options-grid">
                    @foreach($toppings as $topping)
                    <label class="qv-checkbox">
                        <input type="checkbox" name="toppings[]" value="{{ $topping->id }}">
                        <span>{{ $topping->name }}
                            @if($topping->additional_price > 0) <br><small>+Rp {{ number_format($topping->additional_price, 0, ',', '.') }}</small> @endif
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="qv-section">
                <label>Jumlah</label>
                <div class="qv-qty-controls">
                    <button type="button" class="qv-qty-btn minus"><i class="fa-solid fa-minus"></i></button>
                    <input type="number" name="qty" value="1" min="1" class="qv-qty-input" readonly>
                    <button type="button" class="qv-qty-btn plus"><i class="fa-solid fa-plus"></i></button>
                </div>
            </div>

            <button type="button" class="btn-add-to-cart-ajax" onclick="submitQuickViewForm()">
                <i class="fa-solid fa-cart-plus"></i> Tambah ke Keranjang
            </button>
        </form>
    </div>
</div>
