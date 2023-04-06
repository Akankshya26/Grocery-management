<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, Uuids;

    /* Fillable */
    protected $fillable = [
        'category_id',
        'sub_category_id',

        'name',
        'image',
        'price',
        'discount',
        'quantity',
        'is_emi_available',
        'is_available',
        'manufactured_at',
        'expires_at',
        'tax'
    ];

    /* Relations */
    public function images()
    {
        return $this->hasMany(ImageProduct::class, 'product_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function prodWishlist()
    {
        return $this->belongsTo(Wishlist::class);
    }
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function productRating()
    {
        return $this->hasMany(ProductRating::class, 'product_id')->select('id', 'user_id', 'product_id', 'rating');
    }
    public function OrderItem()
    {
        return $this->hasMany(OrderItems::class, 'product_id');
    }

    public function carts()
    {
        return $this->hasMany(CartItem::class, 'product_id');
    }
}
