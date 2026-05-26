<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart';
    protected $fillable = [
        'user_id',
        'product_id',
        'price_unit_id',
        'image'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function priceUnit()
    {
        return $this->belongsTo(ProductPriceUnit::class, 'price_unit_id');
    }   
}
