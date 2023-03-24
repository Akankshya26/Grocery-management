<?php

namespace App\Http\Controllers\V1;

use App\Models\Product;
use App\Models\OrderItems;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderItemsController extends Controller
{
    /**
     * API of List order item
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $orderItem
     */
    public function list(Request $request)
    {
        $this->validate($request, [
            'page'          => 'nullable|integer',
            'perPage'       => 'nullable|integer',
            'search'        => 'nullable',
            'sort_field'    => 'nullable',
            'sort_order'    => 'nullable|in:asc,desc',
        ]);

        $query = OrderItems::query();

        if ($request->search) {
            $query = $query->where('order_id', 'like', "%$request->search%");
        }

        if ($request->sort_field || $request->sort_order) {
            $query = $query->orderBy($request->sort_field, $request->sort_order);
        }

        /* Pagination */
        $count = $query->count();
        if ($request->page && $request->perPage) {
            $page = $request->page;
            $perPage = $request->perPage;
            $query = $query->skip($perPage * ($page - 1))->take($perPage);
        }

        /* Get records */
        $orderItem = $query->get();

        $data = [
            'count'        => $count,
            'order Items'  => $orderItem
        ];

        return ok(' Order iteam  list', $data);
    }
    /**
     * API of Create order items
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $orderIteam
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'order_id'     => 'required|exists:orders,id',
            'product_id'  => 'required|exists:Products,id',
            'quantity'    => 'required|numeric',
            'is_gift'     => 'nullable|boolean'
        ]);
        $product = Product::findOrFail($request->product_id);
        $total = (($product->price + $product->tax) - $product->discount) * $request->quantity;

        $orderIteam = OrderItems::create($request->only('order_id', 'product_id',  'quantity', 'is_gift') + ['amount' => $total]);

        return ok('order iteam created successfully!', $orderIteam->load('prodOrder'));
    }
    /**
     * API of get perticuler order item details
     *
     * @param  $id
     * @return $orderIteam
     */
    public function get($id)
    {
        $orderIteam = OrderItems::with('orderItems', 'prodOrder')->findOrFail($id);

        return ok('Order iteam get successfully', $orderIteam);
    }
    /**
     * API of Update order iteam
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'order_id'     => 'required|exists:orders,id',
            'product_id'  => 'required|exists:Products,id',
            'amount'      => 'required|numeric',
            'discount'    => 'required|numeric|between:0,99.99',
            'tax'         => 'required|numeric|between:0,99.99',
            'quantity'    => 'required|numeric',
            'is_gift'     => 'nullable|boolean'
        ]);

        $orderIteam = OrderItems::findOrFail($id);
        $product = Product::findOrFail($request->product_id);
        $total = (($product->price + $request->tax) - $request->discount) * $request->quantity;

        $orderIteam->update($request->only('order_id', 'product_id', 'discount', 'tax', 'quantity', 'is_gift') + ['amount' => $total]);

        return ok('Order iteam  updated successfully!', $orderIteam);
    }
    /**
     * API of Delete Order iteam
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        OrderItems::findOrFail($id)->delete();

        return ok('Order iteam deleted successfully');
    }
}
