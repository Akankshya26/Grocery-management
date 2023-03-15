<?php

namespace App\Http\Controllers\V1;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImageController extends Controller
{
    public function create(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $image = array();
        if ($files = $request->file('image')) {
            foreach ($files as $file) {
                $image_name =  str_replace(".", "", (string)microtime(true)) . '.' . $file->getClientOriginalExtension();
                $upload_path = 'public/images/';
                $file->move($upload_path, $image_name);
                $image[] = [
                    'product_id' => $product->id,
                    'image_name' => $image_name,
                ];
            }
        }
        $product->img()->createMany($image);
        return ok('Image added successfully!', $product);
    }
}
