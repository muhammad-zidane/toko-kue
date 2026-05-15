<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomizationOption;
use Illuminate\Http\Request;

class CustomizationController extends Controller
{
    public function index()
    {
        $options = CustomizationOption::orderBy('type')->orderBy('sort_order')->paginate(30);
        return view('admin.customizations', compact('options'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'        => 'required|in:rasa,ukuran,topping,lainnya',
            'name'        => 'required|string|max:100',
            'extra_price' => 'required|numeric|min:0',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        CustomizationOption::create([
            'type'        => $request->type,
            'name'        => $request->name,
            'extra_price' => $request->extra_price,
            'sort_order'  => $request->sort_order ?? 0,
            'is_active'   => true,
        ]);

        return back()->with('success', 'Opsi kustomisasi berhasil ditambahkan.');
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
