@extends('admin.layouts.app')
@section('title', 'Moderasi Ulasan')
@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Moderasi Ulasan Pelanggan</h2>
    <div>
        <form method="GET" action="{{ route('admin.reviews.index') }}" style="display: inline-block;">
            <select name="status" onchange="this.form.submit()" style="padding: 8px; border-radius: 6px; border: 1px solid #ddd;">
                <option value="">Semua Status</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Tampil (Approved)</option>
                <option value="hidden" {{ request('status') === 'hidden' ? 'selected' : '' }}>Disembunyikan (Hidden)</option>
            </select>
        </form>
    </div>
</div>


<div style="background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #eee; text-align: left;">
                <th style="padding: 15px;">Pelanggan</th>
                <th style="padding: 15px;">Menu</th>
                <th style="padding: 15px;">Rating & Komentar</th>
                <th style="padding: 15px;">Status</th>
                <th style="padding: 15px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviews as $review)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px;">
                        <strong>{{ $review->user->name ?? 'Guest' }}</strong><br>
                        <span style="color: #666; font-size: 0.85rem;">ORD-{{ $review->order_id }}</span><br>
                        <span style="color: #999; font-size: 0.8rem;">{{ $review->created_at->format('d M Y') }}</span>
                    </td>
                    <td style="padding: 15px;">
                        {{ $review->menu->name ?? 'Menu Terhapus' }}
                    </td>
                    <td style="padding: 15px;">
                        <div style="color: #f1c40f; margin-bottom: 5px;">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <i class="fa-solid fa-star"></i>
                                @else
                                    <i class="fa-regular fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <p style="margin: 0; font-size: 0.9rem; color: #333;">{{ $review->comment ?: '-' }}</p>
                    </td>
                    <td style="padding: 15px;">
                        @if($review->is_approved)
                            <span style="background: #e6f4ea; color: #1e8e3e; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">Tampil</span>
                        @else
                            <span style="background: #fce8e6; color: #d93025; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">Disembunyikan</span>
                        @endif
                    </td>
                    <td style="padding: 15px;">
                        <form action="{{ route('admin.reviews.toggle', $review->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            @if($review->is_approved)
                                <button type="submit" style="background: #fff; color: #d93025; border: 1px solid #d93025; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.85rem;">Sembunyikan</button>
                            @else
                                <button type="submit" style="background: #1e8e3e; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.85rem;">Tampilkan</button>
                            @endif
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding: 30px; text-align: center; color: #666;">Belum ada ulasan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="padding: 15px; border-top: 1px solid #eee;">
        {{ $reviews->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection
