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
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'note' => $item['note'] ?? null,
                ];
            }
        }
        return view('cart.index', compact('cartItems'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'nullable|integer|min:1',
            'note'       => 'nullable|string',
        ]);

        $cart = session()->get('cart', []);
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;
        $note = $request->note;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
            if ($request->has('note')) {
                $cart[$productId]['note'] = $note;
            }
        } else {
            $cart[$productId] = [
                'quantity' => $quantity,
                'note' => $note,
            ];
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Produk ditambahkan ke keranjang!');
    }

    public function updateItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'nullable|integer|min:1',
            'note' => 'nullable|string',
        ]);

        $cart = session()->get('cart', []);
        $productId = (string) $request->product_id;

        if (!isset($cart[$productId])) {
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan di keranjang.'], 404);
        }

        if ($request->filled('quantity')) {
            $cart[$productId]['quantity'] = (int) $request->quantity;
        }

        if ($request->has('note')) {
            $cart[$productId]['note'] = $request->note;
        }

        session()->put('cart', $cart);

        return response()->json(['success' => true]);
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

        $cartItems = [];
        $stockWarnings = [];

        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            if (!$product) {
                continue;
            }

            if (!$product->is_available || $product->stock < $item['quantity']) {
                $stockWarnings[] = "Stok \"{$product->name}\" tidak mencukupi (tersedia: {$product->stock}).";
            }

            $cartItems[] = [
                'product'  => $product,
                'quantity' => $item['quantity'],
                'note'     => $item['note'] ?? null,
            ];
        }

        if (!empty($stockWarnings)) {
            return redirect()->route('cart.index')->withErrors($stockWarnings);
        }

        return view('orders.create', compact('cartItems'));
    }
}