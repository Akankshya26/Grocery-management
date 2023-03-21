<?php

namespace App\Http\Controllers\V1;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * API of List Product
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $product
     */
    public function list(Request $request, $sub_category_id)
    {
        $this->validate($request, [
            'page'          => 'nullable|integer',
            'perPage'       => 'nullable|integer',
            'search'        => 'nullable',
            'sort_field'    => 'nullable',
            'sort_order'    => 'nullable|in:asc,desc',
        ]);

        $query = Product::query();

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
        // $product = $query->get();
        $product = Product::where('sub_category_id', $sub_category_id)->with('subProd')->get();


        $data = [
            'count' => $count,
            'products'  => $product
        ];

        return ok(' Product  list', $data);
    }
    /**
     * API of Create product
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $product
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'category_id'      => 'required|exists:categories,id',
            'sub_category_id'  => 'required|exists:sub_categories,id',
            'name'             => 'required|unique:products,name',
            'image.*'          => 'required|mimes:jpeg,jpg,png,gif|max:10000',
            'price'            => 'required|integer',
            'discount'         => 'required|numeric|between:0,99.99',
            'is_emi_available' => 'required|boolean',
            'is_available'     => 'required|boolean',
            'manufactured_at'  => 'required|date',
            'expires_at'       => 'required|after:manufactured_at',
            'tax'              => 'required|numeric|between:0,99.99'
        ]);
        // dd($request->all());
        $total = ($request->price + $request->tax) - $request->discount;
        $product = Product::create($request->only(
            'category_id',
            'sub_category_id',
            'name',
            'discount',
            'is_emi_available',
            'is_available',
            'manufactured_at',
            'expires_at',
            'tax'
        ) + ['price' => $total]);
        $image = array();
        if ($request->hasFile('image')) {

            // dd($request->image);
            foreach ($request->image as $file) {
                $image_name =  str_replace(".", "", (string)microtime(true)) . '.' . $file->getClientOriginalExtension();
                $upload_path =  'images/' . $product->id;
                $file->storeAs($upload_path, $image_name);
                $image[] = [
                    'product_id' => $product->id,
                    'image_name' => $image_name,
                ];
            }
        }
        $product->img()->createMany($image);
        // dd($product->img()->createMany($image));
        return ok('Product created successfully!',  $product->load('img'));
    }
    public function get($id)
    {
        $product = Product::with('img')->findOrFail($id);

        return ok('product get successfully', $product);
    }
    /**
     * API of Update product
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'category_id'      => 'required|exists:categories,id',
            'sub_category_id'  => 'required|exists:sub_categories,id',
            'name'             => 'required|unique:products,name',
            'image.*'          => 'required|mimes:jpeg,jpg,png,gif|max:10000',
            'price'            => 'required|integer',
            'discount'         => 'required|numeric|between:0,99.99',
            'is_emi_available' => 'required|boolean',
            'is_available'     => 'required|boolean',
            'manufactured_at'  => 'required|date',
            'expires_at'       => 'required|after:manufactured_at',
            'tax'              => 'required|numeric|between:0,99.99'
        ]);

        $total = ($request->price + $request->tax) - $request->discount;
        $product = Product::findOrFail($id);
        $product->update($request->only(
            'category_id',
            'sub_category_id',
            'name',
            'discount',
            'is_emi_available',
            'is_available',
            'manufactured_at',
            'expires_at',
            'tax'
        ) + ['price' => $total]);
        // dd($product);
        $image = array();
        if ($request->hasFile('image')) {
            $upload_path =  'images/' . $product->id;
            Storage::deleteDirectory($upload_path);
            $product->img()->delete();
            // dd($request->image);
            foreach ($request->image as $file) {
                $image_name =  str_replace(".", "", (string)microtime(true)) . '.' . $file->getClientOriginalExtension();
                // $upload_path =  'images/' . $product->id;
                $file->storeAs($upload_path, $image_name);
                $image[] = [
                    'product_id' => $product->id,
                    'image_name' => $image_name,
                ];
            }
        }
        $product->img()->createMany($image);
        return ok('product updated successfully!', $product->load('img'));
    }
    /**
     * API of Delete product
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->img()->delete();
        $product->delete();

        return ok('Product  deleted successfully');
    }
}
