<?php

namespace App\Http\Controllers\V1;

use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class CartItemController extends Controller
{
    /**
     * API of List cart item
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $cartIteam
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

        $query = CartItem::query()->where('user_id', auth()->user()->id);

        /* Pagination */
        $count = $query->count();
        if ($request->page && $request->perPage) {
            $page    = $request->page;
            $perPage = $request->perPage;
            $query   = $query->skip($perPage * ($page - 1))->take($perPage);
        }

        /* Get records */
        $cartItem = $query->get();


        $sum = 0;
        foreach ($cartItem as $item) {
            if ($item->unit == 'kg' || $item->unit == 'L' || $item->unit == 'num') {
                $price = $item->productCart->price * $item->quantity;
            }
            if ($item->unit == 'g' || $item->unit == 'ml') {
                $PPG = $item->productCart->price / 1000; //PPG=price per gram
                $price = $PPG * $item->quantity;
            }
            $sum +=  $price;
        }
        $data = [
            'count'        => $count,
            'cart items'   => $cartItem,
            'Total_amount' =>  $sum
        ];

        return ok(' cart iteam  list', $data);
    }

    /**
     * API of Create cart items
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $cartIteam
     */
    public function create(Request $request)
    {
        $prod_check = Product::where('id', $request->product_id)->first();
        if (!$prod_check) {
            return ok('This product is not available');
        }
        if ($prod_check->quantity == 0) {
            return ok('This Product is out of stuck');
        }
        $prod_wishlist = Wishlist::where('product_id', $request->product_id)->where('user_id', auth()->user()->id)->first();
        if (!$prod_wishlist) {

            return ok('Your product is not available in wishlist');
        }
        $cart_check = CartItem::where('product_id', $request->product_id)->where('user_id', auth()->user()->id)->first();
        if ($cart_check) {
            return ok(' This product is already in cart');
        }
        $cartItem = new CartItem();
        $cartItem->product_id = $request->product_id;
        $cartItem->user_id = auth()->user()->id;
        $cartItem->quantity = $request->quantity;
        $cartItem->unit = $request->unit;
        $cartItem->save();
        /*Remove product from wishlist after addind in Cart*/
        $wishlist = Wishlist::where('product_id', $request->product_id)->where('user_id', auth()->user()->id)->get();
        Wishlist::destroy($wishlist);
        return ok('Product added to cart successfully', $cartItem);
    }

    /**
     * API of Update cart iteam
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'user_id'     => 'nullable',
            'product_id'  => 'nullable',
            'quantity'    => 'required|numeric',
            'unit'        => 'required|in:kg,g,num,L,ml'
        ]);

        $cart_check = CartItem::findOrfail($id);
        $cart_check->update($request->only('quantity', 'unit'));
        return ok('The cart item quantity is updated successfully');
    }

    /**
     * API of Delete cart iteam
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete(Request $request)
    {

        $cartItem = CartItem::where('product_id', $request->product_id)->where('user_id', auth()->user()->id)->first();
        if ($cartItem) {
            $cartItem->delete();
            return ok('product remove from cart succesfullly ');
        }
    }
}
