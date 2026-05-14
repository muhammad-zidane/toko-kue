<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description',
        'price', 'stock', 'image', 'is_available'
    ];

    /**
     * Gunakan slug sebagai route key agar URL produk lebih SEO-friendly.
     *
     * @return string
     */
    public function getRouteKeyName()
{
    return 'slug';
}

    /**
     * Kategori yang memiliki produk ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Semua order item yang mereferensikan produk ini.
     * Digunakan untuk menghitung jumlah terjual (withCount).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
}