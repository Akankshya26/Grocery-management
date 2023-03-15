<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserAddress;

class UserAddressController extends Controller
{
    /**
     * API of Create User Address
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $userAddress
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'user_id'         => 'required|exists:users,id',
            'address_type_id' => 'required|exists:address_types,id',
            'address1'        => 'required|string|max:50',
            'address2'        => 'required|string|max:50',
            'zip_code'        => 'nullable|integer|min:6',
            'is_primary'      => 'nullable|boolean'
        ]);

        $userAddress = UserAddress::create($request->only('user_id', 'address_type_id', 'address1', 'address2', 'zip_code', 'is_primary'));

        return ok('User Address created successfully!', $userAddress);
    }
}
