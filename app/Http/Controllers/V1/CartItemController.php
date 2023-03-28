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

        $query = CartItem::query()->where('user_id', Auth::id());

        if ($request->search) {
            $query = $query->where('name', 'like', "%$request->search%");
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
        $this->validate($request, [
            'product_id'  => 'required|exists:Products,id',
            'quantity'    => 'required|max:10|numeric',
        ]);

        $product_id = $request->input('product_id');
        $product_qty = $request->input('quantity');
        // if (Auth::check()) {
        $prod_check = Product::where('id', $product_id)->exists();
        if ($prod_check) {
            if (CartItem::where('product_id', $product_id)->where('user_id', Auth::id())->exists()) {
                return error(' This product is already in cart');
            } else {
                $cartIteam = new CartItem();
                $cartIteam->product_id = $product_id;
                $cartIteam->user_id = Auth::id();
                $cartIteam->quantity = $product_qty;
                $cartIteam->save();

                $wishlist = Wishlist::where('product_id', $product_id)->where('user_id', Auth::id())->get();
                Wishlist::destroy($wishlist);
                return ok('Product added to cart successfully', $cartIteam);
            }
        }
        // } else {
        //     return error('Continue with Login');
        // }
    }

    /**
     * API of get perticuler cart Item details
     *
     * @param  $id
     * @return $cartIteam
     */
    public function get($id)
    {
        $cartIteam = CartItem::with('userCart', 'productCart')->findOrFail($id);

        return ok('cart iteam get successfully', $cartIteam);
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
            'user_id'     => 'required|exists:users,id',
            'product_id'  => 'required|exists:Products,id',
            'quantity'    => 'required|numeric|max:10',
        ]);
        $product_id = $request->input('product_id');
        $product_qty = $request->input('quantity');


        if (CartItem::where('product_id', $product_id)->where('user_id', Auth::id())->exists()); {
            $cartIteam = CartItem::where('product_id', $product_id)->where('user_id', Auth::id())->first();
            $cartIteam->quantity =  $product_qty;
            $cartIteam->update();
            return ok('Qunatity Updated successfully', $cartIteam);
        }
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
        if (CartItem::where('product_id', $product_id)->where('user_id', Auth::id())->exists()); {
            $cartIteam = CartItem::where('product_id', $product_id)->where('user_id', Auth::id())->first();
            $cartIteam->delete();
            return ok('product remove from cart succesfullly ');
        }
    }
}
