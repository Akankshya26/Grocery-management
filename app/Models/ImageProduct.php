<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageProduct extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'product_id',
        'image_name',
        'created_at',
        'updated_at',
    ];
}
