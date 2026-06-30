@extends('admin.layouts.app')
@section('title', 'Promosi & Diskon')
@section('content')
<style>
    .promo-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .promo-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .promo-subtitle {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin-top: 5px;
    }
    .table-container {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    .promo-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .promo-table th {
        background: #f8f9fa;
        padding: 16px 18px;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--border-color);
    }
    .promo-table td {
        padding: 16px 18px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
        font-size: 0.95rem;
    }
    .promo-table tr:hover { background: #fafafa; }
    
    .promo-code {
        font-family: 'Courier New', monospace;
        font-weight: 800;
        font-size: 1.05rem;
        color: var(--primary);
        background: #fef0f0;
        padding: 4px 12px;
        border-radius: 6px;
        letter-spacing: 1px;
    }
    .discount-badge {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.85rem;
    }
    .status-active {
        background: #e6f4ea; color: var(--green);
        padding: 5px 12px; border-radius: 20px;
        font-weight: 700; font-size: 0.8rem;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .status-inactive {
        background: #f1f2f6; color: var(--text-muted);
        padding: 5px 12px; border-radius: 20px;
        font-weight: 700; font-size: 0.8rem;
    }
    .status-expired {
        background: #f8d7da; color: #721c24;
        padding: 5px 12px; border-radius: 20px;
        font-weight: 700; font-size: 0.8rem;
    }
    .usage-bar {
        width: 80px; height: 6px; background: #e5e7eb;
        border-radius: 3px; overflow: hidden; display: inline-block;
        vertical-align: middle; margin-left: 8px;
    }
    .usage-fill {
        height: 100%; background: var(--primary); border-radius: 3px;
    }
    .action-btn {
        padding: 8px 12px; border-radius: 8px; font-size: 0.85rem;
        font-weight: 600; text-decoration: none; display: inline-flex;
        align-items: center; gap: 6px; transition: 0.2s; border: none;
        cursor: pointer; font-family: inherit;
    }
    .btn-edit { background: #f1f2f6; color: var(--text-main); }
    .btn-edit:hover { background: #e5e7eb; }
    .btn-delete { background: white; color: var(--primary); border: 1px solid var(--border-color); }
    .btn-delete:hover { background: #fff0f0; border-color: var(--primary); }
</style>

<div class="promo-header">
    <div>
        <div class="promo-title">
            <i class="fa-solid fa-ticket" style="color: var(--primary);"></i>
            Promosi & Diskon
        </div>
        <div class="promo-subtitle">Kelola kode kupon dan promo untuk pelanggan restoran.</div>
    </div>
    <a href="{{ route('admin.promotions.create') }}" class="btn-primary" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-plus"></i> Buat Promo Baru
    </a>
</div>

<div class="table-container">
    <table class="promo-table">
        <thead>
            <tr>
                <th>Kode Promo</th>
                <th>Diskon</th>
                <th>Min. Order</th>
                <th>Pemakaian</th>
                <th>Periode</th>
                <th>Status</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($promotions as $promo)
            <tr>
                <td>
                    <span class="promo-code">{{ $promo->code }}</span>
                    @if($promo->description)
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">{{ Str::limit($promo->description, 40) }}</div>
                    @endif
                </td>
                <td>
                    <span class="discount-badge">
                        @if($promo->discount_type === 'percentage')
                            {{ $promo->discount_value }}%
                            @if($promo->max_discount)
                                <span style="font-weight: 400; font-size: 0.75rem;">(max Rp {{ number_format($promo->max_discount, 0, ',', '.') }})</span>
                            @endif
                        @else
                            Rp {{ number_format($promo->discount_value, 0, ',', '.') }}
                        @endif
                    </span>
                </td>
                <td>
                    @if($promo->min_order_amount > 0)
                        <span style="font-weight: 600;">Rp {{ number_format($promo->min_order_amount, 0, ',', '.') }}</span>
                    @else
                        <span style="color: var(--text-muted);">-</span>
                    @endif
                </td>
                <td>
                    <span style="font-weight: 700;">{{ $promo->used_count }}</span>
                    <span style="color: var(--text-muted);">/ {{ $promo->usage_limit ?? '∞' }}</span>
                    @if($promo->usage_limit)
                        <div class="usage-bar">
                            <div class="usage-fill" style="width: {{ min(100, ($promo->used_count / $promo->usage_limit) * 100) }}%"></div>
                        </div>
                    @endif
                </td>
                <td style="font-size: 0.85rem; color: var(--text-muted);">
                    @if($promo->starts_at || $promo->expires_at)
                        {{ $promo->starts_at ? $promo->starts_at->format('d M Y') : 'Kapan saja' }}
                        <br>→ {{ $promo->expires_at ? $promo->expires_at->format('d M Y') : 'Tanpa batas' }}
                    @else
                        <span style="color: var(--text-muted);">Tanpa batas waktu</span>
                    @endif
                </td>
                <td>
                    @if(!$promo->is_active)
                        <span class="status-inactive">Nonaktif</span>
                    @elseif($promo->expires_at && $promo->expires_at->isPast())
                        <span class="status-expired">Expired</span>
                    @elseif($promo->usage_limit && $promo->used_count >= $promo->usage_limit)
                        <span class="status-expired">Habis</span>
                    @else
                        <span class="status-active"><i class="fa-solid fa-circle" style="font-size: 6px;"></i> Aktif</span>
                    @endif
                </td>
                <td style="text-align: right;">
                    <a href="{{ route('admin.promotions.edit', $promo->id) }}" class="action-btn btn-edit" style="margin-right: 5px;">
                        <i class="fa-solid fa-pen"></i> Edit
                    </a>
                    <form action="{{ route('admin.promotions.destroy', $promo->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus promo ini?');">
                        @csrf @method('DELETE')
                        <button class="action-btn btn-delete"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
            
            @if($promotions->count() == 0)
            <tr>
                <td colspan="7" style="text-align: center; padding: 50px; color: var(--text-muted);">
                    <i class="fa-solid fa-ticket" style="font-size: 3rem; margin-bottom: 15px; color: #dfe4ea;"></i>
                    <p style="font-weight: 600; font-size: 1.1rem; color: var(--text-main);">Belum ada promosi</p>
                    <p>Buat promo pertama untuk menarik pelanggan!</p>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection
