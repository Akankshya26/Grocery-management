<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use Illuminate\Support\Str;
use App\Mail\InvitationMail;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{

    public function view()
    {
        $user = User::where('id', auth()->user()->id)->get();
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
        ] + ['phone' => $request->phone]);
        Mail::to($user->email)->send(new InvitationMail($user));
        return ok("User registered successfully!", $user);
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

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return error('Email does not exist');
        }
        $token = Str::random(40);
        PasswordReset::create([
            'email'      => $user->email,
            'token'      => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::to($user->email)->send(new ForgotPasswordMail($token));
        return ok('Please check Your email to reset your password');
    }

    public function resetPassword(Request $request, $token)
    {
        $password = Carbon::now()->subMinute(1)->toDateTimeString();
        PasswordReset::where('created_at', $password)->delete();
        $request->validate([
            'password' => 'required|max:8'
        ]);
        $resetPassword = PasswordReset::where('token', $token)->first();
        if (!$resetPassword) {
            return error('token i is invali or expire');
        }
        $user = User::where('email', $resetPassword->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        PasswordReset::where('email', $user->email)->delete();
        return ok('Password Reset successfully');
    }
}
