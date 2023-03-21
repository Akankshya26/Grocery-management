<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    /* Fillable */
    protected $fillable = [
        'user_id',
        'product_id',
    ];

    /* Relations */
    public function userWishlist()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function productWishlist()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
