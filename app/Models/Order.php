<?php

namespace App\Models;

use App\Models\OrderItems;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    /* Fillable */
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'product_id',
        'user_address_id',
        'order_num',
        'status',
        'expected_delivery_date',


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
        return $this->hasMany(OrderItems::class, 'order_id');
    }
    public function CArts()
    {
        return $this->belongsTo(CartItem::class);
    }
}
