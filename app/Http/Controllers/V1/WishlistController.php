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

        $query = Wishlist::query()->where('user_id', Auth::id());

        if ($request->search) {
            $query = $query->where('user_id', 'like', "%$request->search%");
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
        if (Auth::check()) {
            $prod_check = Product::where('id', $product_id)->exists();
            if ($prod_check) {
                if (Wishlist::where('product_id', $product_id)->where('user_id', Auth::id())->exists()) {
                    return error(' This product is already in wishlist');
                } else {
                    $wish = Wishlist::create($request->only('product_id') + ['user_id' => Auth::id()]);
                    return ok('Product added to wishlist successfully', $wish);
                }
            }
        } else {
            return error('Continue with Login');
        }
    }
    /**
     * API of Update wishlist
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'user_id'    => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id'
        ]);

        $wishlist = Wishlist::findOrFail($id);
        $wishlist->update($request->only('user_id', 'product_id'));

        return ok('wishlist updated successfully!', $wishlist);
    }

    /**
     * API of Delete wishlist
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete(Request $request)
    {

        if (Auth::check()) {
            $product_id = $request->input('product_id'); {
                if (Wishlist::where('product_id', $product_id)->where('user_id', Auth::id())->exists()) {

                    $wish = Wishlist::where('product_id', $product_id)->where('user_id', Auth::id())->first();
                    $wish->delete();
                    return ok('Product removed from wishlist successfully');
                }
            }
        } else {
            return error('Continue with Login');
        }
    }
}
