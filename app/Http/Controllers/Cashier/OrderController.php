<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['table', 'items.menu'])
            ->where('payment_status', 'unpaid')
            ->orderBy('created_at', 'desc')
            ->get();

        $onlineOrders = Order::with(['table', 'items.menu'])
            ->whereIn('order_type', ['delivery', 'pickup'])
            ->where('payment_status', 'paid')
            ->whereIn('order_status', ['confirmed', 'cooking'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $today = Carbon::today();
        
        $stats = [
            'unpaid' => Order::where('payment_status', 'unpaid')->count(),
            'online_ready' => Order::whereIn('order_type', ['delivery', 'pickup'])
                ->where('payment_status', 'paid')
                ->whereIn('order_status', ['confirmed', 'cooking'])
                ->count(),
            'completed' => Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [$today->copy()->startOfDay(), $today->copy()->endOfDay()])
                ->count(),
            'revenue' => Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [$today->copy()->startOfDay(), $today->copy()->endOfDay()])
                ->sum('total_amount'),
        ];

        return view('cashier.dashboard', compact('orders', 'onlineOrders', 'stats'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'order_status' => 'required|in:cooking,completed,cancelled',
            'payment_status' => 'required|in:unpaid,pending,paid',
            'payment_method' => 'nullable|in:cash,qris,bank_transfer',
            'cash_tendered' => 'nullable|numeric|min:0',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        $previousPaymentStatus = $order->payment_status;

        $order->order_status = $request->order_status;
        $order->payment_status = $request->payment_status;

        // Set payment method & cashier when marked as paid
        if ($request->payment_status === 'paid') {
            $order->payment_method = $request->payment_method ?? 'cash';
            $order->cashier_id = auth()->id();
            
            if ($order->payment_method === 'cash' && $request->cash_tendered) {
                $order->cash_tendered = $request->cash_tendered;
                $order->cash_change = $request->cash_tendered - $order->grand_total;
            } else {
                $order->payment_reference = $request->payment_reference;
            }
        }

        $order->save();

        // Track promo usage when payment status changes to paid
        if (($previousPaymentStatus === 'unpaid' || $previousPaymentStatus === 'pending') && $request->payment_status === 'paid') {
            if ($order->promotion_id) {
                \App\Models\Promotion::where('id', $order->promotion_id)->increment('used_count');
            }
        }

        return redirect()->route('cashier.dashboard')->with('success', 'Status pesanan #' . $order->id . ' berhasil diperbarui!');
    }

    /**
     * Terapkan promosi ke pesanan
     */
    public function applyPromotion(Request $request, Order $order)
    {
        $request->validate([
            'promo_code' => 'required|string'
        ]);

        if ($order->payment_status === 'paid') {
            return redirect()->back()->with('error', 'Pesanan sudah lunas, tidak dapat menggunakan promo.');
        }

        $promo = \App\Models\Promotion::valid()->where('code', strtoupper($request->promo_code))->first();

        if (!$promo) {
            return redirect()->back()->with('error', 'Kode promo tidak valid, kadaluarsa, atau kuota habis.');
        }

        // Base total without discount
        $baseTotal = $order->items->sum('subtotal');

        if ($baseTotal < $promo->min_order_amount) {
            return redirect()->back()->with('error', 'Pesanan belum memenuhi minimum order untuk promo ini (Rp ' . number_format($promo->min_order_amount, 0, ',', '.') . ').');
        }

        $discount = $promo->calculateDiscount($baseTotal);

        $order->promotion_id = $promo->id;
        $order->discount_amount = $discount;
        $order->total_amount = $baseTotal - $discount; // update total to reflect discount
        $order->save();

        return redirect()->back()->with('success', 'Promo berhasil diterapkan! Diskon: Rp ' . number_format($discount, 0, ',', '.'));
    }


    /**
     * Menampilkan riwayat transaksi hari ini yang sudah selesai
     */
    public function history()
    {
        $today = Carbon::today();
        
        $orders = Order::with(['table', 'items.menu'])
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$today->copy()->startOfDay(), $today->copy()->endOfDay()])
            ->orderBy('created_at', 'desc')
            // Using paginate instead of get() to prevent memory overload if there are thousands of daily orders
            ->paginate(50);

        // Agregasi dihitung langsung ke database, bukan dari collection
        $totalRevenue = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$today->copy()->startOfDay(), $today->copy()->endOfDay()])
            ->sum('total_amount');
            
        $totalOrders = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$today->copy()->startOfDay(), $today->copy()->endOfDay()])
            ->count();

        return view('cashier.history', compact('orders', 'totalRevenue', 'totalOrders'));
    }

    /**
     * Menampilkan nota/struk untuk dicetak
     */
    public function receipt(Order $order)
    {
        $order->load(['table', 'items.menu']);
        return view('cashier.receipt', compact('order'));
    }

}
