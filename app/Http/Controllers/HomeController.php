<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('is_active', true)->orderBy('order')->get();
        $categories = Category::withCount('products')->get();
        $featuredProducts = Product::with('category')->where('is_available', true)->take(3)->get();
        $testimonials = ProductReview::with(['user', 'product'])->latest()->take(3)->get();

        return view('home.index', compact('banners', 'categories', 'featuredProducts', 'testimonials'));
    }
}
