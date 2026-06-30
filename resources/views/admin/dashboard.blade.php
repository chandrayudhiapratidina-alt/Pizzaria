@extends('admin.layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        align-items: start;
    }
    .column-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
    }
    .column-title {
        font-size: 1.4rem;
        font-weight: 800;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .badge-red { background: var(--primary); color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; }
    .badge-yellow { background: var(--yellow); color: #000; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; }
    .badge-paid { background: #e6f4ea; color: var(--green); padding: 4px 10px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; }
    .column-subtitle { color: var(--text-muted); font-size: 0.9rem; }
    
    .order-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        padding: 20px;
        margin-bottom: 20px;
    }
    .order-card-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }
    .order-number { color: var(--text-muted); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; margin-bottom: 5px; }
    .table-number { font-size: 1.5rem; font-weight: 800; color: var(--primary); }
    
    .order-details {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }
    .order-img { width: 70px; height: 70px; border-radius: 8px; object-fit: cover; background: var(--bg-color); }
    .order-text { flex: 1; }
    .order-items { font-weight: 600; font-size: 0.95rem; margin-bottom: 5px; }
    .order-instructions { font-size: 0.85rem; color: var(--text-muted); }
    
    .btn-green { background: var(--green); color: white; width: 100%; border: none; padding: 12px; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .btn-green:hover { background: #14592a; }
    
    .payment-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        border-left: 6px solid var(--yellow);
        padding: 20px;
        margin-bottom: 20px;
    }
    .payment-header {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 15px;
        margin-bottom: 15px;
    }
    .payment-table { font-size: 2rem; font-weight: 800; line-height: 1; }
    .payment-total-label { color: var(--text-muted); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; text-align: right; }
    .payment-total { font-size: 1.5rem; font-weight: 800; color: var(--primary); }
    
    .payment-items { list-style: none; padding: 0; margin: 0 0 15px 0; font-size: 0.9rem; }
    .payment-item { display: flex; justify-content: space-between; margin-bottom: 8px; color: var(--text-muted); }
    .payment-tax { display: flex; justify-content: space-between; font-weight: 700; font-size: 0.9rem; border-top: 1px solid var(--border-color); padding-top: 10px; }
    
    .btn-red { background: var(--primary); color: white; width: 100%; border: none; padding: 12px; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .btn-red:hover { background: var(--primary-hover); }
    
    /* Stats Section */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid var(--border-color);
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }
    .stat-title {
        color: var(--text-muted);
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .stat-value {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 15px;
    }
    .progress-container {
        background: #f1f2f6;
        height: 8px;
        border-radius: 4px;
        margin-bottom: 8px;
        overflow: hidden;
    }
    .progress-bar {
        height: 100%;
        background: var(--primary);
        border-radius: 4px;
    }
    .stat-target {
        font-size: 0.8rem;
        color: var(--text-muted);
        display: flex;
        justify-content: space-between;
    }
    .target-reached { color: var(--green); font-weight: 600; }
</style>

<div class="stats-grid">
    <!-- Daily Stats -->
    <div class="stat-card">
        <div class="stat-title">
            Pendapatan Harian
            <i class="fa-solid fa-calendar-day" style="color: var(--primary);"></i>
        </div>
        <div class="stat-value">Rp {{ number_format($daily_sales, 0, ',', '.') }}</div>
        @php $daily_pct = min(100, ($daily_sales / $daily_target) * 100); @endphp
        <div class="progress-container">
            <div class="progress-bar" style="width: {{ $daily_pct }}%; {{ $daily_pct == 100 ? 'background: var(--green);' : '' }}"></div>
        </div>
        <div class="stat-target">
            <span>Target: Rp {{ number_format($daily_target, 0, ',', '.') }}</span>
            <span class="{{ $daily_pct == 100 ? 'target-reached' : '' }}">{{ number_format($daily_pct, 1) }}%</span>
        </div>
    </div>
    
    <!-- Weekly Stats -->
    <div class="stat-card">
        <div class="stat-title">
            Pendapatan Mingguan
            <i class="fa-solid fa-calendar-week" style="color: var(--primary);"></i>
        </div>
        <div class="stat-value">Rp {{ number_format($weekly_sales, 0, ',', '.') }}</div>
        @php $weekly_pct = min(100, ($weekly_sales / $weekly_target) * 100); @endphp
        <div class="progress-container">
            <div class="progress-bar" style="width: {{ $weekly_pct }}%; {{ $weekly_pct == 100 ? 'background: var(--green);' : '' }}"></div>
        </div>
        <div class="stat-target">
            <span>Target: Rp {{ number_format($weekly_target, 0, ',', '.') }}</span>
            <span class="{{ $weekly_pct == 100 ? 'target-reached' : '' }}">{{ number_format($weekly_pct, 1) }}%</span>
        </div>
    </div>

    <!-- Monthly Stats -->
    <div class="stat-card">
        <div class="stat-title">
            Pendapatan Bulanan
            <i class="fa-solid fa-calendar-days" style="color: var(--primary);"></i>
        </div>
        <div class="stat-value">Rp {{ number_format($monthly_sales, 0, ',', '.') }}</div>
        @php $monthly_pct = min(100, ($monthly_sales / $monthly_target) * 100); @endphp
        <div class="progress-container">
            <div class="progress-bar" style="width: {{ $monthly_pct }}%; {{ $monthly_pct == 100 ? 'background: var(--green);' : '' }}"></div>
        </div>
        <div class="stat-target">
            <span>Target: Rp {{ number_format($monthly_target, 0, ',', '.') }}</span>
            <span class="{{ $monthly_pct == 100 ? 'target-reached' : '' }}">{{ number_format($monthly_pct, 1) }}%</span>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Left Column: Incoming Orders -->
    <div>
        <div class="column-header">
            <div>
                <div class="column-title">
                    Incoming Digital Orders 
                    @if($incoming_orders->count() > 0)
                        <span class="badge-red">{{ $incoming_orders->count() }} New</span>
                    @endif
                </div>
                <div class="column-subtitle">Paid online. Awaiting service.</div>
            </div>
            <i class="fa-solid fa-filter" style="color: var(--text-muted); cursor: pointer;"></i>
        </div>
        
        @forelse($incoming_orders as $order)
        <div class="order-card">
            <div class="order-card-header">
                <div>
                    <div class="order-number">ORDER #{{ $order->id }}</div>
                    <div class="table-number">Table {{ str_pad($order->table_id ?? 0, 2, '0', STR_PAD_LEFT) }}</div>
                </div>
                <div class="badge-paid">PAID</div>
            </div>
            <div class="order-details">
                @php $firstItem = $order->items->first(); @endphp
                @if($firstItem && $firstItem->menu && $firstItem->menu->image_url)
                    <img src="{{ asset('storage/' . $firstItem->menu->image_url) }}" alt="Pizza" class="order-img">
                @else
                    <div class="order-img" style="display:flex; align-items:center; justify-content:center; color:#ccc;"><i class="fa-solid fa-pizza-slice"></i></div>
                @endif
                <div class="order-text">
                    <div class="order-items">
                        {{ $order->items->map(function($i) { return $i->qty . 'x ' . ($i->menu->name ?? 'Item'); })->implode(', ') }}
                    </div>
                    <div class="order-instructions">
                        @php
                            $notes = [];
                            foreach($order->items as $item) {
                                if($item->customization_notes) {
                                    $arr = is_string($item->customization_notes) ? json_decode($item->customization_notes, true) : $item->customization_notes;
                                    if(is_array($arr)) {
                                        foreach($arr as $k => $v) {
                                            $val = is_array($v) ? implode(', ', $v) : $v;
                                            $notes[] = "$k: $val";
                                        }
                                    }
                                }
                            }
                        @endphp
                        @if(count($notes) > 0)
                            Special Instructions: {{ implode(', ', $notes) }}
                        @endif
                    </div>
                </div>
            </div>
            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                @csrf
                <input type="hidden" name="order_status" value="served">
                <button type="submit" class="btn-green">
                    <i class="fa-regular fa-circle-check"></i> Mark as Served
                </button>
            </form>
        </div>
        @empty
        <div style="text-align:center; padding: 40px; color: var(--text-muted);">No incoming orders.</div>
        @endforelse
    </div>
    
    <!-- Right Column: Payment Queue -->
    <div>
        <div class="column-header">
            <div>
                <div class="column-title">
                    Cashier Payment Queue 
                    @if($payment_queue->count() > 0)
                        <span class="badge-yellow">{{ $payment_queue->count() }} Waiting</span>
                    @endif
                </div>
                <div class="column-subtitle">Customers requested check at table.</div>
            </div>
            <i class="fa-solid fa-money-bill-wave" style="color: var(--text-muted);"></i>
        </div>
        
        @php $tax_rate = \App\Models\Setting::get('tax_rate', 11); @endphp
        @forelse($payment_queue as $order)
        <div class="payment-card">
            <div class="payment-header">
                <div>
                    <div class="order-number">TABLE</div>
                    <div class="payment-table">{{ str_pad($order->table_id ?? 0, 2, '0', STR_PAD_LEFT) }}</div>
                </div>
                <div>
                    <div class="payment-total-label">TOTAL AMOUNT</div>
                    <div class="payment-total">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                </div>
            </div>
            <ul class="payment-items">
                @foreach($order->items as $item)
                <li class="payment-item">
                    <span>{{ $item->menu->name ?? 'Item' }} x{{ $item->qty }}</span>
                    <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </li>
                @endforeach
            </ul>
            <div class="payment-tax">
                <span>Tax ({{ $tax_rate }}%)</span>
                <span>Rp {{ number_format($order->total_amount * ($tax_rate / 100), 0, ',', '.') }}</span>
            </div>
            <div style="margin-top: 20px;">
                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_status" value="completed">
                    <button type="submit" class="btn-red">
                        <i class="fa-solid fa-print"></i> Verify & Print Receipt
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div style="text-align:center; padding: 40px; color: var(--text-muted);">No payments in queue.</div>
        @endforelse
    </div>
</div>
@endsection
