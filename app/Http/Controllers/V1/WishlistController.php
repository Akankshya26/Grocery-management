<?php

namespace App\Http\Controllers\V1;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

        $query = Wishlist::query();

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
            'count' => $count,
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
            'user_id'    => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id'
        ]);
        // dd($request->only('category_id', 'name'));
        $wishlist = Wishlist::create($request->only('user_id', 'product_id'));

        return ok('wishlist created successfully!', $wishlist);
    }

    /**
     * API of get perticuler user Wishlist details
     *
     * @param  $id
     * @return $wishlist
     */
    public function get($id)
    {
        $wishlist = Wishlist::with('userWishlist', 'productWishlist')->findOrFail($id);

        return ok('wishlist get successfully', $wishlist);
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
    public function delete($id)
    {
        Wishlist::findOrFail($id)->delete();

        return ok('wishlist deleted successfully');
    }
}
