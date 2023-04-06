<?php

namespace App\Http\Controllers\V1;

use App\Models\Coupon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;


class CouponController extends Controller
{
    /**
     * API of Create coupon
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $coupon
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'title'         => 'nullable',
            'code'          => 'nullable',
            'value'         => 'required',
            'type'          => 'required|in:amount,percentage',
            'min_order_amt' => 'required',
            'is_one_time'   => 'required|boolean',
            'is_available'  => 'required|boolean',
            'expires_at'    => 'nullable|date'
        ]);
        $expires_at = Carbon::now()->addDays(20);
        $coupon = Coupon::create($request->only('title', 'value', 'type', 'min_order_amt', 'is_one_time', 'is_available') + ['code' => Str::random(6), 'expires_at' => $expires_at]);
        return ok('Coupon created successfully!', $coupon);
    }
    public function list()
    {
        $coupon = Coupon::get();
        return ok('Coupon list get successfully', $coupon);
    }
}
