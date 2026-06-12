<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPenjualanExport;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ShippingZone;
use App\Models\User;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    /**
     * Tampilkan halaman dashboard admin.
     *
     * Menghitung KPI bulan ini (pesanan, pendapatan, pelanggan) beserta
     * persentase pertumbuhannya dibanding bulan lalu, grafik pendapatan
     * harian 7 hari terakhir, top 3 produk terlaris, dan aktivitas terbaru.
     *
     * @return \Illuminate\View\View
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
     * Tampilkan daftar semua pesanan untuk admin (paginated 10 per halaman).
     *
     * @return \Illuminate\View\View
     */
    public function orders()
    {
        $orders = Order::with(['user', 'payment', 'orderItems'])->latest()->paginate(10);

        return view('admin.orders', compact('orders'));
    }

    /**
     * Tampilkan detail satu pesanan beserta item dan data pembayaran.
     *
     * @param  Order $order  Pesanan yang akan ditampilkan
     * @return \Illuminate\View\View
     */
    public function orderDetail(Order $order)
    {
        $order->load(['user', 'orderItems.product', 'orderItems.customizations.option', 'payment']);

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

    /**
     * Perbarui status pesanan. Jika status menjadi 'completed',
     * pembayaran terkait juga otomatis ditandai 'paid'.
     *
     * @param  Order  $order   Pesanan yang diperbarui
     * @param  string $status  Status baru: pending|processing|shipped|completed|cancelled
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateOrderStatus(Order $order, string $status)
    {
        $validStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            return back()->withErrors(['status' => 'Status tidak valid.']);
        }

        $order->load(['user', 'payment']);
        $order->update(['status' => $status]);

        try {
            \Illuminate\Support\Facades\Mail::to($order->user->email)
                ->queue(new \App\Mail\OrderStatusUpdatedMail($order));
        } catch (\Throwable) {}

        if ($status === 'completed' && $order->payment) {
            $order->payment->update(['status' => 'paid', 'paid_at' => now()]);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    /**
     * Tampilkan daftar semua kategori beserta jumlah produk di tiap kategori.
     *
     * @return \Illuminate\View\View
     */
    public function categories()
    {
        $categories = Category::withCount('products')->latest()->get();

        return view('admin.categories', compact('categories'));
    }

    /**
     * Simpan kategori baru ke database. Slug dibuat otomatis dari nama.
     *
     * @param  Request $request  Input: name (wajib), description (opsional)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);

        return back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return back()->with('success', 'Kategori berhasil diperbarui!');
    }


    /**
     * Hapus kategori dari database.
     *
     * @param  Category $category  Kategori yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyCategory(Category $category)
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }


        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus!');
    }

    /**
     * Tampilkan daftar semua pelanggan (non-admin) beserta statistik.
     *
     * @return \Illuminate\View\View
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
     * Tampilkan halaman analitik: pendapatan bulanan, grafik harian,
     * top 5 produk terlaris, distribusi status pesanan, dan statistik kategori.
     *
     * @return \Illuminate\View\View
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

        // Stats bulan ini (tetap untuk kartu ringkasan)
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

        // Grafik penjualan per hari (untuk Chart.js)
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
     * Tampilkan form pengaturan akun admin.
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        return view('admin.settings');
    }

    /**
     * Simpan perubahan data akun admin (nama, email, password opsional).
     *
     * @param  Request $request  Input: name, email, password (opsional), password_confirmation
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Tampilkan daftar semua produk di panel admin (paginated 15 per halaman).
     *
     * @return \Illuminate\View\View
     */
    public function adminProducts()
    {
        $products = Product::with('category')->latest()->paginate(15);

        return view('admin.products', compact('products'));
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

    // ─── NOTIFICATIONS ──────────────────────────────────────────────────────────

    public function markAllNotificationsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    }

    public function markNotificationRead(string $id)
    {
        auth()->user()->notifications()->where('id', $id)->first()?->markAsRead();
        return response()->json(['ok' => true]);
    }

    // ─── BANNERS ────────────────────────────────────────────────────────────────

    public function banners()
    {
        $banners = Banner::orderBy('order')->get();
        return view('admin.banners', compact('banners'));
    }

    public function storeBanner(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image'    => 'required|image|max:3072',
            'link'     => 'nullable|string|max:255',
            'order'    => 'nullable|integer',
        ]);

        $data = $request->only(['title', 'subtitle', 'link', 'order']);
        $data['image']     = $request->file('image')->store('banners', 'public');
        $data['is_active'] = true;

        Banner::create($data);
        return back()->with('success', 'Banner berhasil ditambahkan.');
    }

    public function updateBanner(Request $request, Banner $banner)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'subtitle'  => 'nullable|string|max:255',
            'image'     => 'nullable|image|max:3072',
            'link'      => 'nullable|string|max:255',
            'order'     => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only(['title', 'subtitle', 'link', 'order']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);
        return back()->with('success', 'Banner berhasil diperbarui.');
    }

    public function destroyBanner(Banner $banner)
    {
        Storage::disk('public')->delete($banner->image);
        $banner->delete();
        return back()->with('success', 'Banner berhasil dihapus.');
    }

    // ─── VOUCHERS ───────────────────────────────────────────────────────────────

    public function vouchers()
    {
        $vouchers = Voucher::latest()->paginate(20);
        return view('admin.vouchers', compact('vouchers'));
    }

    public function storeVoucher(Request $request)
    {
        $request->validate([
            'code'        => 'required|string|unique:vouchers,code',
            'type'        => 'required|in:percent,fixed',
            'value'       => 'required|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'min_purchase'=> 'nullable|numeric|min:0',
            'expires_at'  => 'nullable|date',
        ]);

        Voucher::create([
            'code'         => strtoupper($request->code),
            'type'         => $request->type,
            'value'        => $request->value,
            'usage_limit'  => $request->usage_limit,
            'min_purchase' => $request->min_purchase ?? 0,
            'is_active'    => true,
            'expires_at'   => $request->expires_at,
        ]);

        return back()->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function updateVoucher(Request $request, Voucher $voucher)
    {
        $request->validate([
            'type'        => 'required|in:percent,fixed',
            'value'       => 'required|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'min_purchase'=> 'nullable|numeric|min:0',
            'expires_at'  => 'nullable|date',
            'is_active'   => 'nullable|boolean',
        ]);

        $voucher->update([
            'type'         => $request->type,
            'value'        => $request->value,
            'usage_limit'  => $request->usage_limit,
            'min_purchase' => $request->min_purchase ?? 0,
            'is_active'    => $request->boolean('is_active'),
            'expires_at'   => $request->expires_at,
        ]);

        return back()->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroyVoucher(Voucher $voucher)
    {
        $voucher->delete();
        return back()->with('success', 'Voucher berhasil dihapus.');
    }

    // ─── SHIPPING ZONES ─────────────────────────────────────────────────────────

    public function shippingZones()
    {
        $zones = ShippingZone::orderBy('area_name')->get();
        return view('admin.shipping-zones', compact('zones'));
    }

    public function storeShippingZone(Request $request)
    {
        $request->validate([
            'area_name' => 'required|string|max:255',
            'cost'      => 'required|numeric|min:0',
        ]);

        ShippingZone::create(['area_name' => $request->area_name, 'cost' => $request->cost]);
        return back()->with('success', 'Zona pengiriman ditambahkan.');
    }

    public function updateShippingZone(Request $request, ShippingZone $zone)
    {
        $request->validate([
            'area_name'    => 'required|string|max:255',
            'cost'         => 'required|numeric|min:0',
            'is_available' => 'nullable|boolean',
        ]);

        $zone->update([
            'area_name'    => $request->area_name,
            'cost'         => $request->cost,
            'is_available' => $request->has('is_available'),
        ]);

        return back()->with('success', 'Zona pengiriman diperbarui.');
    }

    public function destroyShippingZone(ShippingZone $zone)
    {
        $zone->delete();
        return back()->with('success', 'Zona pengiriman dihapus.');
    }

    // ─── PRODUCTION CALENDAR ────────────────────────────────────────────────────

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

    // ─── REVIEWS MODERATION ─────────────────────────────────────────────────────

    public function reviews(Request $request)
    {
        $query = ProductReview::with('user', 'product')->latest();

        if ($request->filled('status')) {
            $query->where('is_approved', $request->status === 'approved');
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->integer('rating'));
        }

        $reviews  = $query->paginate(20)->withQueryString();
        $counts   = ProductReview::selectRaw('is_approved, COUNT(*) as cnt')->groupBy('is_approved')->pluck('cnt', 'is_approved');
        $pendingCount  = (int) ($counts[0] ?? 0);
        $approvedCount = (int) ($counts[1] ?? 0);

        return view('admin.reviews', compact('reviews', 'pendingCount', 'approvedCount'));
    }

    public function approveReview(ProductReview $review)
    {
        $review->update(['is_approved' => !$review->is_approved]);
        $label = $review->is_approved ? 'disetujui' : 'dibatalkan persetujuannya';
        return back()->with('success', "Ulasan berhasil {$label}.");
    }

    public function destroyReview(ProductReview $review)
    {
        $review->delete();
        return back()->with('success', 'Ulasan berhasil dihapus.');
    }

    // ─── PAYMENT CONFIRM / REJECT ────────────────────────────────────────────────

    public function confirmPayment(Request $request, Order $order)
    {
        $payment = $order->payment;
        if (!$payment) {
            return back()->withErrors(['error' => 'Pembayaran tidak ditemukan.']);
        }

        $payment->update(['status' => 'paid', 'paid_at' => now()]);

        $isFullyPaid = (float) $payment->amount >= (float) $order->total_price;
        $order->update([
            'status'         => 'processing',
            'paid_amount'    => $payment->amount,
            'payment_status' => $isFullyPaid ? 'paid' : 'dp',
        ]);

        return back()->with('success', 'Pembayaran dikonfirmasi.');
    }

    public function rejectPayment(Request $request, Order $order)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);
        $order->payment?->update(['status' => 'failed']);
        $order->update([
            'status'         => 'pending',
            'payment_status' => 'unpaid',
            'paid_amount'    => 0,
            'notes'          => $request->reason ? '[Pembayaran Ditolak] ' . $request->reason : $order->notes,
        ]);
        return back()->with('success', 'Pembayaran ditolak.');
    }
}
