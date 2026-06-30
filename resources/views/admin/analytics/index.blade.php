@extends('admin.layouts.app')
@section('title', 'Sales Analytics')
@section('content')
<style>
    .analytics-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .analytics-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .analytics-subtitle {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin-top: 5px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        position: relative;
        overflow: hidden;
    }
    .stat-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--primary);
    }
    .stat-title {
        color: var(--text-muted);
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .stat-value {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 15px;
    }
    
    .target-box {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 10px 15px;
        margin-top: 10px;
    }
    .target-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .progress-bar-bg {
        width: 100%;
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 5px;
    }
    .progress-bar-fill {
        height: 100%;
        background: var(--primary);
        border-radius: 4px;
    }
    .target-text {
        font-size: 0.8rem;
        display: flex;
        justify-content: space-between;
        font-weight: 600;
    }
    
    .report-section {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        padding: 25px;
    }
    .section-title {
        font-size: 1.2rem;
        font-weight: 800;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .tx-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .tx-table th {
        background: #f8f9fa;
        padding: 15px;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        border-bottom: 2px solid var(--border-color);
    }
    .tx-table td {
        padding: 15px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
        font-size: 0.95rem;
    }
    .tx-table tr:hover {
        background: #fafafa;
    }
    .tx-id {
        font-weight: 700;
        color: var(--primary);
    }
    .tx-total {
        font-weight: 800;
        color: var(--text-main);
    }
</style>

<div class="analytics-header">
    <div>
        <div class="analytics-title">
            <i class="fa-solid fa-chart-line" style="color: var(--primary);"></i>
            Laporan Analitik & Penjualan
        </div>
        <div class="analytics-subtitle">Pusat data mendalam untuk memonitor performa target dan transaksi finansial restoran.</div>
    </div>
    
    <div style="background: white; padding: 15px; border-radius: 10px; border: 1px solid var(--border-color); display: flex; gap: 15px; align-items: flex-end;">
        <form id="exportForm" method="GET" action="" style="display: flex; gap: 10px; align-items: flex-end; margin: 0;">
            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); margin-bottom: 5px;">Mulai Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" style="padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border-color); font-family: inherit;">
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); margin-bottom: 5px;">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" style="padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border-color); font-family: inherit;">
            </div>
            
            <button type="submit" onclick="document.getElementById('exportForm').action='{{ route('admin.analytics.index') }}'" class="btn-primary" style="display: flex; align-items: center; gap: 8px; padding: 10px 15px; background: var(--primary);">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            <button type="submit" onclick="document.getElementById('exportForm').action='{{ route('admin.analytics.exportPdf') }}'" class="btn-primary" style="display: flex; align-items: center; gap: 8px; padding: 10px 15px; background: #c00a27;">
                <i class="fa-solid fa-file-pdf"></i> Export PDF
            </button>
            <button type="submit" onclick="document.getElementById('exportForm').action='{{ route('admin.analytics.exportExcel') }}'" class="btn-primary" style="display: flex; align-items: center; gap: 8px; padding: 10px 15px; background: #1b7339;">
                <i class="fa-solid fa-file-excel"></i> Export Excel
            </button>
        </form>
    </div>
</div>

<div class="stats-grid">
    <!-- Daily -->
    <div class="stat-card">
        <div class="stat-title">
            Hari Ini 
            <i class="fa-solid fa-calendar-day" style="color: var(--primary);"></i>
        </div>
        <div class="stat-value">Rp {{ number_format($sales['daily'], 0, ',', '.') }}</div>
        
        <div class="target-box">
            <div class="target-label">Pencapaian Target Harian</div>
            @php $daily_pct = min(100, ($sales['daily'] / $targets['daily']) * 100); @endphp
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="width: {{ $daily_pct }}%; {{ $daily_pct == 100 ? 'background: var(--green);' : '' }}"></div>
            </div>
            <div class="target-text">
                <span style="color: var(--text-muted);">Rp {{ number_format($targets['daily']/1000000, 1, ',', '.') }} Juta</span>
                <span style="color: {{ $daily_pct == 100 ? 'var(--green)' : 'var(--text-main)' }}">{{ number_format($daily_pct, 1) }}%</span>
            </div>
        </div>
    </div>

    <!-- Weekly -->
    <div class="stat-card">
        <div class="stat-title">
            Minggu Ini
            <i class="fa-solid fa-calendar-week" style="color: var(--primary);"></i>
        </div>
        <div class="stat-value">Rp {{ number_format($sales['weekly'], 0, ',', '.') }}</div>
        
        <div class="target-box">
            <div class="target-label">Pencapaian Target Mingguan</div>
            @php $weekly_pct = min(100, ($sales['weekly'] / $targets['weekly']) * 100); @endphp
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="width: {{ $weekly_pct }}%; {{ $weekly_pct == 100 ? 'background: var(--green);' : '' }}"></div>
            </div>
            <div class="target-text">
                <span style="color: var(--text-muted);">Rp {{ number_format($targets['weekly']/1000000, 1, ',', '.') }} Juta</span>
                <span style="color: {{ $weekly_pct == 100 ? 'var(--green)' : 'var(--text-main)' }}">{{ number_format($weekly_pct, 1) }}%</span>
            </div>
        </div>
    </div>

    <!-- Monthly -->
    <div class="stat-card">
        <div class="stat-title">
            Bulan Ini
            <i class="fa-solid fa-calendar-days" style="color: var(--primary);"></i>
        </div>
        <div class="stat-value">Rp {{ number_format($sales['monthly'], 0, ',', '.') }}</div>
        
        <div class="target-box">
            <div class="target-label">Pencapaian Target Bulanan</div>
            @php $monthly_pct = min(100, ($sales['monthly'] / $targets['monthly']) * 100); @endphp
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="width: {{ $monthly_pct }}%; {{ $monthly_pct == 100 ? 'background: var(--green);' : '' }}"></div>
            </div>
            <div class="target-text">
                <span style="color: var(--text-muted);">Rp {{ number_format($targets['monthly']/1000000, 1, ',', '.') }} Juta</span>
                <span style="color: {{ $monthly_pct == 100 ? 'var(--green)' : 'var(--text-main)' }}">{{ number_format($monthly_pct, 1) }}%</span>
            </div>
        </div>
    </div>

    <!-- All Time -->
    <div class="stat-card" style="background: linear-gradient(135deg, #1e1e1e, #2d2d2d); color: white;">
        <div class="stat-title" style="color: #aaa;">
            Total Sepanjang Masa
            <i class="fa-solid fa-vault" style="color: #ffc107;"></i>
        </div>
        <div class="stat-value" style="color: white; font-size: 1.8rem; margin-top: 20px;">
            Rp {{ number_format($sales['all_time'], 0, ',', '.') }}
        </div>
        <div style="margin-top: 30px; font-size: 0.8rem; color: #888;">
            <i class="fa-solid fa-circle-info"></i> Total dari seluruh orderan berstatus Paid.
        </div>
    </div>
</div>

<div class="report-section" style="margin-bottom: 30px;">
    <div class="section-title">
        <i class="fa-solid fa-chart-area" style="color: var(--primary);"></i>
        Grafik Tren Penjualan
    </div>
    <div style="width: 100%; height: 350px;">
        <canvas id="salesChart"></canvas>
    </div>
</div>

<div class="report-section">
    <div class="section-title">
        <i class="fa-solid fa-receipt"></i>
        Log Transaksi Terakhir
    </div>
    
    <div style="overflow-x: auto;">
        <table class="tx-table">
            <thead>
                <tr>
                    <th>Waktu (WIB)</th>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Meja</th>
                    <th style="text-align: right;">Total Nilai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentTransactions as $tx)
                <tr>
                    <td style="color: var(--text-muted);">{{ \Carbon\Carbon::parse($tx->created_at)->format('d M Y, H:i') }}</td>
                    <td class="tx-id">#ORD-{{ str_pad($tx->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td style="font-weight: 600;">{{ $tx->customer_name }}</td>
                    <td>
                        @if($tx->table_number)
                            <span style="background: #f1f2f6; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.8rem;">Meja {{ $tx->table_number }}</span>
                        @else
                            <span style="color: var(--text-muted); font-size: 0.85rem;">Takeaway / No Table</span>
                        @endif
                    </td>
                    <td class="tx-total" style="text-align: right;">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                
                @if($recentTransactions->count() == 0)
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-muted);">
                        Belum ada transaksi yang lunas.
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // Data dari Controller
        const labels = {!! json_encode($chartLabels) !!};
        const data = {!! json_encode($chartData) !!};
        
        // Buat Gradient Merah
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(230, 57, 70, 0.5)'); // primary color transparan
        gradient.addColorStop(1, 'rgba(230, 57, 70, 0.0)');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: data,
                    borderColor: '#e63946',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#e63946',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // membuat kurva mulus (bezier)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1d3557',
                        titleFont: { size: 13, family: 'Inter' },
                        bodyFont: { size: 14, family: 'Inter', weight: 'bold' },
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                let value = context.parsed.y;
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f2f6',
                            drawBorder: false,
                        },
                        ticks: {
                            font: { family: 'Inter' },
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return (value / 1000000) + ' Jt';
                                } else if (value >= 1000) {
                                    return (value / 1000) + ' Rb';
                                }
                                return value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            font: { family: 'Inter' }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
