<?php

namespace App\Http\Controllers\V1;

use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

        $query = CartItem::query();

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
        $cartIteam = $query->get();
        $sum = CartItem::sum('amount');
        $data = [
            'count'        => $count,
            'cart items'   => $cartIteam,
            'Total_amount' => $sum
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
            'user_id'     => 'required|exists:users,id',
            'product_id'  => 'required|exists:Products,id',
            // 'amount'      => 'required|numeric',
            // 'discount'    => 'required|numeric|between:0,99.99',
            // 'tax'         => 'required|numeric|between:0,99.99',
            'quantity'    => 'required|max:10|numeric',
        ]);
        $product = Product::findOrFail($request->product_id);
        $total = (($product->price + $product->tax) - $product->discount) * $request->quantity;
        $cartIteam = CartItem::create($request->only('user_id', 'product_id', 'quantity')
            + ['amount' => $total]);


        return ok('cart iteam created successfully!', $cartIteam->load('productCart'));
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
            // 'amount'      => 'required|numeric',
            // 'discount'    => 'required|numeric',
            // 'tax'         => 'required|numeric',
            'quantity'    => 'required|numeric|max:10',
        ]);

        $cartIteam = CartItem::findOrFail($id);
        $cartIteam->update($request->only('user_id', 'product_id', 'amount', 'discount', 'tax', 'quantity'));

        return ok('cart iteam  updated successfully!', $cartIteam);
    }

    /**
     * API of Delete cart iteam
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        CartItem::findOrFail($id)->delete();

        return ok('cart iteam deleted successfully');
    }
}
