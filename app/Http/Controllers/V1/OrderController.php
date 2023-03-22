<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\OrderItems;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * API of List orders
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $order
     */
    public function list()
    {
        $old_cartItem = CartItem::where('user_id', Auth::id());
        foreach ($old_cartItem as $item) {
            if (!Product::where('id', $item->product_id)->where('quantity', '>=', $item->quantity)->exists()) {
                $removeItem = CartItem::where('user_id', Auth::id())->where('product_id', $item->product_id)->first();
                $removeItem->delete();
            }
        }
        $cartItem = CartItem::where('user_id', Auth::id())->get();
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
            'fname'     => 'required',
            'lname'     => 'required',
            'email'     => 'required',
            'phone'     => 'required',
            'address1'  => 'required',
            'address2'  => 'nullable',
            'state'     => 'required',
            'city'      => 'required',
            'country'   => 'required',
            'pincode'   => 'required|max:6',
        ]);

        $order = Order::create($request->only('fname', 'lname', 'email', 'phone', 'address1',  'address2', 'city', 'state', 'country', 'pincode'));
        $cartItem = CartItem::where('user_id', Auth::id())->get();
        foreach ($cartItem as $item) {
            OrderItems::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->productCart->price,

            ]);
        }
        if (Auth::user()->address1 == NULL) {
            $user = User::where('id', Auth::id())->first();
            $user->update($request->only('address1',  'address2', 'city', 'state', 'country', 'pincode'));
        }

        $cartItem = CartItem::where('user_id', Auth::id())->get();
        CartItem::destroy($cartItem);

        $total = 0;
        foreach ($cartItem as $item) {
            $total += ($item->productCart->price)  * $item->quantity;
        }
        $data = [
            'cart items' =>  $cartItem,
            'order details' => $order,
            'total amount' => $total

        ];
        return ok('order placed successfully', $data);
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
        $order = Order::findOrFail($id);
        $order->update($request->only('user_id', 'product_id', 'user_address_id', 'status', 'is_cod', 'is_placed', 'expected_delivery_date', 'delivery_date'));

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
            'status'    => 'required|in:Dispached,in_transit,Delivered',
        ]);

        $order = Order::findOrFail($id);
        $order->update($request->only('status'));

        return ok('status updated successfully!', $order);
    }
}
