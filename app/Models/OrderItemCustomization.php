<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemCustomization extends Model
{
    protected $fillable = ['order_item_id', 'customization_option_id', 'value', 'extra_price'];

    public function option()
    {
        return $this->belongsTo(CustomizationOption::class, 'customization_option_id');
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
