<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRating extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'user_id',
        'rating'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
