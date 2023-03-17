<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'product_id',
        'order_item_id',
        'total_amount',
        'tax',
        'discount',
        'quantity',
        'payment_status', //'pending' ,'done'
        'is_cod',
        'expected_delivery_date',
    ];

    /* Relations */
    public function invoiceUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function OrderInvoice()
    {
        return $this->belongsTo(OrderItems::class);
    }
    public function productInvoice()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
