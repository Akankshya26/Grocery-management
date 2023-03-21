<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    /* Fillable */
    protected $fillable = [
        'category_id',
        'name'
    ];

    /* Relations */
    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function subProd()
    {
        return $this->hasMany(Product::class);
    }
}
