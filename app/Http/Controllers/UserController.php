<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::where('is_admin', 0)->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Users fetched successfully.',
            'data' => $users,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255|',
            'email' => 'required|string|email|max:255|unique:users',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = new User;
        $user->uuid = $user->generateUuid();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->is_admin = 0;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->phone_number = $request->phone_number;
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => $user,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::where('is_admin', 0)->where('uuid', $id)->first();

        return response()->json([
            'success' => true,
            'message' => 'User fetched successfully.',
            'data' => $user,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255|',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
        ]);

        User::where('is_admin', 0)->where('uuid', $id)->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => null,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::where('is_admin', 0)->where('uuid', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
            'data' => null,
        ], 200);
    }

    /**
     * Get orders of a user.
     */
    public function orders(Request $request)
    {
        //
    }

    /**
     * Send a password reset link to the given user.
     */
    public function forgotPassword(Request $request)
    {
        //
    }

    /**
     * Reset the given user's password.
     */
    public function resetPassword(Request $request)
    {
        //
    }
}
