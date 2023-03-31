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
            $page = $request->page;
            $perPage = $request->perPage;
            $query = $query->skip($perPage * ($page - 1))->take($perPage);
        }

        /* Get records */
        $cartItem = $query->get();

        $total = 0;
        foreach ($cartItem as $item) {
            $total += ($item->productCart->price)  * $item->quantity;
        }
        $data = [
            'count'        => $count,
            'cart items'   => $cartItem,
            'Total_amount' => $total
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
        $product_id = $request->input('product_id');
        $prod_check = Product::where('id', $product_id)->first();
        if (!$prod_check) {
            return error('This product is not available', [], 'notfound');
        }

        $prod_wishlist = Wishlist::where('product_id', $product_id)->where('user_id', auth()->user()->id)->first();
        if (!$prod_wishlist) {

            return error('Your product is not available in wishlist', [], 'notfound');
        }
        $cart_check = CartItem::where('product_id', $product_id)->where('user_id', auth()->user()->id)->first();
        if ($cart_check) {
            return error(' This product is already in cart', [], 'forbidden');
        }
        $cartIteam = new CartItem();
        $cartIteam->product_id = $product_id;
        $cartIteam->user_id = auth()->user()->id;
        $cartIteam->quantity = 1;
        $cartIteam->save();
        /*Remove product from wishlist after addind in Cart*/
        $wishlist = Wishlist::where('product_id', $product_id)->where('user_id', auth()->user()->id)->get();
        Wishlist::destroy($wishlist);
        return ok('Product added to cart successfully', $cartIteam);
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
            'quantity'    => 'required|numeric|max:10',
        ]);

        $cart_check = CartItem::findOrfail($id);
        $cart_check->update($request->only('quantity'));
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

        $product_id = $request->input('product_id');
        $cartIteam = CartItem::where('product_id', $product_id)->where('user_id', auth()->user()->id)->first();
        if ($cartIteam) {
            $cartIteam->delete();
            return ok('product remove from cart succesfullly ');
        }
    }
}
