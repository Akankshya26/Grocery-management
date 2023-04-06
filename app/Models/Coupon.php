<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    /* Fillable */
    protected $fillable = [
        'title',
        'code',
        'value',
        'type', //amount,percentage
        'min_order_amt',
        'is_one_time',
        'is_available',
        'expires_at'
    ];
    public function couponOrder()
    {
        return $this->hasMany(Order::class, 'coupon_id');
    }
}
