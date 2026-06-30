<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Menu;
use App\Models\Customization;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Menampilkan semua pesanan (admin overview)
     */
    public function index(Request $request)
    {
        Order::autoProgressAllActive();
        
        $query = Order::with(['table', 'items.menu']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('payment')) {
            $query->where('payment_status', $request->payment);
        }

        // Filter berdasarkan tipe order (dine_in, delivery, pickup)
        if ($request->filled('order_type')) {
            $query->where('order_type', $request->order_type);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Menampilkan detail pesanan
     */
    public function show(Order $order)
    {
        Order::autoProgressAllActive();
        
        $order->load(['table', 'items.menu']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update status pesanan oleh admin
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'order_status' => 'required|in:pending_payment,confirmed,cooking,completed,cancelled',
        ]);

        $order->order_status = $request->order_status;
        $order->save();

        return redirect()->back()->with('success', 'Status pesanan #' . $order->id . ' berhasil diperbarui!');
    }

    /**
     * Update status pembayaran oleh admin
     * Pemotongan stok dilakukan di sini saat pembayaran dikonfirmasi (paid)
     */
    public function updatePaymentStatus(Request $request, Order $order, \App\Services\StockDeductionService $stockService)
    {
        $request->validate([
            'payment_status' => 'required|in:unpaid,pending,paid,expired,cancelled,refunded',
            'payment_method' => 'nullable|in:cash,qris,bank_transfer,ewallet,qris_online',
            'cash_tendered' => 'nullable|numeric|min:0',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        $previousPaymentStatus = $order->payment_status;

        $order->payment_status = $request->payment_status;

        if ($request->payment_status === 'paid') {
            $order->payment_method = $request->payment_method ?? 'cash';
            $order->cashier_id = auth()->id();
            
            // Simpan detail POS
            if ($request->payment_method === 'cash') {
                $order->cash_tendered = $request->cash_tendered;
                // Pastikan kembalian dihitung aman di sisi server
                if ($order->cash_tendered >= $order->grand_total) {
                    $order->cash_change = $order->cash_tendered - $order->grand_total;
                }
            } else {
                $order->payment_reference = $request->payment_reference;
            }
        }

        $order->save();

        // Potong stok HANYA saat status pembayaran berubah dari 'pending'/'unpaid' ke 'paid'
        if (in_array($previousPaymentStatus, ['pending', 'unpaid']) && $request->payment_status === 'paid') {
            try {
                $stockService->deductOrderStock($order);
            } catch (\Exception $e) {
                // Log error if stock deduction fails, but don't revert payment here for admin
                \Illuminate\Support\Facades\Log::error("Stock deduction failed for Order #{$order->id}: " . $e->getMessage());
                return redirect()->back()->with('success', 'Pembayaran berhasil diperbarui, tapi pemotongan stok gagal: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Status pembayaran pesanan #' . $order->id . ' berhasil diperbarui!');
    }
}
