<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #d35400;
        }
        .header p {
            margin: 2px 0;
            color: #7f8c8d;
        }
        .divider {
            border-top: 1px dashed #bdc3c7;
            margin: 15px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            text-align: left;
            padding: 8px 0;
        }
        table th {
            border-bottom: 1px solid #bdc3c7;
        }
        .text-right {
            text-align: right;
        }
        .summary {
            margin-top: 15px;
            width: 50%;
            float: right;
        }
        .summary table td {
            padding: 4px 0;
        }
        .footer {
            clear: both;
            text-align: center;
            margin-top: 50px;
            font-size: 10px;
            color: #95a5a6;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            background-color: #27ae60;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Omnichannel Pizzeria</h1>
        <p>Jl. Contoh E-Commerce No. 123, Jakarta</p>
        <p>Telp: 0812-3456-7890</p>
        <div class="divider"></div>
        <h2>BUKTI PEMBAYARAN SAH</h2>
        <span class="badge">LUNAS</span>
    </div>

    <table style="margin-bottom: 20px;">
        <tr>
            <td width="50%">
                <strong>Order ID:</strong> {{ $order->order_number }}<br>
                <strong>Tanggal:</strong> {{ $order->created_at->format('d M Y H:i') }}<br>
                <strong>Tipe Pesanan:</strong> {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}
                @if($order->isDineIn() && $order->table)
                    (Meja {{ $order->table->table_number }})
                @endif
            </td>
            <td width="50%" class="text-right">
                <strong>Pelanggan:</strong> {{ $order->customer_name ?? 'Guest' }}<br>
                <strong>Metode Bayar:</strong> {{ strtoupper($order->payment_method ?? 'Cash') }}<br>
                <strong>Waktu Bayar:</strong> {{ $order->paid_at ? $order->paid_at->format('d M Y H:i') : '-' }}
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->menu->name }}</strong>
                    @if($item->customization_notes)
                        @php $notes = json_decode($item->customization_notes, true); @endphp
                        @if($notes)
                            <div style="font-size: 10px; color: #7f8c8d; margin-top: 2px;">
                                @foreach($notes as $key => $val)
                                    @if(is_array($val))
                                        {{ $key }}: {{ implode(', ', $val) }}<br>
                                    @else
                                        {{ $key }}: {{ $val }}<br>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endif
                </td>
                <td class="text-right">{{ $item->qty }}</td>
                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <div class="summary">
        <table>
            <tr>
                <td>Subtotal</td>
                <td class="text-right">Rp {{ number_format($order->subtotal_amount, 0, ',', '.') }}</td>
            </tr>
            @if($order->delivery_fee > 0)
            <tr>
                <td>Ongkos Kirim</td>
                <td class="text-right">Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($order->discount_amount > 0)
            <tr>
                <td>Diskon</td>
                <td class="text-right" style="color: #c0392b;">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td style="font-weight: bold; font-size: 14px;">Total Akhir</td>
                <td class="text-right" style="font-weight: bold; font-size: 14px;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Terima kasih telah berbelanja di Omnichannel Pizzeria.</p>
        <p>Struk ini dicetak secara digital dan sah sebagai bukti pembayaran.</p>
    </div>

</body>
</html>
