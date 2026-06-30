<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #c00a27; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #c00a27; font-size: 24px; }
        .header p { margin: 5px 0 0 0; color: #666; }
        
        .summary-box { border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; background: #f9f9f9; width: 100%; }
        .summary-box table { width: 100%; }
        .summary-box td { padding: 5px; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .data-table th { background-color: #f2f2f2; font-weight: bold; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .footer { text-align: center; font-size: 10px; color: #999; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ strtoupper($storeName) }}</h1>
        <p>Laporan Penjualan Keseluruhan</p>
        <p>
            Periode: 
            {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal' }} 
            s/d 
            {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Sekarang' }}
        </p>
    </div>

    <div class="summary-box">
        <table>
            <tr>
                <td width="30%"><strong>Total Transaksi:</strong></td>
                <td width="20%">{{ $orders->count() }}</td>
                <td width="25%"><strong>Total Pendapatan:</strong></td>
                <td width="25%" class="text-right">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Total Diskon Diberikan:</strong></td>
                <td>Rp {{ number_format($totalDiscount, 0, ',', '.') }}</td>
                <td><strong>Rata-rata Transaksi:</strong></td>
                <td class="text-right">Rp {{ $orders->count() > 0 ? number_format($totalRevenue / $orders->count(), 0, ',', '.') : 0 }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Waktu</th>
                <th width="15%">No. Order</th>
                <th width="20%">Customer</th>
                <th width="15%">Metode</th>
                <th width="15%" class="text-right">Diskon</th>
                <th width="15%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>#ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $order->customer_name ?? 'Guest' }}</td>
                <td>{{ strtoupper($order->payment_method) }}</td>
                <td class="text-right">Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }} oleh Sistem Admin {{ $storeName }}
    </div>

</body>
</html>
