<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Payment;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalOrders    = Order::count();
        $totalProducts  = Product::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalRevenue   = Payment::where('status', 'paid')->sum('amount');
        $latestOrders   = Order::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalProducts',
            'totalCustomers',
            'totalRevenue',
            'latestOrders'
        ));
    }

    public function orders()
    {
        $orders = Order::with('user', 'payment')->latest()->paginate(10);
        return view('admin.orders', compact('orders'));
    }

    public function updateOrderStatus(Order $order, $status)
    {
        $order->update(['status' => $status]);
        return back()->with('success', 'Status pesanan berhasil diperbarui!');
    }
}