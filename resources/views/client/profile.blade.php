@extends('client.layouts.app')
@section('title', 'Akun Saya - ' . $storeName)
@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    .profile-container {
        max-width: 1000px;
        margin: 0 auto;
        display: flex;
        gap: 30px;
    }
    @media (max-width: 768px) {
        .profile-container {
            flex-direction: column;
        }
    }
    .profile-sidebar {
        flex: 1;
        min-width: 250px;
        background: var(--card-bg);
        border: 1px solid #333;
        border-radius: 16px;
        padding: 20px;
        height: max-content;
    }
    .profile-sidebar h3 {
        color: #fff;
        margin-top: 0;
        margin-bottom: 20px;
        font-family: 'Playfair Display', serif;
        font-size: 1.5rem;
    }
    .tab-btn {
        display: block;
        width: 100%;
        text-align: left;
        background: transparent;
        border: none;
        color: var(--gray);
        padding: 12px 15px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        border-radius: 8px;
        transition: 0.3s;
        margin-bottom: 5px;
    }
    .tab-btn:hover {
        background: rgba(255,255,255,0.05);
        color: #fff;
    }
    .tab-btn.active {
        background: rgba(232, 48, 74, 0.1);
        color: var(--primary);
        border-left: 4px solid var(--primary);
    }
    .profile-content {
        flex: 3;
        background: var(--card-bg);
        border: 1px solid #333;
        border-radius: 16px;
        padding: 30px;
    }
    .tab-pane {
        display: none;
        animation: fadeIn 0.4s;
    }
    .tab-pane.active {
        display: block;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .section-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.8rem;
        color: #fff;
        margin-top: 0;
        margin-bottom: 25px;
        border-bottom: 1px solid #333;
        padding-bottom: 15px;
    }

    /* Forms */
    .form-group { margin-bottom: 20px; }
    .form-group label {
        display: block;
        color: #ccc;
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }
    .form-group input, .form-group textarea {
        width: 100%;
        padding: 12px;
        background: var(--bg-dark);
        border: 1px solid #333;
        border-radius: 8px;
        color: #fff;
        font-family: inherit;
    }
    .form-group input:focus, .form-group textarea:focus {
        border-color: var(--primary);
        outline: none;
    }

    /* Address Cards */
    .address-card {
        background: var(--bg-dark);
        border: 1px solid #333;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .address-info h4 { margin: 0 0 5px; color: #fff; }
    .address-info p { margin: 0; color: var(--gray); font-size: 0.9rem; line-height: 1.5; }
    .btn-delete {
        background: rgba(232, 48, 74, 0.1);
        color: #ff4757;
        border: 1px solid rgba(232, 48, 74, 0.3);
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
    }
    .btn-delete:hover { background: #ff4757; color: #fff; }

    /* Orders Table */
    .orders-table {
        width: 100%;
        border-collapse: collapse;
        color: #ccc;
    }
    .orders-table th, .orders-table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #333;
    }
    .orders-table th {
        color: #fff;
        background: rgba(255,255,255,0.05);
        font-weight: 700;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
        text-transform: capitalize;
    }
    .status-new { background: rgba(52, 152, 219, 0.2); color: #3498db; }
    .status-cooking { background: rgba(241, 196, 15, 0.2); color: #f1c40f; }
    .status-ready { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }
    .status-completed { background: rgba(149, 165, 166, 0.2); color: #95a5a6; }
    .status-cancelled { background: rgba(231, 76, 60, 0.2); color: #e74c3c; }

    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 600;
    }
    .alert-success { background: rgba(46, 204, 113, 0.1); color: #2ecc71; border: 1px solid rgba(46, 204, 113, 0.3); }
    .alert-danger { background: rgba(231, 76, 60, 0.1); color: #e74c3c; border: 1px solid rgba(231, 76, 60, 0.3); }
    
    .action-cell {
        white-space: nowrap;
        display: flex;
        gap: 8px;
        align-items: center;
    }
    
    @media (max-width: 600px) {
        .profile-content {
            padding: 15px;
        }
        .orders-table th, .orders-table td {
            padding: 10px;
            font-size: 0.9rem;
        }
        .action-cell {
            flex-direction: column;
            gap: 5px;
            align-items: flex-start;
        }
        .action-cell .btn {
            margin-left: 0 !important;
            width: 100%;
            text-align: center;
            box-sizing: border-box;
        }
    }
    select option {
        background: #1a1a1a;
        color: #fff;
    }
</style>

<div class="profile-container">
    <div class="profile-sidebar">
        <div style="text-align: center; margin-bottom: 20px;">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Foto Profil" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary); margin-bottom: 10px;">
            @else
                <div style="width: 100px; height: 100px; border-radius: 50%; background: #333; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: bold; margin: 0 auto 10px; border: 2px solid #555;">
                    {{ substr($user->name, 0, 1) }}
                </div>
            @endif
            <h3 style="margin-bottom: 5px;">{{ $user->name }}</h3>
            <p style="color: var(--gray); font-size: 0.85rem; margin: 0;">Akun Pelanggan</p>
        </div>
        
        <button class="tab-btn active" onclick="openTab(event, 'profil')">Profil Saya</button>
        <button class="tab-btn" onclick="openTab(event, 'alamat')">Buku Alamat</button>
        <button class="tab-btn" onclick="openTab(event, 'pesanan')">Riwayat Pesanan</button>
        <button class="tab-btn" onclick="openTab(event, 'ulasan')">Ulasan Saya</button>
        
        <form action="{{ route('logout') }}" method="POST" style="margin-top: 30px;">
            @csrf
            <button type="submit" class="tab-btn" style="color: #ff4757;"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button>
        </form>
    </div>

    <div class="profile-content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <!-- Tab Profil -->
        <div id="profil" class="tab-pane active">
            <h2 class="section-title">Profil Saya</h2>
            <form action="{{ route('client.online.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group" style="text-align: center;">
                    <label style="text-align: left;">Foto Profil</label>
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" id="avatarPreview" alt="Foto Profil" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 1px solid #555;">
                        @else
                            <div id="avatarPreviewFallback" style="width: 80px; height: 80px; border-radius: 50%; background: #222; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 2rem; border: 1px solid #555;">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <img src="" id="avatarPreview" style="display: none; width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 1px solid #555;">
                        @endif
                        <input type="file" name="avatar" accept="image/png, image/jpeg, image/jpg" onchange="previewImage(this)" style="background: transparent; border: 1px dashed #555; padding: 10px; cursor: pointer;">
                    </div>
                </div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" value="{{ $user->name }}" required>
                </div>
                <div class="form-group">
                    <label>Email (Tidak dapat diubah)</label>
                    <input type="email" value="{{ $user->email }}" readonly style="opacity: 0.6; cursor: not-allowed;">
                </div>
                <div class="form-group">
                    <label>No. WhatsApp</label>
                    <input type="text" name="phone_number" value="{{ $user->phone_number }}">
                </div>
                <hr style="border-color: #333; margin: 30px 0;">
                <h4 style="color:#fff; margin-bottom:15px;">Ubah Kata Sandi (Kosongkan jika tidak ingin mengubah)</h4>
                <div class="form-group">
                    <label>Kata Sandi Baru</label>
                    <input type="password" name="password" placeholder="Minimal 8 karakter">
                </div>
                <div class="form-group">
                    <label>Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation">
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 12px 24px; border-radius: 8px;">Simpan Perubahan</button>
            </form>
        </div>

        <!-- Tab Alamat -->
        <div id="alamat" class="tab-pane">
            <h2 class="section-title">Buku Alamat</h2>
            
            @if($addresses->count() > 0)
                @foreach($addresses as $addr)
                    <div class="address-card">
                        <div class="address-info">
                            <h4>{{ $addr->label }}</h4>
                            <p>{{ $addr->full_address }}</p>
                        </div>
                        <form action="{{ route('client.online.profile.address.destroy', $addr->id) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                @endforeach
            @else
                <p style="color: var(--gray); font-style: italic;">Belum ada alamat tersimpan.</p>
            @endif

            <hr style="border-color: #333; margin: 30px 0;">
            <h4 style="color:#fff; margin-bottom:15px;">Tambah Alamat Baru</h4>
            <form action="{{ route('client.online.profile.address.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Label Alamat (Contoh: Rumah, Kantor)</label>
                    <input type="text" name="label" required placeholder="Rumah">
                </div>
                
                <div class="form-group">
                    <label>Tandai Lokasi di Peta</label>
                    <div id="map" style="height: 250px; border-radius: 8px; margin-bottom: 10px; border: 1px solid #333; z-index: 1;"></div>
                    <button type="button" onclick="getCurrentLocation()" class="btn btn-secondary" style="background: rgba(255,255,255,0.1); border: 1px solid #555; font-size: 0.8rem; padding: 6px 12px; margin-bottom: 15px; border-radius: 6px; cursor: pointer; color:#fff;">
                        <i class="fa-solid fa-location-crosshairs"></i> Deteksi Lokasi Saya
                    </button>
                    <p style="color: #ffb74d; font-size: 0.8rem; margin: 0 0 10px 0;"><i class="fa-solid fa-circle-info"></i> Geser pin atau klik pada peta untuk menentukan titik koordinat yang tepat.</p>
                </div>
                <input type="hidden" name="latitude" id="latitude" required>
                <input type="hidden" name="longitude" id="longitude" required>

                <div class="form-group">
                    <label>Detail Alamat & Patokan</label>
                    <textarea name="address" id="address_detail" required placeholder="Contoh: Jl. Sudirman No 1. Patokan: Pagar Hitam..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px; border-radius: 8px;">Simpan Alamat</button>
            </form>
        </div>

        <!-- Tab Pesanan -->
        <div id="pesanan" class="tab-pane">
            <h2 class="section-title">Riwayat Pesanan</h2>
            
            @if($orders->count() > 0)
                <div style="overflow-x: auto;">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td style="font-weight:bold; color:#fff;">ORD-{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td style="text-transform: capitalize;">{{ $order->order_type }}</td>
                                <td style="color:var(--primary); font-weight:bold;">Rp {{ number_format($order->total_amount + $order->delivery_fee, 0, ',', '.') }}</td>
                                <td>
                                    <span class="status-badge status-{{ strtolower($order->order_status) }}">{{ $order->order_status }}</span>
                                </td>
                                <td class="action-cell">
                                    @if($order->isOnline())
                                        <a href="{{ route('client.online.orders.show', $order->id) }}" class="btn" style="background:#333; color:#fff; padding:6px 12px; font-size:0.8rem; border-radius:6px; text-decoration:none;">Detail</a>
                                        @if(in_array($order->order_status, ['completed']))
                                            @php
                                                $itemsCount = $order->items->unique('menu_id')->count();
                                                $reviewsCount = $order->reviews->where('user_id', Auth::id())->count();
                                                $hasPendingReviews = $reviewsCount < $itemsCount;
                                            @endphp
                                            
                                            @if($hasPendingReviews)
                                                <a href="{{ route('client.online.orders.reviews.create', $order->id) }}" class="btn" style="background:var(--primary); color:#fff; padding:6px 12px; font-size:0.8rem; border-radius:6px; text-decoration:none; margin-left:5px;">Beri Ulasan</a>
                                            @else
                                                <span class="btn" style="background:rgba(255,255,255,0.1); color:var(--gray); padding:6px 12px; font-size:0.8rem; border-radius:6px; margin-left:5px; cursor:not-allowed;"><i class="fa-solid fa-check"></i> Sudah Diulas</span>
                                            @endif
                                        @endif
                                    @else
                                        <span style="color:var(--gray); font-size:0.8rem;">Dine In</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="color: var(--gray); text-align:center; padding:30px 0;">Belum ada riwayat pesanan. <br><br> <a href="{{ route('client.guest.catalog') }}" class="btn btn-primary" style="padding: 10px 20px; text-decoration: none; border-radius:8px;">Pesan Sekarang</a></p>
            @endif
        </div>

        <!-- Tab Ulasan Saya -->
        <div id="ulasan" class="tab-pane">
            <h2 class="section-title">Ulasan Saya</h2>
            
            <div style="margin-bottom: 20px; text-align:right;">
                <button type="button" class="btn-primary" style="padding: 10px 20px; border-radius: 8px; border:none; cursor:pointer;" onclick="document.getElementById('createReviewModalOverlay').style.display='flex'">
                    <i class="fa-solid fa-pen-nib"></i> Buat Ulasan Baru
                </button>
            </div>

            @if(isset($myStoreReviews) && $myStoreReviews->count() > 0)
                @foreach($myStoreReviews as $review)
                <div class="address-card" style="flex-direction: column; gap: 15px;">
                    <div style="display: flex; justify-content: space-between; width: 100%; border-bottom: 1px solid #333; padding-bottom: 10px;">
                        <div>
                            <span style="color: var(--gold); font-size: 1.1rem;">
                                @for($i=1; $i<=5; $i++)
                                    @if($i <= $review->rating) <i class="fa-solid fa-star"></i> @else <i class="fa-regular fa-star"></i> @endif
                                @endfor
                            </span>
                            <span class="status-badge {{ $review->is_approved ? 'status-ready' : 'status-cancelled' }}" style="margin-left: 10px;">
                                {{ $review->is_approved ? 'Ditampilkan' : 'Menunggu Persetujuan / Disembunyikan' }}
                            </span>
                        </div>
                        <div style="color: var(--gray); font-size: 0.8rem;">
                            {{ $review->created_at->format('d M Y') }}
                        </div>
                    </div>
                    
                    <div>
                        <span style="display:block; font-size:0.8rem; color:var(--gray); margin-bottom:5px;">
                            Kategori: 
                            @if($review->review_type == 'service') Layanan Restoran @elseif($review->review_type == 'ambiance') Suasana & Tempat @else Pengalaman Umum @endif
                        </span>
                        <p style="margin:0; font-style: italic; color: #fff;">"{{ $review->comment ?? 'Tidak ada komentar text.' }}"</p>
                    </div>

                    <div style="display: flex; gap: 10px; width: 100%; justify-content: flex-end;">
                        <button type="button" class="btn btn-secondary" style="background: rgba(255,255,255,0.1); border:none; padding:6px 15px; border-radius:6px; cursor:pointer; color:#fff;" onclick="openEditReviewModal({{ $review->id }}, {{ $review->rating }}, '{{ $review->review_type }}', `{{ htmlspecialchars($review->comment, ENT_QUOTES) }}`)">
                            <i class="fa-solid fa-pen"></i> Edit
                        </button>
                        
                        <form action="{{ route('client.online.profile.store-reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ulasan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete" style="padding: 6px 15px; margin:0;"><i class="fa-solid fa-trash"></i> Hapus</button>
                        </form>
                    </div>
                </div>
                @endforeach
            @else
                <p style="color: var(--gray); text-align:center; padding:30px 0;">Anda belum pernah memberikan ulasan restoran.</p>
            @endif
        </div>

    </div>
</div>

<!-- Modal Buat Review -->
<div id="createReviewModalOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center;">
    <div style="background: var(--card-bg); width: 90%; max-width: 500px; padding: 30px; border-radius: 16px; position: relative;">
        <button type="button" onclick="document.getElementById('createReviewModalOverlay').style.display='none'" style="position: absolute; top: 15px; right: 15px; background: transparent; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">&times;</button>
        <h2 style="font-family: 'Playfair Display', serif; color: #fff; margin-top: 0;">Buat Ulasan Baru</h2>
        
        <form action="{{ route('client.guest.api.store-reviews') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Rating Anda</label>
                <div class="star-rating-edit" style="display: flex; gap: 5px; flex-direction: row-reverse; justify-content: flex-end;">
                    <input type="radio" id="create-star5" name="rating" value="5" style="display:none;" required checked/>
                    <label for="create-star5" title="5 Bintang" style="cursor:pointer; color:var(--gray); font-size:1.5rem;"><i class="fa-solid fa-star"></i></label>
                    <input type="radio" id="create-star4" name="rating" value="4" style="display:none;" />
                    <label for="create-star4" title="4 Bintang" style="cursor:pointer; color:var(--gray); font-size:1.5rem;"><i class="fa-solid fa-star"></i></label>
                    <input type="radio" id="create-star3" name="rating" value="3" style="display:none;" />
                    <label for="create-star3" title="3 Bintang" style="cursor:pointer; color:var(--gray); font-size:1.5rem;"><i class="fa-solid fa-star"></i></label>
                    <input type="radio" id="create-star2" name="rating" value="2" style="display:none;" />
                    <label for="create-star2" title="2 Bintang" style="cursor:pointer; color:var(--gray); font-size:1.5rem;"><i class="fa-solid fa-star"></i></label>
                    <input type="radio" id="create-star1" name="rating" value="1" style="display:none;" />
                    <label for="create-star1" title="1 Bintang" style="cursor:pointer; color:var(--gray); font-size:1.5rem;"><i class="fa-solid fa-star"></i></label>
                </div>
            </div>

            <div class="form-group">
                <label>Fokus Ulasan</label>
                <select name="review_type" style="width: 100%; padding: 12px; background: var(--bg-dark); border: 1px solid #333; border-radius: 8px; color: #fff;" required>
                    <option value="general">Pengalaman Umum</option>
                    <option value="service">Layanan Restoran</option>
                    <option value="ambiance">Suasana & Tempat</option>
                </select>
            </div>

            <div class="form-group">
                <label>Komentar</label>
                <textarea name="comment" rows="4" placeholder="Ceritakan pengalaman Anda..."></textarea>
            </div>
            
            <input type="hidden" name="guest_name" value="{{ Auth::user()->name }}">
            <input type="hidden" name="user_id" value="{{ Auth::id() }}">
            <input type="hidden" name="redirect_back" value="1">

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; border-radius: 8px;">Kirim Ulasan</button>
        </form>
    </div>
</div>

<!-- Modal Edit Review -->
<div id="editReviewModalOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center;">
    <div style="background: var(--card-bg); width: 90%; max-width: 500px; padding: 30px; border-radius: 16px; position: relative;">
        <button type="button" onclick="document.getElementById('editReviewModalOverlay').style.display='none'" style="position: absolute; top: 15px; right: 15px; background: transparent; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">&times;</button>
        <h2 style="font-family: 'Playfair Display', serif; color: #fff; margin-top: 0;">Edit Ulasan Anda</h2>
        
        <form id="editReviewForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label>Rating Anda</label>
                <div class="star-rating-edit" style="display: flex; gap: 5px; flex-direction: row-reverse; justify-content: flex-end;">
                    <input type="radio" id="edit-star5" name="rating" value="5" style="display:none;" required/>
                    <label for="edit-star5" title="5 Bintang" style="cursor:pointer; color:var(--gray); font-size:1.5rem;"><i class="fa-solid fa-star"></i></label>
                    <input type="radio" id="edit-star4" name="rating" value="4" style="display:none;" />
                    <label for="edit-star4" title="4 Bintang" style="cursor:pointer; color:var(--gray); font-size:1.5rem;"><i class="fa-solid fa-star"></i></label>
                    <input type="radio" id="edit-star3" name="rating" value="3" style="display:none;" />
                    <label for="edit-star3" title="3 Bintang" style="cursor:pointer; color:var(--gray); font-size:1.5rem;"><i class="fa-solid fa-star"></i></label>
                    <input type="radio" id="edit-star2" name="rating" value="2" style="display:none;" />
                    <label for="edit-star2" title="2 Bintang" style="cursor:pointer; color:var(--gray); font-size:1.5rem;"><i class="fa-solid fa-star"></i></label>
                    <input type="radio" id="edit-star1" name="rating" value="1" style="display:none;" />
                    <label for="edit-star1" title="1 Bintang" style="cursor:pointer; color:var(--gray); font-size:1.5rem;"><i class="fa-solid fa-star"></i></label>
                </div>
            </div>

            <div class="form-group">
                <label>Fokus Ulasan</label>
                <select name="review_type" id="edit_review_type" style="width: 100%; padding: 12px; background: var(--bg-dark); border: 1px solid #333; border-radius: 8px; color: #fff;" required>
                    <option value="general">Pengalaman Umum</option>
                    <option value="service">Layanan Restoran</option>
                    <option value="ambiance">Suasana & Tempat</option>
                </select>
            </div>

            <div class="form-group">
                <label>Komentar</label>
                <textarea name="comment" id="edit_comment" rows="4" placeholder="Ceritakan pengalaman Anda..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; border-radius: 8px;">Perbarui Ulasan</button>
        </form>
    </div>
</div>

<style>
.star-rating-edit input:checked ~ label {
    color: var(--gold) !important;
}
.star-rating-edit label:hover,
.star-rating-edit label:hover ~ label {
    color: var(--gold) !important;
}
</style>

<script>
function openEditReviewModal(id, rating, type, comment) {
    document.getElementById('editReviewModalOverlay').style.display = 'flex';
    document.getElementById('editReviewForm').action = "/client/online/profile/store-reviews/" + id;
    
    document.getElementById('edit_review_type').value = type;
    document.getElementById('edit_comment').value = comment !== 'null' ? comment : '';
    
    // Check radio button
    var radio = document.getElementById('edit-star' + rating);
    if(radio) {
        radio.checked = true;
    }
}
</script>

<script>
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tab-pane");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].classList.remove("active");
    }
    tablinks = document.getElementsByClassName("tab-btn");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
    }
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");

    if (tabName === 'alamat' && typeof map !== 'undefined') {
        setTimeout(function() { map.invalidateSize(); }, 200);
    }
}

// Map Logic
var map = L.map('map').setView([-6.8797, 109.1256], 13); // Default Tegal
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OSM'
}).addTo(map);

var userIcon = L.divIcon({
    html: '<div style="background:#00cec9;color:#fff;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1rem;box-shadow:0 3px 15px rgba(0,206,209,0.5);border:3px solid #fff;cursor:grab"><i class="fa-solid fa-house"></i></div>',
    className: '',
    iconSize: [36, 36],
    iconAnchor: [18, 18]
});

var marker = L.marker([-6.8797, 109.1256], { draggable: true, icon: userIcon }).addTo(map);

// Update Hidden Fields
function updateFormCoords(lat, lng) {
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
}

function reverseGeocode(lat, lng) {
    var url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data && data.display_name) {
                document.getElementById('address_detail').value = data.display_name;
            }
        })
        .catch(err => console.error("Geocoding failed:", err));
}

marker.on('dragend', function(e) {
    var position = marker.getLatLng();
    updateFormCoords(position.lat, position.lng);
    reverseGeocode(position.lat, position.lng);
});

map.on('click', function(e) {
    marker.setLatLng(e.latlng);
    updateFormCoords(e.latlng.lat, e.latlng.lng);
    reverseGeocode(e.latlng.lat, e.latlng.lng);
});

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], 16);
            updateFormCoords(lat, lng);
            reverseGeocode(lat, lng);
        }, function(err) {
            alert('Gagal mendeteksi lokasi. Pastikan GPS aktif.');
        });
    } else {
        alert('Browser tidak mendukung deteksi lokasi.');
    }
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = document.getElementById('avatarPreview');
            var fallback = document.getElementById('avatarPreviewFallback');
            if (fallback) fallback.style.display = 'none';
            if (img) {
                img.style.display = 'block';
                img.src = e.target.result;
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
