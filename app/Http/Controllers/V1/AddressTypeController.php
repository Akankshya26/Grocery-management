<?php

namespace App\Http\Controllers\V1;

use App\Models\AddressType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AddressTypeController extends Controller
{
    /**
     * API of List Address Type
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $addressType
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

        $query = AddressType::query();

        if ($request->search) {
            $query = $query->where('name', 'like', "%$request->search%");
        }

        if ($request->sort_field && $request->sort_order) {
            $query = $query->orderBy($request->sort_field, $request->sort_order);
        }

        /* Pagination */
        $count = $query->count();
        if ($request->page && $request->perPage) {
            $page    = $request->page;
            $perPage = $request->perPage;
            $query   = $query->skip($perPage * ($page - 1))->take($perPage);
        }

        /* Get records */
        $addressType = $query->get();

        $data = [
            'count'          => $count,
            'address Types'  => $addressType
        ];

        return ok('Address Type  list', $data);
    }
    /**
     * API of Create Address Type
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $addressType
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:address_types,name'
        ]);

        $addressType = AddressType::create($request->only('name'));

        return ok('Address Type created successfully!', $addressType);
    }

    /**
     * API of Update Address Type
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:address_types,name'
        ]);

        $addressType = AddressType::findOrFail($id);
        $addressType->update($request->only('name'));

        return ok('Address Type updated successfully!', $addressType);
    }
    /**
     * API of Delete Address Type
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        AddressType::findOrFail($id)->delete();

        return ok('Address Type deleted successfully');
    }
}
