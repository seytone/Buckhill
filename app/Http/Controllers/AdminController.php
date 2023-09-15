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
