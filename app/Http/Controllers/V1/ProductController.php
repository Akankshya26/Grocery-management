<?php

namespace App\Http\Controllers\V1;

use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\ProductRating;
use App\Http\Controllers\Controller;
use App\Models\ImageProduct;
use Illuminate\Support\Facades\Auth;
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
            'page'                => 'nullable|integer',
            'perPage'             => 'nullable|integer',
            'search'              => 'nullable',
            'sort_field'          => 'nullable',
            'sort_order'          => 'nullable|in:asc,desc',
            'category_id'         => 'exists:categories,id',
            'sub_category_id.*'   => 'exists:sub_categories,id',

        ]);
        $query = Product::query()->with('productRating.user');
        if ($query->quantity->count() == 0) {
            return ('Product is out of stock');
        }

        if ($request->category_id) {
            $query->whereHas('category', function ($query) use ($request) {
                $query->where('id', $request->category_id);
            });

            if ($request->sub_category_id && count($request->sub_category_id) > 0) {
                $query->whereHas('subCategory', function ($query) use ($request) {
                    $query->whereIn('id', $request->sub_category_id);
                });
            }
        }

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
            'count'     => $count,
            'products'  => $product,
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
            'quantity'          => 'required|integer',
            'is_emi_available' => 'required|boolean',
            'is_available'     => 'required|boolean',
            'manufactured_at'  => 'required|date',
            'expires_at'       => 'nullable',
            'tax'              => 'required|numeric|between:0,99.99'
        ]);
        // dd($request->all());
        $total = ($request->price + $request->tax) - $request->discount;
        $user = auth()->user();
        if (!$user->type == 'partner') {
            return ('You are Not allowed to add products');
        } else {
            $product = Product::create($request->only(
                'category_id',
                'sub_category_id',
                'name',
                'discount',
                'quantity',
                'is_emi_available',
                'is_available',
                'manufactured_at',
                'expires_at',
                'tax'
            ) + ['price' => $total]);
            $image = array();
            if ($request->hasFile('image')) {

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
        }
        $product->img()->createMany($image);
        return ok('Product created successfully!',  $product->load('img'));
    }
    public function get($id)
    {
        $product = Product::with('img', 'productRating')->findOrFail($id);
        if ($product->quantity->count() == 0) {
            return ('Product is out of stock');
        } else {

            return ok('product get successfully', $product);
        }
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
            'category_id'      => 'nullable|exists:categories,id',
            'sub_category_id'  => 'nullable|exists:sub_categories,id',
            'name'             => 'nullable',
            'image.*'          => 'nullable|mimes:jpeg,jpg,png,gif|max:10000',
            'price'            => 'nullable|integer',
            'discount'         => 'nullable|numeric|between:0,99.99',
            'quantity'          => 'nullable|integer',
            'is_emi_available' => 'nullable|boolean',
            'is_available'     => 'nullable|boolean',
            'manufactured_at'  => 'nullable|date',
            'expires_at'       => 'nullable|after:manufactured_at',
            'tax'              => 'nullable|numeric|between:0,99.99'
        ]);
        $total = ($request->price + $request->tax) - $request->discount;
        $image_name = array_column($request->image, 'image_name');
        $product = Product::findOrFail($id);
        $data = ImageProduct::where('product_id', $product->id)->whereNotIn('image_name',  $image_name);

        $product->update($request->only(
            'category_id',
            'sub_category_id',
            'name',
            'discount',
            'quantity',
            'is_emi_available',
            'is_available',
            'manufactured_at',
            'expires_at',
            'tax'
        ) + ['price' => $total]);
        $image = array();
        if ($request->hasFile('image')) {
            $upload_path =  'images/' . $product->id;
            foreach ($request->image as $file) {
                $image_name =  str_replace(".", "", (string)microtime(true)) . '.' . $file->getClientOriginalExtension();
                $file->storeAs($upload_path, $image_name);
                $image[] = [
                    'product_id' => $product->id,
                    'image_name' => $image_name,
                ];
            }
        }
        ImageProduct::updateOrCreate(
            [
                'product_id' => $product->id,
                'image_name' => $image_name,
            ]
        );
        return ok('product updated successfully!', $product->load('img'));
    }
    /**
     * API of Delete product with respective image
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


    public function partnerProduct()
    {
        $query = Product::query()->where('id', auth()->user()->id)->get();
        return ok('Your added Product list', $query);
    }
}
