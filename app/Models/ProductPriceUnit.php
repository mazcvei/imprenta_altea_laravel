<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPriceUnit extends Model
{
    protected $table = 'product_prices_units';
    protected $fillable = [
        'product_id',
        'price',
        'units'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
