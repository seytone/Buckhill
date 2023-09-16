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
    public function store(Request $request) : object
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
    /**
     * @OA\Get(
     *     path="/user/{id}",
     *     tags={"User"},
     *     summary="Get logged-in user details",
     *     description="Get logged-in user details",
     *     @OA\Response(
     *        response=200,
     *        description="User fetched successfully.",
     *        @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
     *          @OA\Property(property="data", type="object")
     *        ),
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show(Request $request, string $id) : object
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
    /**
     * @OA\Put(
     *     path="/user/{id}",
     *     tags={"User"},
     *     summary="Update the given user",
     *     description="Update the given user",
     *     @OA\Parameter(
     *         name="first_name",
     *         in="query",
     *         description="User's first name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="last_name",
     *         in="query",
     *         description="User's last name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="address",
     *         in="query",
     *         description="User's address",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="phone_number",
     *         in="query",
     *         description="User's phone",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *        response=200,
     *        description="User updated successfully.",
     *        @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
     *          @OA\Property(property="data", type="object")
     *        ),
     *     ),
     *     @OA\Response(
     *        response=405,
     *        description="Action not allowed.",
     *        @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="403"),
     *          @OA\Property(property="data", type="object")
     *        ),
     *     ),
     *     @OA\Response(
     *        response=404,
     *        description="User not found.",
     *        @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="403"),
     *          @OA\Property(property="data", type="object")
     *        ),
     *     ),
     *     @OA\Response(
     *        response=403,
     *        description="Validation errors.",
     *        @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="403"),
     *          @OA\Property(property="data", type="object")
     *        ),
     *     ),
     *     security={{ "bearerAuth":{ }}}
     * )
     */
    public function update(Request $request, string $id) : object
    {
        // Check if the user is trying to edit another user profile.
        if ($request->user->uuid !== $id)
            return response()->json([
                'success' => false,
                'message' => 'Action not allowed.',
                'data' => null,
            ], 405);

        if (! $user = User::where('is_admin', 0)->where('uuid', $id)->first())
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'data' => null,
            ], 404);

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
    public function destroy(string $id) : object
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
    /**
     * @OA\Get(
     *     path="/user/orders/{id}",
     *     tags={"User"},
     *     summary="Get user's orders listing",
     *     description="Get user's orders listing",
     *     @OA\Response(
     *        response=200,
     *        description="Orders listed successfully.",
     *        @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
     *          @OA\Property(property="data", type="object")
     *        ),
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function orders(Request $request, string $id) : object
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
    public function forgotPassword(Request $request) : void
    {
        //
    }

    /**
     * Reset the given user's password.
     */
    public function resetPassword(Request $request) : void
    {
        //
    }
}
