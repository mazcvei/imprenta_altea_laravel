<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'image',
        'stock',
    ];

    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }

    public function orders() {
        return $this->belongsToMany(Order::class, 'order_items')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
                    
    }

    public function prices() {
        return $this->hasMany(ProductPriceUnit::class);
    }

}
