<?php

namespace App\Http\Controllers\V1;

use PDF;
use App\Models\User;
use App\Models\Order;
use App\Mail\OrderMail;
use App\Models\Invoice;
use App\Models\CartItem;
use App\Mail\InvoiceMail;
use App\Mail\OrderCancel;
use App\Models\OrderItems;
use App\Models\UserAddress;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    protected $total_amount;
    /**
     * API of List orders
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $order
     */
    public function list(Request $request)
    {
        $this->validate($request, [
            'start_date' => 'date',
            'end_date'   => 'date|before_or_equal:start_date'
        ]);
        $order = Order::where('user_id', auth()->user()->id)->with('OrderItm');

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        if ($request->strt_date) {
            $order->whereDate('created_at', '<=', $end);
        }
        if ($request->end_date) {
            $order->whereDate('created_at', '<=', $start);
        }
        $order_history = $order->get();
        $count = $order_history->count();
        $data = [
            'count' => $count,
            'Order_history' => $order_history,
        ];
        return ok('order history', $data);
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
            'coupon_id'     => 'nullable',
            'canceled_date' => 'nullable'
        ]);
        $total = 0;
        $user = auth()->user();
        $userAddress = UserAddress::findOrFail($request->user_address_id);
        if ($userAddress->user_id != $user->id) {
            return ok('This User Address is not belongs to the authenticated user');
        }
        $cartItem = CartItem::where('user_id', auth()->user()->id)->get();
        foreach ($cartItem as $item) {
            if ($item->unit == 'kg' || $item->unit == 'L' || $item->unit == 'num') {
                $price = $item->productCart->price * $item->quantity;
            }
            if ($item->unit == 'g' || $item->unit == 'ml') {
                $PPG = $item->productCart->price / 1000; //PPG=price per gram
                $price = $PPG * $item->quantity;
            }
            $total +=  $price;
        }
        $delivery_date = Carbon::now()->addDays(5);
        if ($cartItem->count() == 0) {
            return ok('Your cart is empty');
        } else {
            //apply coupon on order place
            $this->total_amount =  $total;
            if ($request->coupon_id) {
                $checkCoupon = Coupon::where('id', $request->coupon_id)->first();
                if (!$checkCoupon) {
                    return ok('This coupon is not available');
                }
                $couponCheck = $checkCoupon->couponOrder->where('coupon_id', $request->coupon_id)
                    ->where('user_id', auth()->user()->id)->first();
                if ($couponCheck) {
                    return ok('This coupon is alredy used');
                }
                if ($total < $checkCoupon->min_order_amt) {
                    return ok('You are not aligible to apply this coupon');
                }
                if ($checkCoupon->type == 'amount') {
                    $this->total_amount = $total - $checkCoupon->value;
                }
                if ($checkCoupon->type == 'percentage') {
                    $couponPrice = $checkCoupon->value / 100 * $total;
                    $this->total_amount = $total - $couponPrice;
                }
            }

            $order = Order::create($request->only('user_address_id', 'status', 'coupon_id', 'canceled_date') +
                ['order_num' => Str::random(6)] + ['user_id' => auth()->user()->id] +
                ['expected_delivery_date' => $delivery_date] + ['total_amount' => $this->total_amount]);

            $cartItem = CartItem::where('user_id',  auth()->user()->id)->get();
            foreach ($cartItem as $item) {
                if ($item->unit == 'kg' || $item->unit == 'L' || $item->unit == 'num') {
                    $price = $item->productCart->price * $item->quantity;
                }
                if ($item->unit == 'g' || $item->unit == 'ml') {
                    $PPG = $item->productCart->price / 1000; //PPG=price per gram
                    $price = $PPG * $item->quantity;
                }
                $orderItems = OrderItems::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'unit'       => $item->unit,
                    'price'      => $price,
                ]);

                /*email send to partnrs*/
                // $partner = $orderItems->prodOrder->created_by;
                // $user = User::findOrFail($partner);
                // Mail::to($user->email)->send(new OrderMail($orderItems, $user));

                /*Deduct product quantity from product table with order placed*/
                $product_qty = $orderItems->prodOrder->quantity;
                $order_qty = $orderItems->quantity;
                $gram = $product_qty  * 1000;
                if ($item->unit == 'g' || $item->unit == 'ml') {
                    $deduct_qyt = ($gram - $order_qty) / 1000;
                    $orderItems->prodOrder->update(['quantity' => $deduct_qyt]);
                }
                if ($item->unit == 'kg' || $item->unit == 'L' || $item->unit == 'num') {
                    $deduct_qyt =  $product_qty - $order_qty;
                    $orderItems->prodOrder->update(['quantity' => $deduct_qyt]);
                }
            }
        }
        /*Remove from cart after order placed*/
        $cartItem = CartItem::where('user_id',  auth()->user()->id)->get();
        CartItem::destroy($cartItem);

        $data = [
            'cart items'      =>  $cartItem,
            'order details'   => $order,
            'total amount'    => $this->total_amount

        ];
        return ok('order placed successfully', $data);
    }

    /*Download Invoice */
    public function downloadInvoice($id)
    {
        $order = Order::findOrFail($id);
        $item = $order->OrderItm()->get();
        // dd($item->price);
        $data = ['order' => $order, 'item' => $item];
        $pdf = PDF::loadView('mail_pdf', $data);
        return $pdf->download('invoice' . $order->id . "-" . now()->format('Y-m-d') . '.pdf');
    }

    /*Order accept api*/
    public function approve($id)
    {
        $order = Order::findOrFail($id);
        if ($order->status == 'reject') {
        }
        if ($order->status == 'accept') {
            return ('This order is already accepted');
        } else {

            $order->update(['status' => 'accept']);
        }
        $total = $order->total_amount;

        $invoice = Invoice::create([
            'user_id'         => $order->user_id,
            'order_id'        => $order->id,
            'user_address_id' => $order->user_address_id,
            'order_num'       => $order->order_num,
            'total_amount'    => $total,
            'invoice_num'     => Str::random(6),
        ]);
        $productData = $invoice->OrderInvoice()->first()->OrderItm()->get();

        $email = $invoice->invoiceUser->email;

        // Mail::to($email)->send(new InvoiceMail($invoice, $productData));

        return ok('The Order id accepted', $invoice);
    }

    /*Order decline api*/
    public function decline($id)
    {
        $order = Order::findOrFail($id);
        $item = $order->OrderItm()->get();
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
            Mail::to($email)->send(new OrderCancel($item, $user, $order));
            return ok('The Order id rejected');
        }
    }
    /*  Track order status with Order_num of customer*/
    public function orderStatus($order_num)
    {
        $status = Order::where('order_num', $order_num)->first();
        return ok('The status Of the order is', $status->status);
    }

    /* Invoice list Of customer*/
    public function invoiceList(Request $request)
    {
        $this->validate($request, [
            'start_date' => 'date',
            'end_date'   => 'date|before_or_equal:start_date'
        ]);
        $user = auth()->user();
        $get_invoice = Invoice::where('user_id', $user->id)->with('OrderItm');

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        if ($request->strt_date) {
            $get_invoice->whereDate('created_at', '<=', $end);
        }
        if ($request->end_date) {
            $get_invoice->whereDate('created_at', '<=', $start);
        }
        $order_history = $get_invoice->get();
        return ok('Invoice List get successfully',  $order_history);
    }

    /* Cancel order list*/
    public function cancelOrder(Request $request)
    {
        $this->validate($request, [
            'start_date' => 'date',
            'end_date'   => 'date|before_or_equal:start_date'
        ]);
        $cancel_order = Order::where('status', 'reject');

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        if ($request->strt_date) {
            $cancel_order->whereDate('created_at', '<=', $end);
        }
        if ($request->end_date) {
            $cancel_order->whereDate('created_at', '<=', $start);
        }
        $cancel_order_list = $cancel_order->get();
        return ok('Canceled order list get succesfully',    $cancel_order_list);
    }

    //Order canceled by customer
    public function customerCancelOrder(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'exists:orders,id'
        ]);
        $currentDate = now()->format('Y-m-d');
        $order_id = $request->order_id;
        $order = Order::where('id', $order_id)->where('user_id', auth()->user()->id)->first();
        if ($order->status == 'canceled') {
            return ok('Your order is already canceled', $order);
        }
        if ($order->expected_delivery_date == $currentDate) {
            return ok('Your order can not be canceled..its not possible!!');
        } else {
            $order->status = "canceled";
            $order->canceled_date = DB::raw('CURRENT_DATE');
            $order->save();
            return ok('Your order is canceled successfully', $order);
        }
    }
    //admin change the delivery date
    public function updateStatus(Request $request)
    {
        $current_day = Carbon::now();
        $order = Order::findOrFail($request->order_id);
        $status = $order->expected_delivery_date;
        if (!$status == $current_day) {
            return ok('Your Product is On the way!!');
        }
        $order->update(['status' => 'out of delivery ']);
        return ok('Order Status Updated Sucessfully', $order);
    }
}
