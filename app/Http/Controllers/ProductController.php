<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Tampilkan semua produk dikelompokkan per kategori (halaman publik).
     *
     * @return \Illuminate\View\View
     */
    public function index()
{
    $categories = \App\Models\Category::with('products')->get();
    return view('products.index', compact('categories'));
}

    /**
     * Tampilkan halaman detail produk (diakses via slug).
     *
     * @param  Product $product  Produk yang ditampilkan
     * @return \Illuminate\View\View
     */
    public function show(Product $product)
{
    $product->load([
        'reviews' => function ($query) {
            $query->with(['user', 'images'])->latest();
        },
    ]);

    return view('products.show', compact('product'));
}

    /**
     * Tampilkan form tambah produk baru (admin).
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Simpan produk baru ke database. Slug dibuat otomatis dari nama.
     * Gambar disimpan ke storage/products jika diupload.
     *
     * @param  Request $request  Input: name, category_id, description, price, stock, image (opsional)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'category_id', 'description', 'price', 'stock']);
        $data['slug'] = $this->generateUniqueSlug($request->name);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    /**
     * Tampilkan form edit produk (admin).
     *
     * @param  Product $product  Produk yang akan diedit
     * @return \Illuminate\View\View
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Simpan perubahan data produk. Gambar lama tidak dihapus otomatis.
     *
     * @param  Request $request  Input: name, category_id, description, price, stock, image (opsional)
     * @param  Product $product  Produk yang diperbarui
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'category_id', 'description', 'price', 'stock']);
        $data['slug'] = $this->generateUniqueSlug($request->name, $product->id);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    private function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (Product::where('slug', $slug)->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * Hapus produk dari database (admin).
     *
     * @param  Product $product  Produk yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus!');
    }
}