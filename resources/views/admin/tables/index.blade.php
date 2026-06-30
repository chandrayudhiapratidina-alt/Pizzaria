@extends('admin.layouts.app')
@section('title', 'Manajemen Meja & QR Code')
@section('content')
<style>
    :root {
        /* CSS Variables untuk Kustomisasi Cetak QR */
        --print-card-width: 95mm;       /* Lebar kartu fisik (cocok untuk cetak presisi) */
        --print-card-height: auto;      /* Tinggi kartu (auto agar fleksibel/tidak terpotong) */
        --print-card-padding: 30px;     /* Padding dalam kartu */
        --print-qr-size: 4cm;           /* Ukuran QR code di dalam kartu (4x4 cm) */
        --print-border-radius: 24px;    /* Tingkat kelengkungan sudut kartu */
        --print-border-color: #c00a27;  /* Warna border putus-putus khas Pizzaria */
        
        /* Font & Warna */
        --print-font-family: 'Inter', system-ui, -apple-system, sans-serif;
        --print-brand-color: #c00a27;   /* Warna teks utama PIZZARIA */
        --print-text-color: #4b5563;    /* Warna teks instruksi */
        --print-badge-bg: #111827;      /* Warna background badge meja */
        --print-badge-text: #ffffff;    /* Warna teks badge meja */
    }

    /* Tampilan Layar Cetak (Screen & Modal overlay) */
    #printArea {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(5px);
        z-index: 9999;
        display: none; /* Diubah ke 'flex' via JavaScript */
        justify-content: center;
        align-items: center;
    }

    .print-card {
        width: var(--print-card-width);
        height: var(--print-card-height);
        padding: var(--print-card-padding);
        background: #ffffff;
        border: 4px dashed var(--print-border-color);
        border-radius: var(--print-border-radius);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        font-family: var(--print-font-family);
    }

    .print-brand {
        font-size: 2.8rem;
        font-weight: 900;
        color: var(--print-brand-color);
        margin: 0 0 6px 0;
        letter-spacing: 2px;
        text-transform: uppercase;
        font-family: var(--print-font-family);
    }

    .print-subtitle {
        font-size: 1.1rem;
        color: var(--print-text-color);
        margin: 0 0 25px 0;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-family: var(--print-font-family);
    }

    .print-qr-wrapper {
        padding: 12px;
        background: #ffffff;
        border-radius: 16px;
        border: 2px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 25px;
        width: var(--print-qr-size);
        height: var(--print-qr-size);
        box-sizing: content-box; /* Menjamin ukuran konten QR tepat 5x5 cm */
    }

    .print-qr-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }

    .print-badge {
        background: var(--print-badge-bg);
        color: var(--print-badge-text);
        padding: 12px 35px;
        border-radius: 100px;
        display: inline-block;
    }

    .print-badge h2 {
        font-size: 2rem;
        font-weight: 800;
        margin: 0;
        letter-spacing: 0.5px;
        font-family: var(--print-font-family);
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    .page-title {
        font-size: 1.8rem;
        font-weight: 800;
    }
    .page-subtitle {
        color: var(--text-muted);
        margin-top: 5px;
    }
    
    .add-form {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        padding: 25px;
        margin-bottom: 30px;
        display: flex;
        align-items: flex-end;
        gap: 15px;
    }
    .form-group {
        flex: 1;
    }
    .form-group label {
        display: block;
        font-weight: 700;
        margin-bottom: 8px;
        font-size: 0.9rem;
        color: var(--text-muted);
    }
    .form-group input {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        font-family: inherit;
        font-size: 1rem;
        transition: border-color 0.2s;
    }
    .form-group input:focus {
        outline: none;
        border-color: var(--primary);
    }
    .btn-add {
        background: var(--primary);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
        font-size: 1rem;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
    }
    .btn-add:hover { background: var(--primary-hover); }

    .tables-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }
    .table-card {
        background: white;
        border-radius: 16px;
        border: 1px solid var(--border-color);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .table-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    .table-card-header {
        background: linear-gradient(135deg, #1e1e1e, #2d2d2d);
        color: white;
        padding: 25px;
        text-align: center;
    }
    .table-card-number {
        font-size: 3rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 5px;
    }
    .table-card-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        opacity: 0.7;
    }
    .table-card-body {
        padding: 20px;
        text-align: center;
    }
    .qr-preview {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        display: flex;
        justify-content: center;
    }
    .qr-preview img {
        width: 200px;
        height: 200px;
    }
    .qr-url {
        background: #f1f2f6;
        padding: 10px 15px;
        border-radius: 8px;
        font-size: 0.8rem;
        color: var(--text-muted);
        word-break: break-all;
        margin-bottom: 15px;
        text-align: left;
    }
    .table-card-actions {
        display: flex;
        gap: 10px;
    }
    .btn-print {
        flex: 1;
        background: #1b7339;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: background 0.2s;
    }
    .btn-print:hover { background: #14592a; }
    .btn-delete {
        background: #fff;
        color: var(--primary);
        border: 2px solid var(--primary);
        padding: 10px 15px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        transition: 0.2s;
    }
    .btn-delete:hover { background: var(--primary); color: white; }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 16px;
        border: 2px dashed var(--border-color);
    }
    .empty-icon { font-size: 4rem; margin-bottom: 15px; color: var(--border-color); }
    .empty-title { font-size: 1.3rem; font-weight: 700; margin-bottom: 8px; color: var(--text-muted); }
    .empty-desc { color: var(--text-muted); font-size: 0.95rem; }

    @media print {
        @page {
            margin: 0; /* Menghapus margin default cetakan browser */
        }
        
        /* 1. Sembunyikan seluruh layout dashboard admin secara total */
        .sidebar, 
        .top-navbar,
        .add-form,
        .tables-grid,
        .page-header,
        .alert,
        .empty-state {
            display: none !important;
        }
        
        /* 2. Reset paksa body & html */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100vh !important;
            width: 100vw !important;
            background: #ffffff !important;
            display: block !important; /* Matikan flex layout bawaan admin */
        }
        
        /* 3. Netralisir pembungkus layout utama (sangat penting untuk menghapus efek transform) */
        .main-content,
        .content-area {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            margin: 0 !important;
            padding: 0 !important;
            transform: none !important;   /* PENTING: Hapus transform dari animasi fadeIn agar fixed centering bekerja */
            animation: none !important;   /* PENTING: Matikan animasi */
            overflow: visible !important;
            background: transparent !important;
        }
        
        /* 4. Sembunyikan semua elemen kecuali printArea */
        body * {
            visibility: hidden !important;
        }
        
        #printArea, #printArea * {
            visibility: visible !important;
        }
        
        #printArea {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            background: #ffffff !important;
            backdrop-filter: none !important;
            box-sizing: border-box !important;
        }
        
        .print-card {
            box-shadow: none !important; /* Hilangkan shadow saat mencetak di kertas */
            background: #ffffff !important;
            margin: auto !important;
            transform: none !important;
            page-break-inside: avoid;
        }
        
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Manajemen Meja & QR Code</h1>
        <p class="page-subtitle">Buat dan kelola QR code untuk setiap meja restoran</p>
    </div>
</div>

<form action="{{ route('admin.tables.store') }}" method="POST" class="add-form">
    @csrf
    <div class="form-group">
        <label>Nomor Meja</label>
        <input type="text" name="table_number" placeholder="Contoh: 1, 2, 3, VIP-1" required>
    </div>
    <button type="submit" class="btn-add">
        <i class="fa-solid fa-plus"></i> Tambah Meja
    </button>
</form>

@if($errors->any())
    <div style="background: #ffeaa7; color: #d63031; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        @foreach($errors->all() as $error)
            <p style="margin: 0;">{{ $error }}</p>
        @endforeach
    </div>
@endif

@if($tables->count() > 0)
<div class="tables-grid">
    @foreach($tables as $table)
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-label">Meja</div>
            <div class="table-card-number">{{ $table->table_number }}</div>
        </div>
        <div class="table-card-body">
            @php
                // Mengambil IP/Host dari URL yang sedang diakses
                $dynamicUrl = url('/client/catalog?table=' . $table->id);
            @endphp
            <div class="qr-preview">
                {{-- Menggunakan Google Chart API untuk generate QR code --}}
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($dynamicUrl) }}" alt="QR Code Meja {{ $table->table_number }}" id="qr-img-{{ $table->id }}">
            </div>
            <div class="qr-url">
                <i class="fa-solid fa-link" style="margin-right: 5px;"></i>
                {{ $dynamicUrl }}
            </div>
            <div class="table-card-actions">
                <button type="button" class="btn-print" onclick="printQR({{ $table->id }}, '{{ $table->table_number }}')">
                    <i class="fa-solid fa-print"></i> Cetak QR
                </button>
                <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST" onsubmit="return confirm('Yakin hapus meja {{ $table->table_number }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="empty-state">
    <div class="empty-icon"><i class="fa-solid fa-qrcode"></i></div>
    <div class="empty-title">Belum ada meja yang dibuat</div>
    <div class="empty-desc">Tambahkan meja baru di atas untuk mulai membuat QR Code.</div>
</div>
@endif

{{-- Hidden print area --}}
<div id="printArea">
    <div class="print-card">
        <h1 class="print-brand">{{ strtoupper($storeName) }}</h1>
        <p class="print-subtitle">SCAN QR UNTUK MEMESAN</p>
        
        <div class="print-qr-wrapper">
            <img id="printQrImg" src="" alt="QR Code">
        </div>
        
        <div class="print-badge">
            <h2 id="printTableNum">Meja 1</h2>
        </div>
    </div>
</div>

<script>
function printQR(tableId, tableNumber) {
    const qrImg = document.getElementById('qr-img-' + tableId);
    const printArea = document.getElementById('printArea');
    const printQrImg = document.getElementById('printQrImg');
    const printTableNum = document.getElementById('printTableNum');

    printQrImg.src = qrImg.src;
    printTableNum.textContent = 'Meja ' + tableNumber;
    printArea.style.display = 'flex';

    setTimeout(() => {
        window.print();
        printArea.style.display = 'none';
    }, 300);
}
</script>
@endsection
