<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'product_id',
        'amount',
        'discount',
        'tax',
        'quantity'
    ];
    public function userCart()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function productCart()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
