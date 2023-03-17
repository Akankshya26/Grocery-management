<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\OrderItems;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * API of Create Invoice
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $invoice
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'user_id'                => 'required|exists:users,id',
            'product_id'             => 'required|exists:Products,id',
            'order_item_id'          => 'required|exists:order_items,id',
            'total_amount'           => 'numeric',
            'payment_status'         => 'required|in:Pending,Done',
            'is_cod'                 => 'nullable|boolean',
            'expected_delivery_date' => 'required|date',
        ]);
        $item = OrderItems::findOrFail($request->order_item_id);
        $total = $item->amount;
        $tax = $item->tax;
        $discount = $item->discount;
        $quantity = $item->quantity;
        // dd($item->amount);

        $invoice = Invoice::create($request->only('user_id', 'product_id', 'order_item_id', 'payment_status', 'is_cod', 'expected_delivery_date')
            + ['total_amount' => $total, 'tax' => $tax, 'discount' => $discount, 'quantity' => $quantity]);

        return ok('order iteam created successfully!', $invoice);
    }
    /**
     * API of get perticuler invoice details
     *
     * @param  $id
     * @return $invoice
     */
    public function get($id)
    {
        $invoice = Invoice::with('invoiceUser', 'productInvoice')->findOrFail($id);

        return ok('invoice get successfully', $invoice);
    }
}
