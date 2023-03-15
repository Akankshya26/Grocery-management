<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id',
        'sub_category_id',
        'name',
        'price',
        'discount',
        'is_emi_available',
        'is_available',
        'manufactured_at',
        'expires_at',
        'tax'
    ];
    public function img()
    {
        return $this->hasMany(ImageProduct::class, 'product_id');
    }
    public function prod()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
