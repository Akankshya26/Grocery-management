<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'product_id',
        'amount',
        'discount',
        'tax',
        'quantity',
        'is_gift'

    ];

    /* Relations */
    public function orderItems()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function prodOrder()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
