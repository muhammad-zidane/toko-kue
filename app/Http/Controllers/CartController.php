<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            if ($product) {
                $cartItems[] = ['product' => $product, 'quantity' => $item['quantity']];
            }
        }
        return view('cart.index', compact('cartItems'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'nullable|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = ['quantity' => $quantity];
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Produk ditambahkan ke keranjang!');
    }

    public function remove(Request $request)
    {
        $cart = session()->get('cart', []);
        $ids = $request->ids ?? [];

        if (is_array($ids)) {
            foreach ($ids as $id) {
                unset($cart[$id]);
            }
        }

        session()->put('cart', $cart);
        return response()->json(['success' => true]);
    }

    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index');
    }

    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong!');
        }

        // Ambil semua produk di keranjang
        $cartItems = [];
        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            if ($product) {
                $cartItems[] = ['product' => $product, 'quantity' => $item['quantity']];
            }
        }

        return view('orders.create', compact('cartItems'));
    }
}