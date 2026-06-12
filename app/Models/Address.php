<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'label', 'recipient_name', 'phone',
        'street', 'rt_rw', 'kelurahan', 'kecamatan', 'city', 'postal_code', 'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute(): string
    {
        return collect([$this->street, $this->rt_rw, $this->kelurahan, $this->kecamatan, $this->city, $this->postal_code])
            ->filter()
            ->implode(', ');
    }
}
