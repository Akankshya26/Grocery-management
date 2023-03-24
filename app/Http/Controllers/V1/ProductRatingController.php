<?php

namespace App\Http\Controllers\V1;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductRating;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProductRatingController extends Controller
{

    public function create(Request $request)
    {
        $this->validate($request, [
            'product_id'    => 'required|exists:products,id',
            'rating'        => 'required|integer|max:5'
        ]);
        $rating     = $request->input('rating');
        $product_id = $request->input('product_id');
        $product_check = Product::where('id', $product_id)->first();
        if ($product_check) {
            $verified_purchaes = Order::where('orders.user_id', Auth::id())
                ->join('order_items', 'orders.id', 'order_items.order_id')
                ->where('order_items.product_id', $product_id)->get();
            if ($verified_purchaes) {
                dd($verified_purchaes);
                $existing_rating = ProductRating::where('user_id', Auth::id())->where('product_id', $product_id)->exists();
                if ($existing_rating) {
                    $existing_rating->rating = $rating;
                    $existing_rating->update();
                } else {
                    ProductRating::create([

                        'user_id' => Auth::id(),
                        'product_id' => $product_id,
                        'rating' => $rating,
                    ]);
                }
                return ok('Thank You For rating this product');
            } else {
                return error('You can not rate this product');
            }
        } else {
            return error('No such Product exists');
        }
    }
}
