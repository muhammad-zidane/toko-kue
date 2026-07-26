<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
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

    public function approve(ProductReview $review)
    {
        $review->update(['is_approved' => !$review->is_approved]);
        $label = $review->is_approved ? 'disetujui' : 'dibatalkan persetujuannya';
        return back()->with('success', "Ulasan berhasil {$label}.");
    }

    public function destroy(ProductReview $review)
    {
        $review->delete();
        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
