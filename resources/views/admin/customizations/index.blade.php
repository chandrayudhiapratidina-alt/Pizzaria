@extends('admin.layouts.app')
@section('title', 'Kustomisasi & Topping')
@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-list-ul" style="color: var(--primary);"></i> Kustomisasi & Topping
        </h2>
        <p style="color: var(--text-muted); margin-top: 5px;">Atur pilihan tambahan (topping, ukuran, crust) untuk setiap menu beserta potongan stok bahan bakunya.</p>
    </div>
    <a href="{{ route('admin.customizations.create') }}" class="btn-primary" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-plus"></i> Tambah Kustomisasi
    </a>
</div>

<div class="card" style="padding: 25px;">
    {{-- GLOBAL CUSTOMIZATIONS --}}
    @if($globalCustomizations->count() > 0)
    <div style="margin-bottom: 30px; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden;">
        <div style="background: #f8f9fa; padding: 15px 20px; font-weight: 700; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 1.1rem; color: var(--primary);">
                <i class="fa-solid fa-globe"></i> Kustomisasi Global (Semua Menu)
            </div>
        </div>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: white; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">
                    <th style="padding: 12px 20px;">Tipe</th>
                    <th style="padding: 12px 20px;">Nama</th>
                    <th style="padding: 12px 20px;">Harga Tambahan</th>
                    <th style="padding: 12px 20px;">Sisa Stok Porsi</th>
                    <th style="padding: 12px 20px; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($globalCustomizations as $cust)
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 12px 20px;">
                        <span style="background: #e6f4ea; color: var(--green); padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">{{ $cust->type }}</span>
                    </td>
                    <td style="padding: 12px 20px; font-weight: 600;">{{ $cust->name }}</td>
                    <td style="padding: 12px 20px;">Rp {{ number_format($cust->additional_price, 0, ',', '.') }}</td>
                    <td style="padding: 12px 20px;">
                        <span style="font-weight: 800; color: {{ $cust->available_stock <= 5 ? 'var(--primary)' : 'var(--text-main)' }};">{{ $cust->available_stock }} porsi</span>
                    </td>
                    <td style="padding: 12px 20px; text-align: right;">
                        <a href="{{ route('admin.customizations.edit', $cust->id) }}" class="btn-primary" style="background: #f1f2f6; color: var(--text-main); font-size: 0.85rem; padding: 6px 12px; margin-right: 5px; text-decoration: none;"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('admin.customizations.destroy', $cust->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus kustomisasi ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-primary" style="background: white; border: 1px solid var(--border-color); color: var(--primary); font-size: 0.85rem; padding: 6px 12px;"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- CATEGORY LEVEL CUSTOMIZATIONS --}}
    @foreach($categories as $category)
        <div style="margin-bottom: 30px; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden;">
            <div style="background: #e6f7ff; padding: 15px 20px; font-weight: 700; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <div style="font-size: 1.1rem; color: #0077b6;">
                    <i class="fa-solid fa-tags"></i> Kategori: {{ $category->name }}
                </div>
            </div>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <!-- ... header and body similar to global ... -->
                <thead>
                    <tr style="background: white; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">
                        <th style="padding: 12px 20px;">Tipe</th>
                        <th style="padding: 12px 20px;">Nama</th>
                        <th style="padding: 12px 20px;">Harga Tambahan</th>
                        <th style="padding: 12px 20px;">Sisa Stok Porsi</th>
                        <th style="padding: 12px 20px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($category->customizations as $cust)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 12px 20px;">
                            <span style="background: #e6f4ea; color: var(--green); padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">{{ $cust->type }}</span>
                        </td>
                        <td style="padding: 12px 20px; font-weight: 600;">{{ $cust->name }}</td>
                        <td style="padding: 12px 20px;">Rp {{ number_format($cust->additional_price, 0, ',', '.') }}</td>
                        <td style="padding: 12px 20px;">
                            <span style="font-weight: 800; color: {{ $cust->available_stock <= 5 ? 'var(--primary)' : 'var(--text-main)' }};">{{ $cust->available_stock }} porsi</span>
                        </td>
                        <td style="padding: 12px 20px; text-align: right;">
                            <a href="{{ route('admin.customizations.edit', $cust->id) }}" class="btn-primary" style="background: #f1f2f6; color: var(--text-main); font-size: 0.85rem; padding: 6px 12px; margin-right: 5px; text-decoration: none;"><i class="fa-solid fa-pen"></i></a>
                            <form action="{{ route('admin.customizations.destroy', $cust->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus kustomisasi ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-primary" style="background: white; border: 1px solid var(--border-color); color: var(--primary); font-size: 0.85rem; padding: 6px 12px;"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    {{-- MENU LEVEL CUSTOMIZATIONS --}}
    @foreach($menus as $menu)
        <div style="margin-bottom: 30px; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden;">
            <div style="background: #fdf5e6; padding: 15px 20px; font-weight: 700; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <div style="font-size: 1.1rem; color: #d35400;">
                    <i class="fa-solid fa-pizza-slice"></i> Menu: {{ $menu->name }}
                </div>
            </div>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: white; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">
                        <th style="padding: 12px 20px;">Tipe</th>
                        <th style="padding: 12px 20px;">Nama</th>
                        <th style="padding: 12px 20px;">Harga Tambahan</th>
                        <th style="padding: 12px 20px;">Sisa Stok Porsi</th>
                        <th style="padding: 12px 20px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($menu->customizations as $cust)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 12px 20px;">
                            <span style="background: #e6f4ea; color: var(--green); padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">{{ $cust->type }}</span>
                        </td>
                        <td style="padding: 12px 20px; font-weight: 600;">{{ $cust->name }}</td>
                        <td style="padding: 12px 20px;">Rp {{ number_format($cust->additional_price, 0, ',', '.') }}</td>
                        <td style="padding: 12px 20px;">
                            <span style="font-weight: 800; color: {{ $cust->available_stock <= 5 ? 'var(--primary)' : 'var(--text-main)' }};">{{ $cust->available_stock }} porsi</span>
                        </td>
                        <td style="padding: 12px 20px; text-align: right;">
                            <a href="{{ route('admin.customizations.edit', $cust->id) }}" class="btn-primary" style="background: #f1f2f6; color: var(--text-main); font-size: 0.85rem; padding: 6px 12px; margin-right: 5px; text-decoration: none;"><i class="fa-solid fa-pen"></i></a>
                            <form action="{{ route('admin.customizations.destroy', $cust->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus kustomisasi ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-primary" style="background: white; border: 1px solid var(--border-color); color: var(--primary); font-size: 0.85rem; padding: 6px 12px;"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
    
    @if($globalCustomizations->count() == 0 && $categories->count() == 0 && $menus->count() == 0)
        <div style="text-align: center; padding: 50px; color: var(--text-muted);">
            <i class="fa-solid fa-list-ul" style="font-size: 3rem; margin-bottom: 15px; color: #dfe4ea;"></i>
            <p style="font-weight: 600; font-size: 1.1rem; color: var(--text-main);">Belum ada kustomisasi terdaftar</p>
            <p>Silakan klik tombol Tambah Kustomisasi untuk membuat opsi tambahan.</p>
        </div>
    @endif
</div>
@endsection
