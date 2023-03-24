<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    use HasFactory;

    /* Fillable */
    protected $fillable = [
        'order_id',
        'product_id',
        'price',
        'quantity',

    ];

    /* Relations */
    public function orders()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function prodOrder()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
