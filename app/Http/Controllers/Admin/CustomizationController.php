<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CustomizationOption;
use Illuminate\Http\Request;

class CustomizationController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $selectedCategory = $request->get('category_id');

        $query = CustomizationOption::with('category')
            ->orderBy('type')
            ->orderBy('sort_order');

        if ($selectedCategory) {
            $query->where('category_id', $selectedCategory);
        }

        $options = $query->paginate(30)->withQueryString();

        return view('admin.customizations', compact('options', 'categories', 'selectedCategory'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'type'        => 'required|in:rasa,ukuran,topping,lainnya',
            'name'        => 'required|string|max:100',
            'extra_price' => 'required|numeric|min:0',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        CustomizationOption::create([
            'category_id' => $request->category_id,
            'type'        => $request->type,
            'name'        => $request->name,
            'extra_price' => $request->extra_price,
            'sort_order'  => $request->sort_order ?? 0,
            'is_active'   => true,
        ]);

        return back()->with('success', 'Opsi kustomisasi berhasil ditambahkan.');
    }

    public function update(Request $request, CustomizationOption $option)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'type'        => 'required|in:rasa,ukuran,topping,lainnya',
            'name'        => 'required|string|max:100',
            'extra_price' => 'required|numeric|min:0',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $option->update([
            'category_id' => $request->category_id,
            'type'        => $request->type,
            'name'        => $request->name,
            'extra_price' => $request->extra_price,
            'sort_order'  => $request->sort_order ?? 0,
        ]);

        return back()->with('success', 'Opsi kustomisasi berhasil diperbarui.');
    }

    public function toggle(CustomizationOption $option)
    {
        $option->update(['is_active' => !$option->is_active]);
        return back()->with('success', 'Status opsi diperbarui.');
    }

    public function destroy(CustomizationOption $option)
    {
        $option->delete();
        return back()->with('success', 'Opsi kustomisasi berhasil dihapus.');
    }
}
