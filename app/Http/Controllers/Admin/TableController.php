<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    /**
     * Menampilkan daftar semua meja dengan QR Code
     */
    public function index()
    {
        $tables = Table::orderBy('table_number', 'asc')->get();
        return view('admin.tables.index', compact('tables'));
    }

    /**
     * Menyimpan meja baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|string|unique:tables,table_number',
        ]);

        $table = Table::create([
            'table_number' => $request->table_number,
            'status' => 'available',
        ]);

        return redirect()->route('admin.tables.index')->with('success', 'Meja ' . $table->table_number . ' berhasil ditambahkan!');
    }

    /**
     * Menghapus meja
     */
    public function destroy(Table $table)
    {
        $table->delete();
        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil dihapus.');
    }
}
