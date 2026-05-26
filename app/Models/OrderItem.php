<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $fillable = [
        'order_id',
        'product_id',
        'product_price_id',
        'image',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function priceUnit()
    {
        return $this->belongsTo(ProductPriceUnit::class, 'product_price_id');
    }
}
