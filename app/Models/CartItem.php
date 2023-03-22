<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends Model
{
    use HasFactory;
    /* Fillable */

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity'
    ];

    /* Relations */
    public function userCart()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function productCart()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function OrderCart()
    {
        return $this->hasMany(Order::class);
    }
}
