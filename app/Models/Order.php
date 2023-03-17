<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'product_id',
        'user_address_id',
        'status',
        'is_cod',
        'expected_delivery_date',
        'delivery_date'
    ];

    /* Relations */
    public function userOrder()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function productOrder()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    public function OrderItm()
    {
        return $this->hasMany(Order::class, 'order_id');
    }
}
