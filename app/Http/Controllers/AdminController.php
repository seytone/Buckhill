<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/admin",
     *     tags={"Admin"},
     *     summary="Get admin listing",
     *     description="Get admin listing",
     *     @OA\Response(
     *        response=200,
     *        description="Admins fetched successfully.",
     *        @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
     *          @OA\Property(property="data", type="object")
     *        ),
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        $admins = User::where('is_admin', 1)->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Admins fetched successfully.',
            'data' => $admins,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/admin",
     *     tags={"Admin"},
     *     summary="Create a new admin",
     *     description="Create a new admin",
     *     @OA\Parameter(
     *         name="first_name",
     *         in="query",
     *         description="Admin's first name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="last_name",
     *         in="query",
     *         description="Admin's last name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Admin's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="address",
     *         in="query",
     *         description="Admin's address",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="phone_number",
     *         in="query",
     *         description="Admin's phone",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Admin's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password_confirm",
     *         in="query",
     *         description="Admin's password confirmation",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *        response=200,
     *        description="Admin created successfully.",
     *        @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
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
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255|',
            'email' => 'required|string|email|max:255|unique:users',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'password' => 'required|string|min:5',
            'password_confirm' => 'required|same:password',
        ]);

        if ($errors = $validator->errors()->all())
            return response()->json([
                'success' => false,
                'message' => $errors,
            ], 403);

        $user = new User;
        $user->uuid = $user->generateUuid();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->is_admin = 1;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->phone_number = $request->phone_number;
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Admin created successfully.',
            'data' => $user,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/admin/{id}",
     *     tags={"Admin"},
     *     summary="Get logged-in admin details",
     *     description="Get logged-in admin details",
     *     @OA\Response(
     *        response=200,
     *        description="Admin fetched successfully.",
     *        @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
     *          @OA\Property(property="data", type="object")
     *        ),
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show(string $id)
    {
        $admin = User::where('is_admin', 1)->where('uuid', $id)->first();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Admin not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Admin fetched successfully.',
            'data' => $admin,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Action not allowed.',
            'data' => null,
        ], 405);
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
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/admin/user-listing",
     *     tags={"Admin"},
     *     summary="Get user listing",
     *     description="Get user listing",
     *     @OA\Response(
     *        response=200,
     *        description="Users fetched successfully.",
     *        @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
     *          @OA\Property(property="data", type="object")
     *        ),
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function userListing()
    {
        $users = User::where('is_admin', 0)->paginate(20);
        $users->appends(['sort' => 'first_name']);

        return response()->json([
            'success' => true,
            'message' => 'Users fetched successfully.',
            'data' => $users,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/admin/user-edit/{id}",
     *     tags={"Admin"},
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
     *        response=403,
     *        description="Validation errors.",
     *        @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="403"),
     *          @OA\Property(property="data", type="object")
     *        ),
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function userEdit(Request $request, string $id)
    {
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
    /**
     * @OA\Delete(
     *    path="/admin/user-delete/{id}",
     *    tags={"Admin"},
     *    summary="Delete User",
     *    description="Delete User",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        description="Id of User",
     *        required=true,
     *        @OA\Schema(type="string")
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
     *          @OA\Property(property="data", type="object")
     *        ),
     *    ),
     *    security={{"bearerAuth":{}}}
     *  )
     */
    public function userDelete(string $id)
    {
        User::where('is_admin', 0)->where('uuid', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
            'data' => null,
        ], 200);
    }
}
