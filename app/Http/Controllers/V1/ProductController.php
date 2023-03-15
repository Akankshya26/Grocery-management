<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * API of Create sub-category
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $product
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'category_id'      => 'required|exists:categories,id',
            'sub_category_id'  => 'required|exists:sub_categories,id',
            'name'             => 'required|unique:sub_categories,name',
            'price'            => 'required|integer',
            'discount'         => 'required|integer',
            'is_emi_available' => 'required|boolean',
            'is_available'     => 'required|boolean',
            'manufactured_at'  => 'required|date',
            'expires_at'       => 'required|date',
            'tax'              => 'required|integer'
        ]);
        // dd($request->only('category_id', 'name'));
        $subCategory = Product::create($request->only(
            'category_id',
            'sub_category_id',
            'name',
            'price',
            'discount',
            'discount',
            'is_emi_available',
            'is_available',
            'manufactured_at',
            'expires_at',
            'tax'
        ));

        return ok('Product created successfully!', $subCategory);
    }
}
