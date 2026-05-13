<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductReviewImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductReviewController extends Controller
{
    public function index(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load([
            'orderItems.product',
            'payment',
            'productReviews.images',
        ]);

        return view('orders.reviews', compact('order'));
    }

    public function store(Request $request, Order $order, Product $product)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['payment', 'orderItems']);

        $isEligible = $order->status === 'completed' && ($order->payment?->status === 'paid');
        if (!$isEligible) {
            return back()->withErrors(['review' => 'Ulasan hanya bisa dikirim untuk pesanan yang sudah selesai dan dibayar.']);
        }

        $productBelongsToOrder = $order->orderItems->contains(fn ($item) => (int) $item->product_id === (int) $product->id);
        if (!$productBelongsToOrder) {
            abort(404);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $existing = ProductReview::where('user_id', auth()->id())
            ->where('order_id', $order->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            return back()->withErrors(['review' => 'Kamu sudah mengirim ulasan untuk produk ini pada pesanan ini.']);
        }

        $review = ProductReview::create([
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'product_id' => $product->id,
            'rating' => (int) $request->rating,
            'comment' => $request->comment,
        ]);

        $files = $request->file('images', []);
        foreach ($files as $file) {
            $path = $file->store('review_images', 'public');
            ProductReviewImage::create([
                'product_review_id' => $review->id,
                'path' => $path,
            ]);
        }

        return redirect()
            ->route('orders.reviews.index', $order)
            ->with('success', 'Ulasan berhasil dikirim!');
    }

    public function update(Request $request, Order $order, ProductReview $review)
    {
        $this->authorizeReviewOwnership($order, $review);

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $review->update([
            'rating' => (int) $request->rating,
            'comment' => $request->comment,
        ]);

        $files = $request->file('images', []);
        foreach ($files as $file) {
            $path = $file->store('review_images', 'public');
            ProductReviewImage::create([
                'product_review_id' => $review->id,
                'path' => $path,
            ]);
        }

        return redirect()
            ->route('orders.reviews.index', $order)
            ->with('success', 'Ulasan berhasil diperbarui.');
    }

    public function destroy(Order $order, ProductReview $review)
    {
        $this->authorizeReviewOwnership($order, $review);

        $review->load('images');
        foreach ($review->images as $image) {
            if ($image->path) {
                Storage::disk('public')->delete($image->path);
            }
        }

        $review->delete();

        return redirect()
            ->route('orders.reviews.index', $order)
            ->with('success', 'Ulasan berhasil dihapus.');
    }

    public function destroyImage(Order $order, ProductReview $review, ProductReviewImage $image)
    {
        $this->authorizeReviewOwnership($order, $review);

        if ((int) $image->product_review_id !== (int) $review->id) {
            abort(404);
        }

        if ($image->path) {
            Storage::disk('public')->delete($image->path);
        }
        $image->delete();

        return redirect()
            ->route('orders.reviews.index', $order)
            ->with('success', 'Gambar ulasan berhasil dihapus.');
    }

    private function authorizeReviewOwnership(Order $order, ProductReview $review): void
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ((int) $review->user_id !== (int) auth()->id()) {
            abort(403);
        }

        if ((int) $review->order_id !== (int) $order->id) {
            abort(404);
        }
    }
}

