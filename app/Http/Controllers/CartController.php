<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index()
    {
        $cart      = session()->get('cart', []);
        $cartItems = $this->cartService->resolveItems($cart);

        return view('cart.index', compact('cartItems'));
    }

    /**
     * Tambah produk ke keranjang (disimpan di session).
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id'          => 'required|exists:products,id',
            'quantity'            => 'nullable|integer|min:1',
            'note'                => 'nullable|string',
            'customizations_json' => 'nullable|string',
        ]);

        $cart              = session()->get('cart', []);
        $productId         = $request->product_id;
        $quantity          = $request->quantity ?? 1;
        $note              = $request->note;
        $rawCustomizations = json_decode($request->customizations_json ?? '[]', true) ?: [];
        $customizations    = $this->cartService->normalizeCustomizationIds($rawCustomizations);

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

        $cartCount = collect(session()->get('cart', []))->sum('quantity');

        if ($request->expectsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Produk ditambahkan ke keranjang!',
                'cart_count' => $cartCount,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Produk ditambahkan ke keranjang!');
    }

    /**
     * Perbarui jumlah atau catatan item di keranjang.
     */
    public function updateItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity'   => 'nullable|integer|min:1',
            'note'       => 'nullable|string',
        ]);

        $cart      = session()->get('cart', []);
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
     */
    public function remove(Request $request)
    {
        $cart = session()->get('cart', []);
        $ids  = $request->ids ?? [];

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
     */
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

        $products      = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
        $optionsMap    = $this->cartService->loadOptionsFromCart($cart);
        $cartItems     = [];
        $stockWarnings = [];

        foreach ($cart as $id => $item) {
            $product = $products->get($id);
            if (!$product) {
                continue;
            }

            if (!$product->is_available || $product->stock < $item['quantity']) {
                $stockWarnings[] = "Stok \"{$product->name}\" tidak mencukupi (tersedia: {$product->stock}).";
            }

            $customizationIds = $this->cartService->normalizeCustomizationIds($item['customizations'] ?? []);
            $cartItems[] = [
                'product'              => $product,
                'quantity'             => $item['quantity'],
                'note'                 => $item['note'] ?? null,
                'customizations'       => $customizationIds,
                'customizationOptions' => collect($customizationIds)
                    ->map(fn($oid) => $optionsMap->get($oid))
                    ->filter()
                    ->values(),
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