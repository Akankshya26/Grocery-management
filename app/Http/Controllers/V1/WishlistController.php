<?php

namespace App\Http\Controllers\V1;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * API of List Wishlist
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $wishlist
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

        $query = Wishlist::query()->where('user_id', auth()->user()->id);

        /* Pagination */
        $count = $query->count();
        if ($request->page && $request->perPage) {
            $page = $request->page;
            $perPage = $request->perPage;
            $query = $query->skip($perPage * ($page - 1))->take($perPage);
        }

        /* Get records */
        $wishlist = $query->get();

        $data = [
            'count'      => $count,
            'wishlists'  => $wishlist
        ];

        return ok(' User Wishlist list', $data);
    }

    /**
     * API of Create Wishlist
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $wishlist
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|exists:products,id'
        ]);
        $product_id = $request->input('product_id');

        $prod_check = Product::where('id', $product_id)->exists();
        if ($prod_check) {
            if (Wishlist::where('product_id', $product_id)->where('user_id', auth()->user()->id)->exists()) {
                return error(' This product is already in wishlist');
            } else {
                $wish = Wishlist::create($request->only('product_id') + ['user_id' => auth()->user()->id]);
                return ok('Product added to wishlist successfully', $wish);
            }
        }
    }
    /**
     * API of Delete wishlist
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete(Request $request)
    {

        $this->validate($request, [
            'product_id' => 'required|exists:products,id'
        ]);
        $wishlist = Wishlist::where('product_id', $request->product_id)->where('user_id', auth()->user()->id)->first();

        if (!$wishlist) {
            return error('Not found');
        }
        $wishlist->delete();
        return ok('Product removed from wishlist successfully');
    }
}
