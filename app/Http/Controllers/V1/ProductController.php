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
    public function list(Request $request)
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
        $product = $query->get();

        $data = [
            'count' => $count,
            'data'  => $product
        ];

        return ok(' Product  list', $data);
    }
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
            'image.*'          => 'required',
            'price'            => 'required|integer',
            'discount'         => 'required|integer',
            'is_emi_available' => 'required|boolean',
            'is_available'     => 'required|boolean',
            'manufactured_at'  => 'required|date',
            'expires_at'       => 'required|date',
            'tax'              => 'required|integer'
        ]);
        // dd($request->image);
        // dd($request->only('category_id', 'name'));
        $product = Product::create($request->only(
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
        // dd($product);
        $image = array();
        if ($request->hasFile('image')) {
            // dd($request->image);
            foreach ($request->image as $file) {
                $image_name =  str_replace(".", "", (string)microtime(true)) . '.' . $file->getClientOriginalExtension();
                $upload_path =  'images/' . $product->id;
                $file->storeAs($upload_path, $image_name);
                $images[] = [
                    'product_id' => $product->id,
                    'image_name' => $image_name,
                ];
            }
        }
        $product->img()->createMany($images);
        return ok('Product created successfully!', $product);
    }
    public function get($id)
    {
        $product = Product::with('img')->findOrFail($id);

        return ok('Category get successfully', $product);
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
            'name'             => 'required|unique:sub_categories,name',
            'image.*'          => 'required',
            'price'            => 'required|integer',
            'discount'         => 'required|integer',
            'is_emi_available' => 'required|boolean',
            'is_available'     => 'required|boolean',
            'manufactured_at'  => 'required|date',
            'expires_at'       => 'required|date',
            'tax'              => 'required|integer'
        ]);

        $product = Product::findOrFail($id);
        $product->updateOrCreate($request->only(
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
                $images[] = [
                    'product_id' => $product->id,
                    'image_name' => $image_name,
                ];
            }
        }
        $product->img()->createMany($images);
        return ok('product updated successfully!', $product);
    }
    /**
     * API of Delete product
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        Product::with('img')->findOrFail($id)->delete();

        return ok('Product iteam deleted successfully');
    }
}
