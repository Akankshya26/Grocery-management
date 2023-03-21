<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductRating;

class ProductRatingController extends Controller
{
    /**
     * API of List Product Rating
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $productRating
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

        $query = ProductRating::query();

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
        $productRating = $query->get();

        $data = [
            'count' => $count,
            'product Ratings'  => $productRating
        ];

        return ok(' Product Rating  list', $data);
    }
    public function create(Request $request)
    {
        $this->validate($request, [
            'product_id'    => 'required|exists:products,id',
            'user_id'       => 'required|exists:users,id',
            'rating'        => 'required|integer|max:5'
        ]);
        $productRating = ProductRating::create($request->only('product_id', 'user_id', 'rating'));

        return ok('product Rating  created successfully!', $productRating);
    }
    /**
     * API of get perticuler product Rating details
     *
     * @param  $id
     * @return $productRating
     */
    public function get($id)
    {
        $productRating = ProductRating::with('user')->findOrFail($id);

        return ok('product Rating get successfully', $productRating);
    }
    /**
     * API of Update product Rating
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'product_id'    => 'required|exists:products,id',
            'user_id'       => 'required|exists:users,id',
            'rating'        => 'required|integer|max:5'
        ]);

        $productRating = ProductRating::findOrFail($id);
        $productRating->update($request->only('product_id', 'user_id', 'rating'));

        return ok('product Rating  updated successfully!', $productRating);
    }
    /**
     * API of Delete Product Rating
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        ProductRating::findOrFail($id)->delete();

        return ok('product Rating deleted successfully');
    }
}
