<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, Uuids;
    protected $fillable = [
        'category_id',
        'sub_category_id',
        'name',
        'image',
        'price',
        'discount',
        'is_emi_available',
        'is_available',
        'manufactured_at',
        'expires_at',
        'tax'
    ];

    /* Relations */
    public function img()
    {
        return $this->hasMany(ImageProduct::class, 'product_id');
    }
    public function prod()
    {
        return $this->belongsTo(Category::class);
    }
    public function prodWishlist()
    {
        return $this->belongsTo(Wishlist::class);
    }
}
