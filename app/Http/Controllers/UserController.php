<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Models\Order;

class UserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Action not allowed.',
            'data' => null,
        ], 405);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        // Check if the user is trying to see another user profile.
        if ($request->user->uuid !== $id)
            return response()->json([
                'success' => false,
                'message' => 'Action not allowed.',
                'data' => null,
            ], 405);

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
        // Check if the user is trying to edit another user profile.
        if ($request->user->uuid !== $id)
            return response()->json([
                'success' => false,
                'message' => 'Action not allowed.',
                'data' => null,
            ], 405);

        $user = User::where('is_admin', 0)->where('uuid', $id)->first();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255|',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
        ]);

        if ($errors = $validator->errors()->all())
            return response()->json([
                'success' => false,
                'message' => $errors,
            ], 403);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => $user,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Action not allowed.',
            'data' => null,
        ], 405);
    }

    /**
     * Get orders of a user.
     */
    public function orders(Request $request, string $id)
    {
        // Check if the user is trying to see another user orders.
        if ($request->user->uuid !== $id)
            return response()->json([
                'success' => false,
                'message' => 'Action not allowed.',
                'data' => null,
            ], 405);

        $orders = Order::where('user_id', $request->user->id)->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Orders fetched successfully.',
            'data' => $orders,
        ], 200);
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
