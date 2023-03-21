<?php

namespace App\Http\Controllers\V1;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CartItem;

class OrderController extends Controller
{
    /**
     * API of List orders
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $order
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

        $query = Order::query();

        if ($request->search) {
            $query = $query->where('product_id', 'like', "%$request->search%");
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
        $order = $query->get();

        $data = [
            'count'   => $count,
            'orders'  => $order
        ];

        return ok(' order  list', $data);
    }
    /**
     * API of Create order
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $order
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'user_id'                => 'required|exists:users,id',
            'product_id'             => 'required|exists:products,id',
            'cart_item_id'           => 'required|exists:cart_items,id',
            'user_address_id'        => 'required|exists:user_addresses,id',
            // 'status'                 => 'required|in:Pending,Dispached,in_transit,Delivered',
            'is_cod'                 => 'nullable|boolean',
            'is_placed'              => 'nullable|boolean',
            'expected_delivery_date' => 'required|date',
            'delivery_date'          => 'required|date'

        ]);
        $cart_items = CartItem::findOrFail($request->cart_item_id);
        // dd($cart_items->product_id);
        if ($request->is_placed == 1) {
            if ($request->product_id == $cart_items->product_id) {
                $cart_items->delete();
            }
        }

        $order = Order::create($request->only('user_id', 'product_id', 'cart_item_id', 'user_address_id',  'is_cod', 'is_placed', 'expected_delivery_date', 'delivery_date'));

        return ok('order created successfully!', $order);
    }

    /**
     * API of get perticuler order details
     *
     * @param  $id
     * @return $order
     */
    public function get($id)
    {
        $order = Order::with('userOrder', 'productOrder')->findOrFail($id);

        return ok('order get successfully', $order);
    }
    /**
     * API of Update order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'user_id'                => 'required|exists:users,id',
            'product_id'             => 'required|exists:products,id',
            'user_address_id'        => 'required|exists:user_addresses,id',
            'status'                 => 'required|in:Pending,Dispached,in_transit,Delivered',
            'is_cod'                 => 'nullable|boolean',
            'expected_delivery_date' => 'required|date_format:m/d/Y',
            'delivery_date'          => 'required|date_format:m/d/Y'

        ]);

        $order = Order::findOrFail($id);
        $order->update($request->only('user_id', 'product_id', 'user_address_id', 'status', 'is_cod', 'expected_delivery_date', 'delivery_date'));

        return ok('order updated successfully!', $order);
    }
    /**
     * API of Delete Order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        Order::findOrFail($id)->delete();

        return ok('Order deleted successfully');
    }

    /**
     * API of Update order status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function statusUpdate(Request $request, $id)
    {
        $this->validate($request, [
            'status'                 => 'required|in:Dispached,in_transit,Delivered',
        ]);

        $order = Order::findOrFail($id);
        $order->update($request->only('status'));

        return ok('status updated successfully!', $order);
    }
}
