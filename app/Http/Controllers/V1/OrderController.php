<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\CartItem;
use App\Mail\InvoiceMail;
use App\Models\OrderItems;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\OrderMail;
use PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
        // $old_cartItem = CartItem::where('user_id', Auth::id());
        // foreach ($old_cartItem as $item) {
        //     if (!Product::where('id', $item->product_id)->where('quantity', '>=', $item->quantity)->exists()) {
        //         $removeItem = CartItem::where('user_id', Auth::id())->where('product_id', $item->product_id)->first();
        //         $removeItem->delete();
        //     }
        // }
        // $cartItem = CartItem::where('user_id', Auth::id())->get();


        //order Histoery
        $order = Order::where('user_id', Auth::id())->with('OrderItm')->get();

        return ok('order history', $order);
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
            'user_address_id'         => 'required',
            'status'                  => 'nullable|in:pending, accept, reject, out of delivery, delivered',
            'expected_delivery_date'  => 'required',
        ]);

        $cartItem = CartItem::where('user_id', Auth::id());

        // dd($cartItem->user_id);
        if ($cartItem->count() == 0) {
            return error('Your cart is empty');
        } else {
            $order = Order::create($request->only('user_address_id', 'status', 'expected_delivery_date') + ['order_num' => Str::random(6)] + ['user_id' => Auth::id()]);
            $cartItem = CartItem::where('user_id', Auth::id())->get();
            foreach ($cartItem as $item) {
                $orderItems = OrderItems::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->productCart->price,

                ]);

                $partner = $orderItems->prodOrder->created_by;
                $user = User::findOrFail($partner);
                Mail::to($user->email)->send(new OrderMail($orderItems));
            }
        }
        if (Auth::user()->address1 == NULL) {
            // dd($request->all());
            $user = User::where('id', Auth::id())->first();
            Auth::user()->update($request->only('address1',  'address2', 'city', 'state', 'country', 'pincode'));
        }

        $cartItem = CartItem::where('user_id', Auth::id())->get();
        CartItem::destroy($cartItem);

        $total = 0;
        foreach ($cartItem as $item) {
            $total += ($item->productCart->price)  * $item->quantity;
        }

        // dd($orderItems->product_id);
        $invoice = Invoice::create([
            'user_id'         => Auth::id(),
            'order_id'        => $order->id,
            'user_address_id' => $order->user_address_id,
            'order_num'       => $order->order_num,
            'total_amount'    => $total,
            'invoice_num'     => Str::random(6),
        ]);
        $productData = $invoice->OrderInvoice()->first()->OrderItm()->get();
        // dd($productData->prodOrder);

        $email = $invoice->invoiceUser->email;

        Mail::to($email)->send(new InvoiceMail($invoice, $productData));

        $data = [
            'cart items' =>  $cartItem,
            'order details' => $order,
            'total amount' => $total

        ];
        return ok('order placed successfully', $data);
    }
    public function Invoice($id)
    {
        $order = Order::findOrFail($id);
        $data = ['order' => $order];
        $pdf = PDF::loadView('mail_pdf', $data);
        return $pdf->download('invoice' . $order->id . '.pdf');
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
