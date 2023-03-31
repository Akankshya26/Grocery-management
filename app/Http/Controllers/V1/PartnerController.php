<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PartnerController extends Controller
{
    public function view()
    {
        $user = User::where('id', auth()->user()->id)->get();
        return ok('User Profile get succesfully', $user);
    }
    /**
     * API of List partner
     *
     *@param  \Illuminate\Http\Request  $request
     *@return $partner
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

        $query = User::query()->where('type', 'partner');

        if ($request->search) {
            $query = $query->where('first_name', 'like', "%$request->search%")
                ->Orwhere('last_name', 'like', "%$request->search%");
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
        $partner = $query->get();

        $data = [
            'count'     => $count,
            'partners'  => $partner
        ];

        return ok('Partner  list', $data);
    }
    /**
     * API of create partner
     *
     *  @param  \Illuminate\Http\Request  $request
     * @return $token
     */
    public function create(Request $request)
    {
        //validation
        $request->validate([
            'first_name'        => 'required|alpha|max:36',
            'last_name'         => 'required|alpha|max:36',
            'email'             => 'required|email|unique:users,email|max:255',
            'password'          => 'required|max:8',
            'organization_name' => 'required_if:type,partner',
        ]);

        $user = User::create($request->only('first_name', 'last_name', 'type', 'email', 'organization_name') + [
            'password' => Hash::make($request->password)
        ] + ['type' => 'partner']);
        $data = [
            'user'  => $user
        ];

        return ok("partner Created successfully!", $data);
    }
    /**
     * API of Update partenr
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'first_name'        => 'nullable|alpha|max:36',
            'last_name'         => 'nullable|alpha|max:36',
            'email'             => 'nullable|email|unique:users,email|max:255',
            'organization_name' => 'required_if:type,partner',
        ]);

        $user = User::findOrFail($id);
        $user->update($request->only('first_name', 'last_name', 'type', 'email', 'oragnization_name')
            + ['type' => 'partner']);

        return ok('Partner updated successfully!', $user);
    }
    /**
     * API of Delete Partner
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        User::findOrFail($id)->delete();

        return ok('Partner deleted successfully');
    }
}
