@extends('cashier.layouts.app')
@section('title', 'POS Terminal')
@section('content')
<style>
    .pos-container {
        display: flex;
        gap: 20px;
        height: calc(100vh - 120px);
    }
    
    /* Left Side: Menus */
    .pos-menu-area {
        flex: 1;
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    .pos-categories {
        display: flex;
        gap: 10px;
        padding: 15px;
        background: #fafafa;
        border-bottom: 1px solid var(--border-color);
        overflow-x: auto;
    }
    
    .category-btn {
        padding: 8px 15px;
        border: 1px solid var(--border-color);
        background: white;
        border-radius: 20px;
        font-weight: 600;
        color: var(--text-muted);
        cursor: pointer;
        white-space: nowrap;
        transition: 0.2s;
    }
    
    .category-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }
    
    .category-btn.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }
    
    .pos-grid {
        padding: 15px;
        overflow-y: auto;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
        flex: 1;
    }
    
    .menu-card {
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 10px;
        cursor: pointer;
        position: relative;
        transition: 0.2s;
        text-align: center;
        background: white;
        user-select: none;
    }
    
    .menu-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(192, 10, 39, 0.1);
    }
    
    .menu-card.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        filter: grayscale(1);
    }
    
    .menu-img {
        width: 100%;
        height: 100px;
        object-fit: contain;
        margin-bottom: 10px;
    }
    
    .menu-title {
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 5px;
        line-height: 1.2;
        color: var(--text-main);
    }
    
    .menu-price {
        color: var(--primary);
        font-weight: 800;
        font-size: 0.95rem;
    }
    
    .stock-badge {
        position: absolute;
        top: 5px;
        right: 5px;
        background: #f1f2f6;
        color: var(--text-muted);
        font-size: 0.7rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
    }
    
    /* Right Side: Cart */
    .pos-cart-area {
        width: 380px;
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    .cart-header {
        padding: 15px;
        background: var(--text-main);
        color: white;
        font-weight: 700;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .cart-body {
        flex: 1;
        overflow-y: auto;
        padding: 15px;
    }
    
    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 12px;
    }
    
    .cart-item-name {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--text-main);
    }
    
    .cart-item-price {
        font-size: 0.8rem;
        color: var(--text-muted);
    }
    
    .qty-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8f9fa;
        border-radius: 20px;
        padding: 3px 10px;
        border: 1px solid var(--border-color);
    }
    
    .qty-btn {
        background: none;
        border: none;
        color: var(--primary);
        cursor: pointer;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
    }
    
    .cart-footer {
        padding: 15px;
        border-top: 1px solid var(--border-color);
        background: #fafafa;
    }
    
    .cart-total-row {
        display: flex;
        justify-content: space-between;
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 15px;
    }
    
    .checkout-btn {
        width: 100%;
        padding: 15px;
        font-size: 1.1rem;
        border-radius: 8px;
    }
    
    .customer-form {
        margin-bottom: 15px;
    }
    
    .customer-form select, .customer-form input {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        margin-bottom: 10px;
        font-family: inherit;
    }
    
    /* Smart POS Modal */
    .modal-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        display: none; justify-content: center; align-items: center; z-index: 1000;
    }
    .modal-box {
        background: white; border-radius: 12px; width: 400px; padding: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    .modal-box h3 {
        margin-bottom: 20px; font-weight: 800; text-align: center;
    }
    .quick-cash-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;
    }
    .quick-cash-btn {
        padding: 10px; border: 1px solid var(--border-color); border-radius: 6px;
        background: white; cursor: pointer; font-weight: 600; font-family: inherit; transition: 0.2s;
    }
    .quick-cash-btn:hover, .quick-cash-btn.selected {
        background: #fdf2f2; border-color: var(--primary); color: var(--primary);
    }
</style>

<div class="pos-container">
    <!-- Kiri: Menu -->
    <div class="pos-menu-area">
        <div class="pos-categories">
            <button class="category-btn active" onclick="filterCategory('all')">Semua Menu</button>
            @foreach($categories as $cat)
                <button class="category-btn" onclick="filterCategory({{ $cat->id }})">
                    <i class="{{ $cat->icon }}"></i> {{ $cat->name }}
                </button>
            @endforeach
        </div>
        
        <div class="pos-grid" id="menuGrid">
            @foreach($menus as $menu)
                <div class="menu-card {{ $menu->daily_stock <= 0 ? 'disabled' : '' }}" 
                     data-category="{{ $menu->category_id }}"
                     onclick="{{ $menu->daily_stock > 0 ? "addToCart({$menu->id}, '{$menu->name}', {$menu->base_price}, {$menu->daily_stock})" : "return false" }}">
                    <div class="stock-badge">Stok: {{ $menu->daily_stock }}</div>
                    @if($menu->image_url)
                        <img src="{{ asset('storage/'.$menu->image_url) }}" class="menu-img" alt="{{ $menu->name }}">
                    @else
                        <div class="menu-img" style="background: #f1f2f6; display: flex; align-items: center; justify-content: center; color: var(--text-muted); border-radius: 8px;">
                            <i class="fa-solid fa-image fa-2x"></i>
                        </div>
                    @endif
                    <div class="menu-title">{{ $menu->name }}</div>
                    <div class="menu-price">Rp {{ number_format($menu->base_price, 0, ',', '.') }}</div>
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- Kanan: Cart -->
    <div class="pos-cart-area">
        <div class="cart-header">
            <span><i class="fa-solid fa-cart-shopping"></i> Keranjang</span>
            <button onclick="clearCart()" style="background: none; border: none; color: white; cursor: pointer; font-size: 0.8rem; font-weight: 700; text-decoration: underline;">Kosongkan</button>
        </div>
        
        <div class="cart-body" id="cartBody">
            <div style="text-align: center; color: var(--text-muted); margin-top: 50px;">
                <i class="fa-solid fa-basket-shopping fa-3x" style="color: #e5e7eb; margin-bottom: 15px;"></i>
                <p>Keranjang masih kosong</p>
            </div>
        </div>
        
        <div class="cart-footer">
            <div class="customer-form">
                <select id="orderType" onchange="toggleTableInput()">
                    <option value="dine_in">Dine-in (Makan di tempat)</option>
                    <option value="takeaway">Takeaway (Bawa pulang)</option>
                </select>
                
                <select id="tableSelect">
                    <option value="">Pilih Meja...</option>
                    @foreach($tables as $table)
                        <option value="{{ $table->id }}">Meja {{ $table->table_number }} (Kapasitas: {{ $table->capacity }})</option>
                    @endforeach
                </select>
                
                <input type="text" id="customerName" placeholder="Nama Pelanggan (Wajib)" required>
            </div>
            
            <div class="cart-total-row">
                <span>Total:</span>
                <span id="cartTotalText">Rp 0</span>
            </div>
            
            <button class="btn-primary checkout-btn" onclick="openPaymentModal()" id="btnCheckout" disabled>
                Lanjutkan Pembayaran
            </button>
        </div>
    </div>
</div>

<!-- Modal Smart POS -->
<div class="modal-overlay" id="paymentModal">
    <div class="modal-box">
        <h3>Proses Pembayaran</h3>
        
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px; border: 1px dashed var(--border-color);">
            <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 5px;">Total Tagihan</div>
            <div style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);" id="modalTotalTagihan">Rp 0</div>
        </div>
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 0.9rem;">Metode Pembayaran</label>
            <div style="display: flex; gap: 10px;">
                <button class="quick-cash-btn selected" style="flex: 1;" id="btnMethodCash" onclick="selectMethod('cash')">
                    <i class="fa-solid fa-money-bill-wave"></i> Tunai
                </button>
                <button class="quick-cash-btn" style="flex: 1;" id="btnMethodQris" onclick="selectMethod('qris')">
                    <i class="fa-solid fa-qrcode"></i> QRIS
                </button>
            </div>
        </div>
        
        <div id="cashArea">
            <label style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 0.9rem;">Uang Diterima (Pecahan Cepat)</label>
            <div class="quick-cash-grid">
                <button class="quick-cash-btn" onclick="setCash(100000)">Rp 100.000</button>
                <button class="quick-cash-btn" onclick="setCash(50000)">Rp 50.000</button>
                <button class="quick-cash-btn" id="btnUangPas" onclick="setCashExact()">Uang Pas</button>
            </div>
            
            <input type="number" id="inputCashTendered" placeholder="Atau ketik manual..." style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 1.1rem; margin-bottom: 15px; font-family: inherit;">
            
            <div style="background: #fef0f0; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; font-weight: 700;">
                    <span style="color: var(--primary);">Kembalian:</span>
                    <span style="color: var(--primary); font-size: 1.2rem;" id="kembalianText">Rp 0</span>
                </div>
            </div>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button class="btn-primary" style="flex: 1;" onclick="submitOrder()" id="btnSubmitFinal">Bayar & Cetak Struk</button>
            <button style="flex: 1; padding: 10px; border: 1px solid var(--border-color); background: white; border-radius: 6px; font-weight: 600; cursor: pointer; font-family: inherit;" onclick="closePaymentModal()">Batal</button>
        </div>
    </div>
</div>

<script>
    let cart = [];
    let cartTotal = 0;
    
    function filterCategory(catId) {
        document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        
        document.querySelectorAll('.menu-card').forEach(card => {
            if(catId === 'all' || card.getAttribute('data-category') == catId) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    function addToCart(id, name, price, maxStock) {
        let existing = cart.find(item => item.id === id);
        if (existing) {
            if (existing.qty < maxStock) {
                existing.qty++;
            } else {
                alert('Stok tidak mencukupi!');
            }
        } else {
            cart.push({ id, name, price, qty: 1, maxStock });
        }
        renderCart();
    }
    
    function changeQty(id, delta) {
        let index = cart.findIndex(item => item.id === id);
        if (index > -1) {
            let item = cart[index];
            let newQty = item.qty + delta;
            
            if (newQty <= 0) {
                cart.splice(index, 1);
            } else if (newQty > item.maxStock) {
                alert('Stok tidak mencukupi!');
            } else {
                item.qty = newQty;
            }
            renderCart();
        }
    }
    
    function clearCart() {
        if(confirm('Kosongkan keranjang?')) {
            cart = [];
            renderCart();
        }
    }
    
    function renderCart() {
        const cartBody = document.getElementById('cartBody');
        const cartTotalText = document.getElementById('cartTotalText');
        const btnCheckout = document.getElementById('btnCheckout');
        
        cartTotal = 0;
        
        if (cart.length === 0) {
            cartBody.innerHTML = `
                <div style="text-align: center; color: var(--text-muted); margin-top: 50px;">
                    <i class="fa-solid fa-basket-shopping fa-3x" style="color: #e5e7eb; margin-bottom: 15px;"></i>
                    <p>Keranjang masih kosong</p>
                </div>
            `;
            cartTotalText.innerText = 'Rp 0';
            btnCheckout.disabled = true;
            return;
        }
        
        btnCheckout.disabled = false;
        let html = '';
        
        cart.forEach(item => {
            let subtotal = item.price * item.qty;
            cartTotal += subtotal;
            
            html += `
                <div class="cart-item">
                    <div style="flex: 1;">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</div>
                    </div>
                    <div class="qty-controls">
                        <button class="qty-btn" onclick="changeQty(${item.id}, -1)"><i class="fa-solid fa-minus"></i></button>
                        <span style="font-weight: 700; font-size: 0.95rem; width: 20px; text-align: center;">${item.qty}</span>
                        <button class="qty-btn" onclick="changeQty(${item.id}, 1)"><i class="fa-solid fa-plus"></i></button>
                    </div>
                </div>
            `;
        });
        
        cartBody.innerHTML = html;
        cartTotalText.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(cartTotal);
    }
    
    function toggleTableInput() {
        const type = document.getElementById('orderType').value;
        const tableSelect = document.getElementById('tableSelect');
        if (type === 'takeaway') {
            tableSelect.style.display = 'none';
            tableSelect.value = '';
        } else {
            tableSelect.style.display = 'block';
        }
    }
    
    /* Smart POS Modal Logic */
    let selectedMethod = 'cash';
    let cashTendered = 0;
    
    function openPaymentModal() {
        if (!document.getElementById('customerName').value) {
            alert('Tolong isi Nama Pelanggan!');
            document.getElementById('customerName').focus();
            return;
        }
        if (document.getElementById('orderType').value === 'dine_in' && !document.getElementById('tableSelect').value) {
            alert('Tolong pilih Nomor Meja untuk Dine-in!');
            return;
        }
        
        document.getElementById('paymentModal').style.display = 'flex';
        document.getElementById('modalTotalTagihan').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(cartTotal);
        
        document.getElementById('btnUangPas').innerText = 'Uang Pas (' + new Intl.NumberFormat('id-ID').format(cartTotal) + ')';
        selectMethod('cash');
        setCashExact();
    }
    
    function closePaymentModal() {
        document.getElementById('paymentModal').style.display = 'none';
    }
    
    function selectMethod(method) {
        selectedMethod = method;
        document.getElementById('btnMethodCash').classList.remove('selected');
        document.getElementById('btnMethodQris').classList.remove('selected');
        
        if (method === 'cash') {
            document.getElementById('btnMethodCash').classList.add('selected');
            document.getElementById('cashArea').style.display = 'block';
            calculateChange();
        } else {
            document.getElementById('btnMethodQris').classList.add('selected');
            document.getElementById('cashArea').style.display = 'none';
            document.getElementById('btnSubmitFinal').disabled = false;
        }
    }
    
    function setCash(amount) {
        document.getElementById('inputCashTendered').value = amount;
        calculateChange();
    }
    
    function setCashExact() {
        document.getElementById('inputCashTendered').value = cartTotal;
        calculateChange();
    }
    
    document.getElementById('inputCashTendered').addEventListener('input', calculateChange);
    
    function calculateChange() {
        if (selectedMethod !== 'cash') return;
        
        cashTendered = parseFloat(document.getElementById('inputCashTendered').value) || 0;
        let change = cashTendered - cartTotal;
        let changeText = document.getElementById('kembalianText');
        let btnSubmit = document.getElementById('btnSubmitFinal');
        
        if (change < 0) {
            changeText.innerText = 'Uang Kurang!';
            changeText.style.color = '#e63946';
            btnSubmit.disabled = true;
        } else {
            changeText.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(change);
            changeText.style.color = 'var(--primary)';
            btnSubmit.disabled = false;
        }
    }
    
    function submitOrder() {
        const btnSubmit = document.getElementById('btnSubmitFinal');
        btnSubmit.disabled = true;
        btnSubmit.innerText = 'Memproses...';
        
        const payload = {
            order_type: document.getElementById('orderType').value,
            customer_name: document.getElementById('customerName').value,
            table_id: document.getElementById('tableSelect').value,
            payment_method: selectedMethod,
            cash_tendered: selectedMethod === 'cash' ? cashTendered : null,
            cart: JSON.stringify(cart)
        };
        
        fetch('{{ route("cashier.pos.checkout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Berhasil, arahkan ke cetak struk (buka di tab baru atau redirect)
                window.location.href = data.redirect_url;
            } else {
                alert('Gagal: ' + data.message);
                btnSubmit.disabled = false;
                btnSubmit.innerText = 'Bayar & Cetak Struk';
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan jaringan.');
            btnSubmit.disabled = false;
            btnSubmit.innerText = 'Bayar & Cetak Struk';
        });
    }
</script>
@endsection
