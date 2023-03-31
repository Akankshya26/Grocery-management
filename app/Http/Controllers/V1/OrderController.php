<?php

namespace App\Http\Controllers\V1;

use PDF;
use App\Models\User;
use App\Models\Order;
use App\Mail\OrderMail;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\CartItem;
use App\Mail\InvoiceMail;
use App\Mail\OrderCancel;
use App\Models\OrderItems;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\UserAddress;
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
        //order Histoery
        $order = Order::where('user_id', auth()->user()->id)->with('OrderItm')->get();

        return ok('order history', $order);
    }

    public function partnerOrder()
    {
        $product = Product::where('created_by', Auth::id());

        $order = Order::where('user_id', $product->created_by)->first();
        dd($product);
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
            'user_address_id'        => 'required|exist:user_address,id',
            // 'order_num'              => 'exists:orders,order_num',
        ]);

        $user = auth()->user();
        $userAddress = UserAddress::findorfail($request->user_address_id);
        if ($userAddress->user_id != $user->id) {
            return error('This User Address is not belongs to the authenticated user');
        }
        $cartItem = CartItem::where('user_id', Auth::id());
        $delivery_date = Carbon::now()->addDays(5);
        if ($cartItem->count() == 0) {
            return error('Your cart is empty');
        } else {
            $order = Order::create($request->only('user_address_id', 'status') +
                ['order_num' => Str::random(6)] + ['user_id' => Auth::id()] +
                ['expected_delivery_date' => $delivery_date]);
            $cartItem = CartItem::where('user_id', Auth::id())->get();
            // $orderItems = [];
            // $orderItems = $order->OrderItm()->createmany($orderItems);
            foreach ($cartItem as $item) {
                $orderItems = OrderItems::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->productCart->price,
                ]);

                /*email send to partnrs*/
                $partner = $orderItems->prodOrder->created_by;
                $user = User::findOrFail($partner);
                Mail::to($user->email)->send(new OrderMail($orderItems, $user));
            }
        }
        /*Remove from cart after order placed*/
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

    /*Generate Invoice */
    public function downloadInvoice($id)
    {

        $order = Order::findOrFail($id);
        $item = $order->OrderItm()->get();
        $data = ['order' => $order, 'item' => $item];
        $pdf = PDF::loadView('mail_pdf', $data);
        return $pdf->download('invoice' . $order->id . "-" . now()->format('Y-m-d') . '.pdf');
    }

    /*Order accept api*/
    public function approve(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        if ($order->status == 'reject') {
        }
        if ($order->status == 'accept') {
            return ('This order is already accepted');
        } else {

            $order->update(['status' => 'accept']);
        }
        $cartItem = $order->OrderItm;
        $total = 0;
        foreach ($cartItem as $item) {
            $total += ($item->price)  * $item->quantity;
        }
        $invoice = Invoice::create([
            'user_id'         => $order->userOrder->id,
            'order_id'        => $order->id,
            'user_address_id' => $order->user_address_id,
            'order_num'       => $order->order_num,
            'total_amount'    => $total,
            'invoice_num'     => Str::random(6),
        ]);
        $productData = $invoice->OrderInvoice()->first()->OrderItm()->get();

        $email = $invoice->invoiceUser->email;

        Mail::to($email)->send(new InvoiceMail($invoice, $productData));

        return ok('The Order id accepted', $invoice);
    }

    /*Order decline api*/
    public function decline($id)
    {
        $order = Order::findOrFail($id);
        if ($order->status == 'accept') {
            return ('This order is already placed');
        }
        if ($order->status == 'reject') {
            return ('This order is already reject');
        } else {
            $order->update(['status' => 'reject']);
            $order->save();
            $email = $order->userOrder->email;
            $userName = $order->OrderItm->first()->prodOrder->created_by;
            $user = User::findorfail($userName);
            Mail::to($email)->send(new OrderCancel($order, $user));
            return ok('The Order id rejected');
        }
    }
    /*  Track order status with Order_num*/
    public function orderStatus($order_num)
    {
        $status = Order::where('order_num', $order_num)->first();
        return ok('The status Of the order is', $status->status);
    }

    /* Invoice list Of customer*/
    public function invoiceList()
    {
        $query = Invoice::get();
        return ok('Invoice List get successfully', $query);
    }
    /* Cancel order list*/
    public function cancelOrder()
    {
        $cancel = Order::where('status', 'reject')->get();
        return ok('Canceled order list get succesfully', $cancel);
    }
}
