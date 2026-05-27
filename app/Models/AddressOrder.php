<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddressOrder extends Model
{
    protected $table = 'order_addresses';
    protected $fillable = [
        'order_id',
        'address_line',
        'city',
        'province',
        'postal_code',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
