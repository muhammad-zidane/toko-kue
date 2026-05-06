<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Cek apakah user adalah admin. Jika bukan, abort 403.
     */
    private function checkAdmin()
    {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }
    }

    public function dashboard()
    {
        $this->checkAdmin();

        $now = Carbon::now();
        $startOfThisMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // Stats with growth
        $ordersThisMonth = Order::where('created_at', '>=', $startOfThisMonth)->count();
        $ordersLastMonth = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $orderGrowth = $ordersLastMonth > 0 ? round(($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth * 100) : 0;

        $revenueThisMonth = Payment::where('status', 'paid')->where('created_at', '>=', $startOfThisMonth)->sum('amount');
        $revenueLastMonth = Payment::where('status', 'paid')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('amount');
        $revenueGrowth = $revenueLastMonth > 0 ? round(($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth * 100) : 0;

        $customersThisMonth = User::where('role', 'customer')->where('created_at', '>=', $startOfThisMonth)->count();
        $customersLastMonth = User::where('role', 'customer')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $customerGrowth = $customersLastMonth > 0 ? round(($customersThisMonth - $customersLastMonth) / $customersLastMonth * 100) : 0;

        $pendingOrdersCount = Order::where('status', 'pending')->count();

        // Recent Orders
        $latestOrders = Order::with('user', 'orderItems.product')->latest()->take(5)->get();

        // Daily Revenue (7 days)
        $dailyRevenue = [];
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();
            $amount = Payment::where('status', 'paid')->whereBetween('created_at', [$dayStart, $dayEnd])->sum('amount');
            $dailyRevenue[] = [
                'day' => $dayNames[$date->dayOfWeek],
                'amount' => (float) $amount,
            ];
        }
        $maxDaily = max(array_column($dailyRevenue, 'amount') ?: [1]);
        if ($maxDaily == 0) $maxDaily = 1;
        $revenueThisWeek = array_sum(array_column($dailyRevenue, 'amount'));

        // Top Products
        $topProducts = Product::withCount('orderItems')
            ->with('category')
            ->orderByDesc('order_items_count')
            ->take(3)
            ->get();
        $maxSold = $topProducts->first()->order_items_count ?? 1;
        if ($maxSold == 0) $maxSold = 1;

        // Recent Activities (from recent orders)
        $recentActivities = $latestOrders->map(function ($order) use ($now) {
            $minsAgo = $now->diffInMinutes($order->created_at);
            if ($minsAgo < 60) $timeLabel = $minsAgo . ' menit yang lalu';
            elseif ($minsAgo < 1440) $timeLabel = floor($minsAgo / 60) . ' jam yang lalu';
            else $timeLabel = floor($minsAgo / 1440) . ' hari yang lalu';

            switch ($order->status) {
                case 'pending':
                    $message = 'Pesanan baru <strong>' . $order->order_code . '</strong> masuk dari ' . ($order->user->name ?? '-') . '.';
                    $colorClass = 'bg-pink';
                    break;
                case 'processing':
                    $message = 'Pesanan <strong>' . $order->order_code . '</strong> sedang diproses.';
                    $colorClass = 'bg-blue';
                    break;
                case 'completed':
                    $message = 'Pesanan <strong>' . $order->order_code . '</strong> telah selesai.';
                    $colorClass = 'bg-green';
                    break;
                default:
                    $message = 'Pesanan <strong>' . $order->order_code . '</strong> dibatalkan.';
                    $colorClass = 'bg-red';
            }
            return compact('message', 'timeLabel', 'colorClass');
        });

        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalRevenue = Payment::where('status', 'paid')->sum('amount');

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
        $this->checkAdmin();

        $orders = Order::with('user', 'payment')->latest()->paginate(10);
        return view('admin.orders', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        $this->checkAdmin();

        $order->load('user', 'orderItems.product', 'payment');
        return view('admin.order-detail', compact('order'));
    }

    public function updateOrderStatus(Order $order, $status)
    {
        $this->checkAdmin();

        $validStatuses = ['pending', 'processing', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return back()->withErrors(['status' => 'Status tidak valid.']);
        }

        $order->update(['status' => $status]);

        // Jika order dikonfirmasi bayar, update payment juga
        if ($status === 'completed' && $order->payment) {
            $order->payment->update(['status' => 'paid', 'paid_at' => now()]);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    public function categories()
    {
        $this->checkAdmin();

        $categories = Category::withCount('products')->latest()->get();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function destroyCategory(Category $category)
    {
        $this->checkAdmin();

        $category->delete();
        return back()->with('success', 'Kategori berhasil dihapus!');
    }

    public function customers()
    {
        $this->checkAdmin();

        $customers = User::where('role', '!=', 'admin')
            ->with('orders')
            ->latest()
            ->get();

        $totalCustomers = $customers->count();
        $newCustomers = $customers->filter(fn($c) => $c->created_at->month === now()->month && $c->created_at->year === now()->year)->count();
        $totalOrders = $customers->sum(fn($c) => $c->orders->count());

        return view('admin.customers', compact('customers', 'totalCustomers', 'newCustomers', 'totalOrders'));
    }

    public function analytics()
    {
        $this->checkAdmin();

        $now = Carbon::now();
        $startOfThisMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        $revenueThisMonth = (float) Payment::where('status', 'paid')->where('created_at', '>=', $startOfThisMonth)->sum('amount');
        $revenueLastMonth = (float) Payment::where('status', 'paid')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('amount');
        $growthPercent = $revenueLastMonth > 0 ? round(($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth * 100, 1) : 0;

        $ordersThisMonth = Order::where('created_at', '>=', $startOfThisMonth)->count();
        $avgOrderValue = $ordersThisMonth > 0 ? round($revenueThisMonth / $ordersThisMonth) : 0;

        // Daily revenue (7 days)
        $dailyRevenue = [];
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $amount = (float) Payment::where('status', 'paid')
                ->whereBetween('created_at', [$date->copy()->startOfDay(), $date->copy()->endOfDay()])
                ->sum('amount');
            $dailyRevenue[] = ['day' => $dayNames[$date->dayOfWeek], 'amount' => $amount];
        }
        $maxDaily = max(array_column($dailyRevenue, 'amount') ?: [1]);
        if ($maxDaily == 0) $maxDaily = 1;

        // Top products
        $topProducts = Product::withCount('orderItems')
            ->with('category')
            ->orderByDesc('order_items_count')
            ->take(5)
            ->get();
        $maxSold = $topProducts->first()->order_items_count ?? 1;
        if ($maxSold == 0) $maxSold = 1;

        // Order status distribution
        $statusCounts = Order::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status')->toArray();
        $totalOrdersAll = array_sum($statusCounts) ?: 1;

        // Category stats
        $categories = Category::withCount('products')->get();
        $maxProd = $categories->max('products_count') ?: 1;

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
        $this->checkAdmin();

        $payments = Payment::with('order.user')->latest()->get();

        $totalRevenue = $payments->where('status', 'paid')->sum('amount');
        $pendingPayments = $payments->where('status', 'unpaid')->sum('amount');
        $paidCount = $payments->where('status', 'paid')->count();
        $pendingCount = $payments->where('status', 'unpaid')->count();

        return view('admin.finance', compact('payments', 'totalRevenue', 'pendingPayments', 'paidCount', 'pendingCount'));
    }

    public function settings()
    {
        $this->checkAdmin();
        return view('admin.settings');
    }

    public function updateSettings(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return back()->with('success', 'Pengaturan berhasil disimpan!');
    }

    public function adminProducts()
    {
        $this->checkAdmin();

        $products = Product::with('category')->latest()->paginate(15);
        return view('admin.products', compact('products'));
    }
}