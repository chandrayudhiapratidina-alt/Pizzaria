<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        
        // Dynamic targets from Database (Settings)
        $targets = [
            'daily' => \App\Models\Setting::get('target_daily', 1000000),
            'weekly' => \App\Models\Setting::get('target_weekly', 7000000),
            'monthly' => \App\Models\Setting::get('target_monthly', 30000000),
        ];

        // Sales Aggregation
        $sales = [
            'daily' => Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [$today->copy()->startOfDay(), $today->copy()->endOfDay()])
                ->sum(\Illuminate\Support\Facades\DB::raw('total_amount - COALESCE(delivery_fee, 0)')),
            'weekly' => Order::where('payment_status', 'paid')->where('created_at', '>=', $startOfWeek)->sum(\Illuminate\Support\Facades\DB::raw('total_amount - COALESCE(delivery_fee, 0)')),
            'monthly' => Order::where('payment_status', 'paid')->where('created_at', '>=', $startOfMonth)->sum(\Illuminate\Support\Facades\DB::raw('total_amount - COALESCE(delivery_fee, 0)')),
            'all_time' => Order::where('payment_status', 'paid')->sum(\Illuminate\Support\Facades\DB::raw('total_amount - COALESCE(delivery_fee, 0)'))
        ];

        // Dynamic Chart Data with Smart Grouping
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::today()->subDays(6)->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::today()->endOfDay();
        
        $chartLabels = [];
        $chartData = [];
        
        $diffDays = $startDate->copy()->startOfDay()->diffInDays($endDate->copy()->startOfDay());

        if ($diffDays == 0) {
            // Cuma 1 Hari -> Group by Hour (00:00 - 23:00)
            $ordersGrouped = Order::selectRaw('HOUR(created_at) as hour, SUM(total_amount - COALESCE(delivery_fee, 0)) as total')
                            ->where('payment_status', 'paid')
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->groupBy('hour')
                            ->pluck('total', 'hour')
                            ->toArray();

            for ($i = 0; $i < 24; $i++) {
                $chartLabels[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                $chartData[] = $ordersGrouped[$i] ?? 0;
            }
        } elseif ($diffDays > 60) {
            // Lebih dari 60 Hari -> Group by Month
            $ordersGrouped = Order::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_amount - COALESCE(delivery_fee, 0)) as total')
                            ->where('payment_status', 'paid')
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->groupBy('year', 'month')
                            ->get()
                            ->mapWithKeys(function ($item) {
                                return [$item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT) => $item->total];
                            })
                            ->toArray();

            $currentDate = $startDate->copy()->startOfMonth();
            while($currentDate <= $endDate->copy()->endOfMonth()) {
                $monthString = $currentDate->format('Y-m');
                $chartLabels[] = $currentDate->format('M Y');
                $chartData[] = $ordersGrouped[$monthString] ?? 0;
                $currentDate->addMonth();
            }
        } else {
            // 2 hingga 60 Hari -> Group by Date (Default)
            $ordersGrouped = Order::selectRaw('DATE(created_at) as date, SUM(total_amount - COALESCE(delivery_fee, 0)) as total')
                            ->where('payment_status', 'paid')
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->groupBy('date')
                            ->orderBy('date', 'asc')
                            ->pluck('total', 'date')
                            ->toArray();

            $currentDate = $startDate->copy();
            while($currentDate <= $endDate) {
                $dateString = $currentDate->format('Y-m-d');
                $chartLabels[] = $currentDate->format('d M');
                $chartData[] = $ordersGrouped[$dateString] ?? 0;
                $currentDate->addDay();
            }
        }

        // Recent completed transactions for the report table
        $recentTransactions = Order::where('payment_status', 'paid')
                                   ->orderBy('created_at', 'desc')
                                   ->take(50)
                                   ->get();

        return view('admin.analytics.index', compact('sales', 'targets', 'recentTransactions', 'chartLabels', 'chartData'));
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $filename = 'Laporan_Penjualan_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SalesReportExport($startDate, $endDate), $filename);
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $query = Order::with(['items', 'promotion'])->where('payment_status', 'paid');
        
        if ($startDate) {
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        // Limit data to prevent DOMPDF from crashing on large datasets
        $orders = $query->orderBy('created_at', 'desc')->take(1000)->get();
        
        // Sum from DB instead of collection for accuracy of total
        $totalRevenue = (clone $query)->sum(\Illuminate\Support\Facades\DB::raw('total_amount - COALESCE(delivery_fee, 0)'));
        $totalDiscount = (clone $query)->sum('discount_amount');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.exports.sales_report_pdf', compact('orders', 'startDate', 'endDate', 'totalRevenue', 'totalDiscount'));
        
        $filename = 'Laporan_Penjualan_' . Carbon::now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }
}
