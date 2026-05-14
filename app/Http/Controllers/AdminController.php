<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $now = Carbon::now();
        $startOfThisMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth   = $now->copy()->subMonth()->endOfMonth();

        $ordersThisMonth = Order::where('created_at', '>=', $startOfThisMonth)->count();
        $ordersLastMonth = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $orderGrowth     = $this->calculateGrowthPercent($ordersThisMonth, $ordersLastMonth);

        $revenueThisMonth = Payment::where('status', 'paid')->where('created_at', '>=', $startOfThisMonth)->sum('amount');
        $revenueLastMonth = Payment::where('status', 'paid')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('amount');
        $revenueGrowth    = $this->calculateGrowthPercent($revenueThisMonth, $revenueLastMonth);

        $customersThisMonth = User::where('role', 'customer')->where('created_at', '>=', $startOfThisMonth)->count();
        $customersLastMonth = User::where('role', 'customer')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $customerGrowth     = $this->calculateGrowthPercent($customersThisMonth, $customersLastMonth);

        $pendingOrdersCount = Order::where('status', 'pending')->count();
        $latestOrders       = Order::with('user', 'orderItems.product')->latest()->take(5)->get();

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

        $totalOrders    = Order::count();
        $totalProducts  = Product::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalRevenue   = Payment::where('status', 'paid')->sum('amount');

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

    public function orders()
    {
        $orders = Order::with(['user', 'payment', 'orderItems'])->latest()->paginate(10);

        return view('admin.orders', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        $order->load(['user', 'orderItems.product', 'payment']);

        return view('admin.order-detail', compact('order'));
    }

    public function downloadProof(Order $order)
    {
        $order->load('payment');

        if (!$order->payment?->proof_image) {
            abort(404, 'Bukti pembayaran tidak ditemukan.');
        }

        $path = $order->payment->proof_image;

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            abort(404, 'File bukti pembayaran tidak ditemukan.');
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename  = 'bukti-' . $order->order_code . '.' . $extension;

        return \Illuminate\Support\Facades\Storage::disk('public')->download($path, $filename);
    }

    public function updateOrderStatus(Order $order, string $status)
    {
        $validStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            return back()->withErrors(['status' => 'Status tidak valid.']);
        }

        $order->update(['status' => $status]);

        if ($status === 'completed' && $order->payment) {
            $order->payment->update(['status' => 'paid', 'paid_at' => now()]);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    public function categories()
    {
        $categories = Category::withCount('products')->latest()->get();

        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Category::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function destroyCategory(Category $category)
    {
        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus!');
    }

    public function customers()
    {
        $customers = User::where('role', '!=', 'admin')
            ->withCount('orders')
            ->with(['orders' => fn ($q) => $q->select('id', 'user_id', 'status', 'created_at')])
            ->latest()
            ->get();

        $totalCustomers = $customers->count();
        $newCustomers   = $customers->filter(
            fn ($c) => $c->created_at->month === now()->month && $c->created_at->year === now()->year
        )->count();
        $totalOrders = $customers->sum('orders_count');

        return view('admin.customers', compact('customers', 'totalCustomers', 'newCustomers', 'totalOrders'));
    }

    public function analytics()
    {
        $now              = Carbon::now();
        $startOfThisMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth   = $now->copy()->subMonth()->endOfMonth();

        $revenueThisMonth = (float) Payment::where('status', 'paid')->where('created_at', '>=', $startOfThisMonth)->sum('amount');
        $revenueLastMonth = (float) Payment::where('status', 'paid')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('amount');
        $growthPercent    = $this->calculateGrowthPercent($revenueThisMonth, $revenueLastMonth, 1);

        $ordersThisMonth = Order::where('created_at', '>=', $startOfThisMonth)->count();
        $avgOrderValue   = $ordersThisMonth > 0 ? round($revenueThisMonth / $ordersThisMonth) : 0;

        $dailyRevenue = $this->buildDailyRevenue();
        $maxDaily     = max(array_column($dailyRevenue, 'amount') ?: [1]) ?: 1;

        $topProducts = Product::withCount('orderItems')->with('category')->orderByDesc('order_items_count')->take(5)->get();
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
            'categories', 'maxProd'
        ));
    }

    public function finance()
    {
        $payments = Payment::with('order.user')->latest()->get();

        $totalRevenue    = $payments->where('status', 'paid')->sum('amount');
        $pendingPayments = $payments->where('status', 'unpaid')->sum('amount');
        $paidCount       = $payments->where('status', 'paid')->count();
        $pendingCount    = $payments->where('status', 'unpaid')->count();

        return view('admin.finance', compact('payments', 'totalRevenue', 'pendingPayments', 'paidCount', 'pendingCount'));
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user = auth()->user();
        $user->name  = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Pengaturan berhasil disimpan!');
    }

    public function adminProducts()
    {
        $products = Product::with('category')->latest()->paginate(15);

        return view('admin.products', compact('products'));
    }

    private function buildDailyRevenue(): array
    {
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $result   = [];

        for ($i = 6; $i >= 0; $i--) {
            $date   = Carbon::now()->subDays($i);
            $amount = (float) Payment::where('status', 'paid')
                ->whereBetween('created_at', [$date->copy()->startOfDay(), $date->copy()->endOfDay()])
                ->sum('amount');

            $result[] = ['day' => $dayNames[$date->dayOfWeek], 'amount' => $amount];
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
