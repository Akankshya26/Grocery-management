<?php

namespace App\Http\Controllers\V1;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserAddressController extends Controller
{
    /**
     * API of List user address list
     *@param  \Illuminate\Http\Request  $request
     *@return $userAddress
     */
    public function list(Request $request)
    {
        $query = UserAddress::query()->where('user_id', auth()->user()->id);


        /* Pagination */
        $count = $query->count();
        /* Get records */
        $userAddress = $query->get();

        $data = [
            'count'           => $count,
            'user addresses'  => $userAddress
        ];

        return ok(' user address  list', $data);
    }
    /**
     * API of Create User Address
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $userAddress
     */
    public function create(Request $request)
    {
        $this->validate($request, [

            'address_type_id' => 'required|exists:address_types,id',
            'address1'        => 'required|string|max:50',
            'address2'        => 'required|string|max:50',
            'zip_code'        => 'nullable|integer',
            'is_primary'      => 'nullable|boolean'
        ]);

        $userAddress = auth()->user()->UserAddress()->create($request->only('address_type_id', 'address1', 'address2', 'zip_code', 'is_primary'));

        return ok('User Address created successfully!', $userAddress);
    }
    /**
     * API of get perticuler user Address details
     *
     * @param  $id
     * @return $productRating
     */
    public function get($id)
    {
        $userAddress = UserAddress::findOrFail($id);

        return ok('user address get successfully', $userAddress);
    }
    /**
     * API of Update user address
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'user_id'         => 'required|exists:users,id',
            'address_type_id' => 'required|exists:address_types,id',
            'address1'        => 'required|string|max:50',
            'address2'        => 'required|string|max:50',
            'zip_code'        => 'nullable|integer|size:6',
            'is_primary'      => 'nullable|boolean'
        ]);

        $userAddress = UserAddress::findOrFail($id);
        $userAddress->update($request->only('user_id', 'address_type_id', 'address1', 'address2', 'zip_code', 'is_primary'));

        return ok('user address updated successfully!', $userAddress);
    }
    /**
     * API of Delete user address
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        UserAddress::findOrFail($id)->delete();

        return ok('user address deleted successfully');
    }
}
