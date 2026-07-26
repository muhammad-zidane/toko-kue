<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPenjualanExport;
use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    /**
     * Tampilkan halaman dashboard admin.
     */
    public function dashboard()
    {
        ['now' => $now, 'startOfThisMonth' => $startOfThisMonth, 'startOfLastMonth' => $startOfLastMonth, 'endOfLastMonth' => $endOfLastMonth]
            = $this->monthBoundaries();

        $orderStats = Order::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as this_month,
                SUM(CASE WHEN created_at BETWEEN ? AND ? THEN 1 ELSE 0 END) as last_month,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending
            ', [$startOfThisMonth, $startOfLastMonth, $endOfLastMonth, 'pending'])
            ->first();

        $totalOrders        = (int) $orderStats->total;
        $ordersThisMonth    = (int) $orderStats->this_month;
        $ordersLastMonth    = (int) $orderStats->last_month;
        $pendingOrdersCount = (int) $orderStats->pending;
        $orderGrowth        = $this->calculateGrowthPercent($ordersThisMonth, $ordersLastMonth);

        $revenueStats = Payment::where('status', 'paid')
            ->selectRaw('
                COALESCE(SUM(amount), 0) as total,
                COALESCE(SUM(CASE WHEN created_at >= ? THEN amount ELSE 0 END), 0) as this_month,
                COALESCE(SUM(CASE WHEN created_at BETWEEN ? AND ? THEN amount ELSE 0 END), 0) as last_month
            ', [$startOfThisMonth, $startOfLastMonth, $endOfLastMonth])
            ->first();

        $totalRevenue     = (float) $revenueStats->total;
        $revenueThisMonth = (float) $revenueStats->this_month;
        $revenueLastMonth = (float) $revenueStats->last_month;
        $revenueGrowth    = $this->calculateGrowthPercent($revenueThisMonth, $revenueLastMonth);

        $customerStats = User::where('role', 'customer')
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as this_month,
                SUM(CASE WHEN created_at BETWEEN ? AND ? THEN 1 ELSE 0 END) as last_month
            ', [$startOfThisMonth, $startOfLastMonth, $endOfLastMonth])
            ->first();

        $totalCustomers     = (int) $customerStats->total;
        $customersThisMonth = (int) $customerStats->this_month;
        $customersLastMonth = (int) $customerStats->last_month;
        $customerGrowth     = $this->calculateGrowthPercent($customersThisMonth, $customersLastMonth);

        $latestOrders = Order::with('user', 'orderItems.product')->latest()->take(5)->get();

        $dailyRevenue    = $this->buildDailyRevenue();
        $maxDaily        = max(array_column($dailyRevenue, 'amount') ?: [1]) ?: 1;
        $revenueThisWeek = array_sum(array_column($dailyRevenue, 'amount'));

        $topProducts = Product::withCount('orderItems')->with('category')->orderByDesc('order_items_count')->take(3)->get();
        $maxSold     = max($topProducts->first()->order_items_count ?? 0, 1);

        $recentActivities = $latestOrders->map(function ($order) use ($now) {
            $minsAgo = max(0, (int) round($order->created_at->diffInMinutes($now)));

            if ($minsAgo < 60) {
                $timeLabel = $minsAgo . ' menit yang lalu';
            } elseif ($minsAgo < 1440) {
                $timeLabel = floor($minsAgo / 60) . ' jam yang lalu';
            } else {
                $timeLabel = floor($minsAgo / 1440) . ' hari yang lalu';
            }

            $colorMap = [
                'pending'    => 'bg-pink',
                'processing' => 'bg-blue',
                'completed'  => 'bg-green',
            ];

            return [
                'order_code' => $order->order_code,
                'user_name'  => $order->user->name ?? '-',
                'status'     => $order->status,
                'color'      => $colorMap[$order->status] ?? 'bg-red',
                'time_label' => $timeLabel,
            ];
        });

        $totalProducts = Product::count();

        return view('admin.dashboard', compact(
            'totalOrders', 'totalProducts', 'totalCustomers', 'totalRevenue',
            'ordersThisMonth', 'orderGrowth',
            'revenueThisMonth', 'revenueGrowth',
            'customersThisMonth', 'customerGrowth',
            'pendingOrdersCount',
            'latestOrders',
            'dailyRevenue', 'maxDaily', 'revenueThisWeek',
            'topProducts', 'maxSold',
            'recentActivities'
        ));
    }

    /**
     * Tampilkan daftar semua pelanggan (non-admin) beserta statistik.
     */
    public function customers()
    {
        $startOfThisMonth = Carbon::now()->startOfMonth();

        $stats = User::customers()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as new_this_month
            ', [$startOfThisMonth])
            ->first();

        $totalCustomers = (int) $stats->total;
        $newCustomers   = (int) $stats->new_this_month;
        $totalOrders    = (int) Order::whereHas('user', fn ($q) => $q->customers())->count();

        $customers = User::customers()
            ->withCount('orders')
            ->with(['orders' => fn ($q) => $q->select('id', 'user_id', 'status', 'created_at')])
            ->latest()
            ->paginate(20);

        return view('admin.customers', compact('customers', 'totalCustomers', 'newCustomers', 'totalOrders'));
    }

    /**
     * Tampilkan halaman analitik.
     */
    public function analytics(Request $request)
    {
        ['now' => $now, 'startOfThisMonth' => $startOfThisMonth, 'startOfLastMonth' => $startOfLastMonth, 'endOfLastMonth' => $endOfLastMonth]
            = $this->monthBoundaries();

        // Filter tanggal dari request
        $dari   = $request->filled('dari')   ? $request->dari   : $startOfThisMonth->format('Y-m-d');
        $sampai = $request->filled('sampai') ? $request->sampai : $now->format('Y-m-d');

        $dariCarbon   = Carbon::parse($dari)->startOfDay();
        $sampaiCarbon = Carbon::parse($sampai)->endOfDay();

        // Stats bulan ini
        $revenueThisMonth = (float) Payment::where('status', 'paid')->where('created_at', '>=', $startOfThisMonth)->sum('amount');
        $revenueLastMonth = (float) Payment::where('status', 'paid')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('amount');
        $growthPercent    = $this->calculateGrowthPercent($revenueThisMonth, $revenueLastMonth, 1);

        $ordersThisMonth = Order::where('created_at', '>=', $startOfThisMonth)->count();
        $avgOrderValue   = $ordersThisMonth > 0 ? round($revenueThisMonth / $ordersThisMonth) : 0;

        // Data berdasarkan filter tanggal
        $filteredOrders  = Order::whereBetween('created_at', [$dariCarbon, $sampaiCarbon]);
        $totalFilterOrders   = (clone $filteredOrders)->count();
        $totalFilterRevenue  = (float) Payment::where('status', 'paid')
            ->whereBetween('created_at', [$dariCarbon, $sampaiCarbon])->sum('amount');
        $totalItemsTerjual   = \App\Models\OrderItem::whereHas('order', fn($q) =>
            $q->whereBetween('created_at', [$dariCarbon, $sampaiCarbon]))->sum('quantity');
        $avgFilterOrder = $totalFilterOrders > 0 ? round($totalFilterRevenue / $totalFilterOrders) : 0;

        // Grafik penjualan per hari
        $penjualanPerHari = Order::whereBetween('created_at', [$dariCarbon, $sampaiCarbon])
            ->selectRaw('DATE(created_at) as tanggal, COUNT(*) as jumlah_pesanan, SUM(total_price) as total')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $dailyRevenue = $this->buildDailyRevenue();
        $maxDaily     = max(array_column($dailyRevenue, 'amount') ?: [1]) ?: 1;

        $topProducts = Product::withCount('orderItems')->with('category')->orderByDesc('order_items_count')->take(10)->get();
        $maxSold     = max($topProducts->first()->order_items_count ?? 0, 1);

        $statusCounts  = Order::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status')->toArray();
        $totalOrdersAll = array_sum($statusCounts) ?: 1;

        $categories = Category::withCount('products')->get();
        $maxProd    = $categories->max('products_count') ?: 1;

        return view('admin.analytics', compact(
            'revenueThisMonth', 'revenueLastMonth', 'growthPercent',
            'ordersThisMonth', 'avgOrderValue',
            'dailyRevenue', 'maxDaily',
            'topProducts', 'maxSold',
            'statusCounts', 'totalOrdersAll',
            'categories', 'maxProd',
            'dari', 'sampai',
            'totalFilterOrders', 'totalFilterRevenue', 'totalItemsTerjual', 'avgFilterOrder',
            'penjualanPerHari'
        ));
    }

    public function exportLaporan(Request $request)
    {
        $request->validate([
            'dari'   => 'required|date',
            'sampai' => 'required|date|after_or_equal:dari',
        ]);

        $filename = 'laporan-penjualan-' . $request->dari . '-sd-' . $request->sampai . '.xlsx';
        return Excel::download(new LaporanPenjualanExport($request->dari, $request->sampai), $filename);
    }

    public function finance()
    {
        $payments = Payment::with('order.user')->latest()->paginate(50);

        $stats = Payment::selectRaw('status, COUNT(*) as cnt, COALESCE(SUM(amount), 0) as total')
            ->whereIn('status', ['paid', 'unpaid'])
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $totalRevenue    = (float) ($stats['paid']->total   ?? 0);
        $pendingPayments = (float) ($stats['unpaid']->total ?? 0);
        $paidCount       = (int)   ($stats['paid']->cnt     ?? 0);
        $pendingCount    = (int)   ($stats['unpaid']->cnt   ?? 0);

        return view('admin.finance', compact('payments', 'totalRevenue', 'pendingPayments', 'paidCount', 'pendingCount'));
    }

    /**
     * Tampilkan daftar semua produk di panel admin (paginated 15 per halaman).
     */
    public function adminProducts()
    {
        $products = Product::with('category')->latest()->paginate(15);

        return view('admin.products', compact('products'));
    }

    /**
     * PRODUCTION CALENDAR
     */
    public function productionCalendar(Request $request)
    {
        $month = $request->integer('month', now()->month);
        $year  = $request->integer('year', now()->year);

        $orders = Order::with('orderItems.product')
            ->whereNotIn('status', ['cancelled'])
            ->whereYear('delivery_date', $year)
            ->whereMonth('delivery_date', $month)
            ->get()
            ->groupBy(fn($o) => $o->delivery_date?->format('Y-m-d'));

        return view('admin.production-calendar', compact('orders', 'month', 'year'));
    }

    private function monthBoundaries(): array
    {
        $now = Carbon::now();
        return [
            'now'              => $now,
            'startOfThisMonth' => $now->copy()->startOfMonth(),
            'startOfLastMonth' => $now->copy()->subMonth()->startOfMonth(),
            'endOfLastMonth'   => $now->copy()->subMonth()->endOfMonth(),
        ];
    }

    private function buildDailyRevenue(): array
    {
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $start    = Carbon::now()->subDays(6)->startOfDay();

        $totals = Payment::where('status', 'paid')
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, SUM(amount) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $result = [];
        for ($i = 6; $i >= 0; $i--) {
            $date     = Carbon::now()->subDays($i);
            $result[] = [
                'day'    => $dayNames[$date->dayOfWeek],
                'amount' => (float) ($totals[$date->format('Y-m-d')] ?? 0),
            ];
        }

        return $result;
    }

    private function calculateGrowthPercent(float|int $current, float|int $previous, int $decimals = 0): float|int
    {
        if ($previous <= 0) {
            return 0;
        }

        return round(($current - $previous) / $previous * 100, $decimals);
    }
}
