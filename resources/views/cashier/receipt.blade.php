<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota #{{ $order->id }} - {{ $storeName }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: #f4f5f7;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 30px;
            min-height: 100vh;
        }
        
        .receipt {
            background: white;
            width: 350px;
            padding: 30px 25px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed #dfe4ea;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .receipt-brand {
            font-size: 1.8rem;
            font-weight: 800;
            color: #c00a27;
            letter-spacing: 2px;
        }
        .receipt-subtitle {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .receipt-info {
            margin-bottom: 15px;
            font-size: 0.85rem;
        }
        .receipt-info-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
        }
        .receipt-info-label { color: #6c757d; }
        .receipt-info-value { font-weight: 600; }
        
        .receipt-divider {
            border: none;
            border-top: 1px dashed #dfe4ea;
            margin: 15px 0;
        }
        
        .receipt-items {
            margin-bottom: 15px;
        }
        .receipt-item {
            padding: 8px 0;
            border-bottom: 1px dotted #f1f2f6;
        }
        .receipt-item:last-child { border-bottom: none; }
        .receipt-item-name {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 3px;
        }
        .receipt-item-detail {
            display: flex;
            justify-content: space-between;
            color: #6c757d;
            font-size: 0.8rem;
        }
        .receipt-item-notes {
            font-size: 0.75rem;
            color: #a5b1c2;
            margin-top: 2px;
        }
        
        .receipt-total {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .receipt-total-row {
            display: flex;
            justify-content: space-between;
            font-size: 1.3rem;
            font-weight: 800;
        }
        .receipt-total-amount { color: #c00a27; }
        
        .receipt-payment {
            text-align: center;
            padding: 10px;
            background: #e6f4ea;
            border-radius: 8px;
            font-weight: 600;
            color: #1b7339;
            font-size: 0.85rem;
            margin-bottom: 15px;
        }
        
        .receipt-footer {
            text-align: center;
            border-top: 2px dashed #dfe4ea;
            padding-top: 20px;
            font-size: 0.8rem;
            color: #6c757d;
        }
        .receipt-footer p { margin-bottom: 5px; }
        .receipt-footer .thank-you {
            font-size: 1rem;
            font-weight: 700;
            color: #1e1e1e;
            margin-bottom: 8px;
        }
        
        .print-actions {
            text-align: center;
            margin-top: 20px;
        }
        .print-btn {
            background: #c00a27;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .print-btn:hover { background: #a00820; }
        
        @media print {
            body { background: white; padding: 0; }
            .receipt { box-shadow: none; border-radius: 0; width: 100%; max-width: 80mm; padding: 10px; }
            .print-actions { display: none; }
            .receipt-brand { font-size: 1.4rem; }
        }
    </style>
</head>
<body>
    <div>
        <div class="receipt">
            <div class="receipt-header">
                @php
                    $storeName = \App\Models\Setting::get('store_name', 'PIZZARIA');
                    $storeAddress = \App\Models\Setting::get('store_address', 'Jl. Contoh No. 123, Kota Anda');
                    $storePhone = \App\Models\Setting::get('store_phone', '(021) 1234-5678');
                @endphp
                <div class="receipt-brand">{{ $storeName }}</div>
                <div class="receipt-subtitle">{{ $storeAddress }}</div>
                <div class="receipt-subtitle">Telp: {{ $storePhone }}</div>
            </div>
            
            <div class="receipt-info">
                <div class="receipt-info-row">
                    <span class="receipt-info-label">No. Order</span>
                    <span class="receipt-info-value">#ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Tanggal</span>
                    <span class="receipt-info-value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Meja</span>
                    <span class="receipt-info-value">{{ $order->table ? $order->table->table_number : '-' }}</span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Customer</span>
                    <span class="receipt-info-value">{{ $order->customer_name }}</span>
                </div>
            </div>
            
            <hr class="receipt-divider">
            
            <div class="receipt-items">
                @foreach($order->items as $item)
                <div class="receipt-item">
                    <div class="receipt-item-name">{{ $item->menu->name ?? 'Unknown' }}</div>
                    <div class="receipt-item-detail">
                        <span>{{ $item->qty }} x Rp {{ number_format($item->subtotal / $item->qty, 0, ',', '.') }}</span>
                        <span style="font-weight: 600;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($item->customization_notes)
                        @php $notes = is_string($item->customization_notes) ? json_decode($item->customization_notes, true) : $item->customization_notes; @endphp
                        @if($notes && is_array($notes))
                        <div class="receipt-item-notes">
                            @foreach($notes as $key => $val)
                                {{ $key }}: {{ is_array($val) ? implode(', ', $val) : $val }} &nbsp;
                            @endforeach
                        </div>
                        @endif
                    @endif
                </div>
                @endforeach
            </div>
            
            <hr class="receipt-divider">
            
            <div class="receipt-total">
                @if($order->discount_amount > 0)
                <div class="receipt-info-row" style="margin-bottom: 5px;">
                    <span class="receipt-info-label" style="font-size: 0.9rem;">Subtotal</span>
                    <span class="receipt-info-value" style="font-size: 0.9rem;">Rp {{ number_format($order->items->sum('subtotal'), 0, ',', '.') }}</span>
                </div>
                <div class="receipt-info-row" style="margin-bottom: 10px;">
                    <span class="receipt-info-label" style="font-size: 0.9rem;">Diskon ({{ $order->promotion->code ?? 'Promo' }})</span>
                    <span class="receipt-info-value" style="font-size: 0.9rem; color: #1b7339;">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                </div>
                <hr style="border: none; border-top: 1px dashed #dfe4ea; margin-bottom: 10px;">
                @endif
                <div class="receipt-total-row">
                    <span>TOTAL</span>
                    <span class="receipt-total-amount">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
            
            @php
                $methodLabels = ['cash' => '💵 Tunai (Cash)', 'qris' => '📱 QRIS', 'debit' => '💳 Kartu Debit', 'credit' => '💳 Kartu Kredit'];
            @endphp
            <div class="receipt-payment">
                Dibayar via: {{ $methodLabels[$order->payment_method] ?? '💰 Tunai' }}
            </div>
            
            <div class="receipt-footer">
                <p class="thank-you">Terima Kasih! 🍕</p>
                <p>Selamat menikmati hidangan Anda.</p>
                <p>Sampai jumpa kembali di {{ \App\Models\Setting::get('store_name', 'Pizzaria') }}!</p>
            </div>
        </div>
        
        <div class="print-actions">
            <button class="print-btn" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Cetak Nota
            </button>
            <a href="{{ route('cashier.pos') }}" style="background: #e9ecef; color: #1e1e1e; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; margin-left: 10px; font-family: inherit;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Kasir
            </a>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>
