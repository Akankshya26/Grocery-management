<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'product_id',
        'amount',
        'discount',
        'tax',
        'quantity'
    ];
    protected $dates = ['deleted_at'];

    /* Relations */
    public function userCart()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function productCart()
    {
        return $this->hasMany(Product::class, 'product_id', 'id');
    }
    public function AbcdCart()
    {
        return $this->hasMany(Order::class);
    }
}
