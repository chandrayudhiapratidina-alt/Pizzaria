@extends('client.layouts.app')
@section('title', 'Beri Ulasan - ' . $storeName)
@section('content')
<style>
    .review-container {
        max-width: 800px;
        margin: 0 auto;
        background: var(--card-bg);
        border: 1px solid #333;
        border-radius: 16px;
        padding: 30px;
    }
    .review-header {
        border-bottom: 1px solid #333;
        padding-bottom: 15px;
        margin-bottom: 25px;
    }
    .review-header h2 {
        font-family: 'Playfair Display', serif;
        color: #fff;
        margin: 0 0 5px 0;
    }
    .review-header p {
        color: var(--gray);
        margin: 0;
    }
    .item-card {
        background: var(--bg-dark);
        border: 1px solid #333;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        display: flex;
        gap: 20px;
    }
    .item-img {
        width: 100px;
        height: 100px;
        border-radius: 8px;
        object-fit: cover;
    }
    .item-content {
        flex: 1;
    }
    .item-content h4 {
        color: #fff;
        margin: 0 0 5px 0;
        font-size: 1.2rem;
    }
    .rating-group {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        gap: 5px;
        margin-bottom: 15px;
    }
    .rating-group input {
        display: none;
    }
    .rating-group label {
        color: #555;
        font-size: 2rem;
        cursor: pointer;
        transition: color 0.2s;
    }
    .rating-group label:hover,
    .rating-group label:hover ~ label,
    .rating-group input:checked ~ label {
        color: #f1c40f;
    }
    .form-group textarea {
        width: 100%;
        background: var(--card-bg);
        border: 1px solid #333;
        border-radius: 8px;
        padding: 15px;
        color: #fff;
        font-family: inherit;
        resize: vertical;
        min-height: 80px;
    }
    .form-group textarea:focus {
        border-color: var(--primary);
        outline: none;
    }
    .reviewed-badge {
        background: rgba(46, 204, 113, 0.2);
        color: #2ecc71;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: bold;
        display: inline-block;
        margin-top: 10px;
    }
    
    /* Mobile Responsiveness */
    @media (max-width: 600px) {
        .item-card {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .rating-group {
            justify-content: center;
        }
        .review-container {
            padding: 15px;
        }
        .item-img {
            width: 120px;
            height: 120px;
        }
        .btn {
            width: 100%;
            display: block;
            margin-bottom: 10px;
            text-align: center;
        }
        .btn-primary {
            margin-bottom: 0;
        }
    }
</style>

<div class="review-container">
    <div class="review-header">
        <h2>Beri Ulasan</h2>
        <p>Pesanan #ORD-{{ $order->id }} &bull; {{ $order->created_at->format('d M Y') }}</p>
    </div>

    <form action="{{ route('client.online.orders.reviews.store', $order->id) }}" method="POST">
        @csrf
        @foreach($items as $index => $item)
            <div class="item-card">
                @if($item->menu->image_url)
                    <img src="{{ asset('storage/' . $item->menu->image_url) }}" alt="{{ $item->menu->name }}" class="item-img">
                @else
                    <div class="item-img" style="background:#333; display:flex; align-items:center; justify-content:center; color:#666;">
                        <i class="fa-solid fa-pizza-slice fa-2x"></i>
                    </div>
                @endif
                <div class="item-content">
                    <h4>{{ $item->menu->name }}</h4>
                    
                    @if(in_array($item->menu_id, $reviewedMenuIds))
                        <div class="reviewed-badge"><i class="fa-solid fa-check"></i> Sudah Diulas</div>
                    @else
                        <input type="hidden" name="reviews[{{ $index }}][menu_id]" value="{{ $item->menu_id }}">
                        
                        <div style="margin-top: 10px;">
                            <label style="color: #ccc; font-size: 0.9rem; margin-bottom: 5px; display: block;">Rating Bintang</label>
                            <div class="rating-group">
                                <input type="radio" id="star5_{{ $index }}" name="reviews[{{ $index }}][rating]" value="5" required>
                                <label for="star5_{{ $index }}"><i class="fa-solid fa-star"></i></label>
                                
                                <input type="radio" id="star4_{{ $index }}" name="reviews[{{ $index }}][rating]" value="4">
                                <label for="star4_{{ $index }}"><i class="fa-solid fa-star"></i></label>
                                
                                <input type="radio" id="star3_{{ $index }}" name="reviews[{{ $index }}][rating]" value="3">
                                <label for="star3_{{ $index }}"><i class="fa-solid fa-star"></i></label>
                                
                                <input type="radio" id="star2_{{ $index }}" name="reviews[{{ $index }}][rating]" value="2">
                                <label for="star2_{{ $index }}"><i class="fa-solid fa-star"></i></label>
                                
                                <input type="radio" id="star1_{{ $index }}" name="reviews[{{ $index }}][rating]" value="1">
                                <label for="star1_{{ $index }}"><i class="fa-solid fa-star"></i></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <textarea name="reviews[{{ $index }}][comment]" placeholder="Tulis komentar atau pengalaman Anda menikmati menu ini... (Opsional)"></textarea>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <div style="text-align: right; margin-top: 30px;">
            <a href="{{ route('client.online.profile') }}" class="btn" style="background: transparent; color: #ccc; border: 1px solid #333; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin-right: 10px;">Batal</a>
            <button type="submit" class="btn btn-primary" style="padding: 12px 24px; border-radius: 8px;">Kirim Ulasan</button>
        </div>
    </form>
</div>
@endsection
