<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /* Fillable */
    protected $fillable = [
        'name',
        'slug',
        'status'
    ];

    /* Relations */
    public function subCategory()
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
