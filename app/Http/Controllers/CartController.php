<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Tampilkan isi keranjang belanja dari session.
     *
     * @return \Illuminate\View\View
     */
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

    /**
     * Tambah produk ke keranjang (disimpan di session).
     * Jika produk sudah ada, jumlahnya ditambahkan.
     *
     * @param  Request $request  Input: product_id (wajib), quantity (default 1)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id'        => 'required|exists:products,id',
            'quantity'          => 'nullable|integer|min:1',
            'note'              => 'nullable|string',
            'customizations_json' => 'nullable|string',
        ]);

        $cart = session()->get('cart', []);
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;
        $note = $request->note;
        $customizations = json_decode($request->customizations_json ?? '[]', true) ?: [];

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
            if ($request->has('note')) {
                $cart[$productId]['note'] = $note;
            }
            if ($request->has('customizations_json')) {
                $cart[$productId]['customizations'] = $customizations;
            }
        } else {
            $cart[$productId] = [
                'quantity'       => $quantity,
                'note'           => $note,
                'customizations' => $customizations,
            ];
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Produk ditambahkan ke keranjang!');
    }

    /**
     * Perbarui jumlah atau catatan item di keranjang.
     *
     * @param  Request $request  Input: product_id (wajib), quantity (opsional), note (opsional)
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Hapus satu atau beberapa item dari keranjang berdasarkan product ID.
     *
     * @param  Request $request  Input: ids[] array product ID yang dihapus
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Kosongkan seluruh isi keranjang belanja dari session.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index');
    }

    /**
     * Tampilkan form checkout dengan item-item dari keranjang session.
     * Redirect ke keranjang jika keranjang kosong.
     *
     * @param  Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
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

        $savedAddresses = auth()->user()->addresses()->latest()->get();
        $dpMinAmount    = config('app.dp_min_amount', 200000);
        $dpPercentage   = config('app.dp_percentage', 50);

        return view('orders.create', compact('cartItems', 'savedAddresses', 'dpMinAmount', 'dpPercentage'));
    }
}