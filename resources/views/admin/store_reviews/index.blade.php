@extends('admin.layouts.app')

@section('title', 'Manajemen Ulasan Restoran')

@section('content')
<style>
    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .review-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .review-table-container {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        overflow: hidden;
    }
    .review-table {
        width: 100%;
        border-collapse: collapse;
    }
    .review-table th {
        background: #f8fafc;
        padding: 15px 20px;
        text-align: left;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--border-color);
    }
    .review-table td {
        padding: 15px 20px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
        color: var(--text-main);
    }
    .review-table tr:hover {
        background: #fafafa;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    .badge-primary { background: #e0e7ff; color: #4338ca; }
    .badge-secondary { background: #f1f2f6; color: var(--text-muted); }
    .badge-success { background: #d1fae5; color: #065f46; }
    .badge-warning { background: #fef3c7; color: #b45309; }
    .badge-info { background: #e0f2fe; color: #0369a1; }

    .star-rating {
        color: var(--yellow);
        font-size: 0.9rem;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        color: white;
        text-decoration: none;
    }
    .btn-action:hover {
        transform: translateY(-2px);
    }
    .btn-toggle-show { background: #10b981; }
    .btn-toggle-show:hover { background: #059669; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3); }
    .btn-toggle-hide { background: #f59e0b; }
    .btn-toggle-hide:hover { background: #d97706; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3); }
    .btn-edit { background: #3b82f6; }
    .btn-edit:hover { background: #2563eb; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3); }
    .btn-delete { background: #ef4444; }
    .btn-delete:hover { background: #dc2626; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3); }
</style>

<div class="review-header">
    <div class="review-title">
        <i class="fa-solid fa-comments text-primary"></i> Ulasan Restoran
    </div>
    <a href="{{ route('admin.store_reviews.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Ulasan Baru
    </a>
</div>


<div class="review-table-container">
    <table class="review-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Pengguna</th>
                <th>Rating</th>
                <th>Tipe</th>
                <th>Komentar</th>
                <th>Status</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviews as $review)
            <tr>
                <td><span style="font-size: 0.9rem; color: var(--text-muted);">{{ $review->created_at->format('d M Y H:i') }}</span></td>
                <td>
                    @if($review->user)
                        <strong style="display: block; margin-bottom: 2px;">{{ $review->user->name }}</strong>
                        <span class="badge badge-primary">Member</span>
                    @else
                        <strong style="display: block; margin-bottom: 2px;">{{ $review->guest_name }}</strong>
                        <span class="badge badge-secondary">Guest</span>
                    @endif
                </td>
                <td>
                    <div class="star-rating">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                <i class="fa-solid fa-star"></i>
                            @else
                                <i class="fa-regular fa-star"></i>
                            @endif
                        @endfor
                    </div>
                </td>
                <td>
                    @if($review->review_type == 'service')
                        <span class="badge badge-info">Pelayanan</span>
                    @elseif($review->review_type == 'ambiance')
                        <span class="badge badge-success">Suasana</span>
                    @else
                        <span class="badge badge-secondary">Umum</span>
                    @endif
                </td>
                <td style="max-width: 250px; font-style: italic;">"{{ Str::limit($review->comment, 80) ?? '-' }}"</td>
                <td>
                    @if($review->is_approved)
                        <span class="badge badge-success">Tampil</span>
                    @else
                        <span class="badge badge-warning">Sembunyi</span>
                    @endif
                </td>
                <td>
                    <div class="action-buttons" style="justify-content: center;">
                        <form action="{{ route('admin.store_reviews.toggle', $review->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-action {{ $review->is_approved ? 'btn-toggle-hide' : 'btn-toggle-show' }}" title="{{ $review->is_approved ? 'Sembunyikan' : 'Tampilkan' }}">
                                <i class="fa-solid {{ $review->is_approved ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                            </button>
                        </form>
                        <a href="{{ route('admin.store_reviews.edit', $review->id) }}" class="btn-action btn-edit" title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        
                        <form action="{{ route('admin.store_reviews.destroy', $review->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ulasan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action btn-delete" title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                    <i class="fa-regular fa-comments" style="font-size: 3rem; margin-bottom: 10px; opacity: 0.5;"></i><br>
                    Belum ada ulasan restoran.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
