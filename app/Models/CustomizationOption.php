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

    public function getTypeColorClassAttribute(): string
    {
        return match($this->type) {
            'rasa'    => 'bg-rose-50 text-rose-700 border border-rose-200',
            'ukuran'  => 'bg-blue-50 text-blue-700 border border-blue-200',
            'topping' => 'bg-amber-50 text-amber-700 border border-amber-200',
            default   => 'bg-gray-100 text-gray-700 border border-gray-200',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeLabel($this->type);
    }
}
