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
        $cart = session()->get('cart', []);
        $cart[$request->product_id] = ['quantity' => $request->quantity ?? 1];
        session()->put('cart', $cart);
        return redirect('/cart');
    }
}