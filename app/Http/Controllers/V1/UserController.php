<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{

    public function view()
    {
        $user = User::where('id', Auth::id())->get();
        return ok('User Profile get succesfully', $user);
    }
    /**
     * API of User registration
     *
     *  @param  \Illuminate\Http\Request  $request
     * @return $token
     */
    public function register(Request $request)
    {
        //validation
        $request->validate([
            'first_name'        => 'required|alpha|max:36',
            'last_name'         => 'required|alpha|max:36',
            'email'             => 'required|email|unique:users,email|max:255',
            'password'          => 'required|max:8',
            'type'              => 'in:customer'
        ]);
        $user = User::create($request->only('first_name', 'last_name', 'type', 'email') + [
            'password' => Hash::make($request->password)
        ]);
        $data = [
            'user'  => $user
        ];

        return ok("User registered successfully!", $data);
    }

    /**
     * API of User login
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $data
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return error("User with this email is not found!");
        }
        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken($request->email)->plainTextToken;

            $data = [
                'token' => $token,
                'user'  => $user
            ];
            return ok('User Logged in Succesfully', $data);
        } else {
            return error("Password is incorrect");
        }
    }

    /**
     * API of User Logout
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return ok("Logged out successfully!");
    }

    /**
     * API of Update user
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
            'type'              => 'in:admin,partner,customer', //default customer
            'organization_name' => 'required_if:type,partner',
            'rating'            => 'required_if:type,partner',
        ]);
        $user = User::findOrFail($id);
        $user->update($request->only('first_name', 'last_name', 'type', 'email', 'oragnization_name', 'rating'));

        return ok('User updated successfully!', $user);
    }

    /**
     * API of Delete User
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function delete($id)
    {
        User::findOrFail($id)->delete();

        return ok('User deleted successfully');
    }
    //change password
    public function updatePassword(Request $request)
    {
        # Validation
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required',
        ]);


        #Match The Old Password
        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return back()->with("error", "Old Password Doesn't match!");
        }


        #Update the new Password
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
        return ok('password updated succesfully');
    }
}
