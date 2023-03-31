<?php

namespace App\Http\Controllers\V1;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubCategoryController extends Controller
{
    /**
     * API of List sub_category
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $subCategory
     */
    public function list(Request $request, $id)
    {
        $this->validate($request, [
            'page'          => 'nullable|integer',
            'perPage'       => 'nullable|integer',
            'search'        => 'nullable',
            'sort_field'    => 'nullable',
            'sort_order'    => 'nullable|in:asc,desc',
        ]);

        /* Get records of perticular category*/
        $query = Category::where('id', $id)->with('subCategory');

        // $query = SubCategory::query()->where('category_id', $category->id);

        if ($request->search) {
            $query = $query->where('name', 'like', "%$request->search%");
        }

        if ($request->sort_field && $request->sort_order) {
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
        $subCategory = $query->get();


        $data = [
            'count'           => $count,
            'sub Categories'  => $subCategory
        ];

        return ok('sub category  list', $data);
    }
    /**
     * API of Create sub-category
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $subCategory
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'category_id'    => 'required|exists:categories,id',
            'name'           => 'required|unique:sub_categories,name',
            'slug'           => 'required'
        ]);
        // dd($request->only('category_id', 'name'));
        $subCategory = SubCategory::create($request->only('category_id', 'name', 'slug'));

        return ok('Sub Category created successfully!', $subCategory);
    }
    /**
     * API of get perticuler sub Category details
     *
     * @param  $id
     * @return $subCategory
     */
    public function get($id)
    {
        $subCategory = SubCategory::findOrFail($id);

        return ok('sub category get successfully', $subCategory);
    }
    /**
     * API of Update sub category
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'category_id'    => 'required|exists:categories,id',
            'name'           => 'required|unique:sub_categories,name',
            'slug'           => 'required'
        ]);

        $subCategory = SubCategory::findOrFail($id);
        $subCategory->update($request->only('category_id', 'name', 'slug'));

        return ok('Sub category updated successfully!', $subCategory);
    }
    /**
     * API of Delete Sub Category
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        $subCategory = SubCategory::findOrFail($id);
        if ($subCategory->subProd()->count() > 0) {
            $subCategory->subProd()->delete();
        }
        $subCategory->delete();
        return ok('SubCategory deleted successfully');
    }
}
