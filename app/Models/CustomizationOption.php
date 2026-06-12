<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomizationOption extends Model
{
    protected $fillable = ['category_id', 'type', 'name', 'extra_price', 'is_active', 'sort_order'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public static function typeLabel(string $type): string
    {
        return match($type) {
            'rasa'    => 'Rasa',
            'ukuran'  => 'Ukuran',
            'topping' => 'Topping',
            default   => 'Lainnya',
        };
    }
}
