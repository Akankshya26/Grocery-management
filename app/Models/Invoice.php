<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    /* Fillable */
    protected $fillable = [
        'user_id',
        'user_address_id',
        'product_id',
        'order_id',
        'order_num',
        'total_amount', //'pending' ,'done'
        'invoice_num',
        'payment_status',
    ];

    /* Relations */
    public function invoiceUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function OrderInvoice()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function productInvoice()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
