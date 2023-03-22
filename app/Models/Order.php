<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    /* Fillable */
    public $timestamps = false;
    protected $fillable = [
        'fname',
        'lname',
        'email',
        'address1',
        'address2',
        'phone',
        'city',
        'state',
        'country',
        'pincode',

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
    public function CArts()
    {
        return $this->belongsTo(CartItem::class);
    }
}
