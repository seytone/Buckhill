<?php

namespace App\Http\Controllers;

use Exception;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use App\Models\User;
use App\Models\JwtToken;

class AuthController extends Controller
{
    /**
     * Login an user using credentials.
     */
    /**
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"Authentication"},
     *     summary="Authenticate user and generate JWT token",
     *     description="Authenticate user and generate JWT token",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *        response=200,
     *        description="User logged-in successfully.",
     *        @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
     *          @OA\Property(property="data", type="object")
     *        ),
     *     ),
     *     @OA\Response(
     *        response=400,
     *        description="Validation errors.",
     *        @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="401"),
     *          @OA\Property(property="data", type="object")
     *        ),
     *     ),
     *     @OA\Response(
     *        response=401,
     *        description="Invalid credentials.",
     *        @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="401"),
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
     *        response=500,
     *        description="Failed to generate access token.",
     *        @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="403"),
     *          @OA\Property(property="data", type="object")
     *        ),
     *     ),
     * )
     */
    public function login(Request $request) : object
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:users',
            'password' => 'required|string|min:5',
        ]);

        if ($errors = $validator->errors()->all())
            return response()->json([
                'success' => false,
                'message' => $errors,
            ], 400);

        if (! Auth::attempt($request->only(['email', 'password'])))
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);

        if (! $user = Auth::user())
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);

        if (! $token = $this->generateAccessToken($request, $user))
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate access token.',
            ], 500);

        return response()->json([
            'success' => true,
            'message' => 'User logged-in successfully.',
            'authorization' => [
                'type' => 'bearer',
                'access_token' => $token->access_token ?? null,
                'expires_at' => $token->expires_at ?? null,
                'user' => $user,
            ]
        ], 200);
    }

    /**
     * Logout an user.
     */
    /**
     * @OA\Get(
     *     path="/auth/logout",
     *     tags={"Authentication"},
     *     summary="Log-out user and invalidate JWT token",
     *     description="Log-out user and invalidate JWT token",
     *     @OA\Response(
     *        response=200,
     *        description="User logged out successful.",
     *        @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
     *          @OA\Property(property="data", type="object")
     *        ),
     *     ),
     *     @OA\Response(
     *        response=404,
     *        description="Token not found.",
     *        @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="403"),
     *          @OA\Property(property="data", type="object")
     *        ),
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function logout(Request $request) : object
    {
        if (! $request->bearerToken())
            return response()->json([
                'success' => false,
                'message' => 'Token not found.',
            ], 404);

        JwtToken::where('unique_id', md5($request->bearerToken()))->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful.',
        ], 200);
    }

    /**
     * Generate an user token.
     */
    protected function generateAccessToken(Request $request, User $user) : object | null
    {
        if (! $user instanceof User)
            return null;

        $timestamp = new DateTimeImmutable();
        $issued_at = $timestamp->getTimestamp();
        $expires_at = $timestamp->modify('+3 hour')->getTimestamp();

        // define jwt token claims
        $payload = [
            'iss' => $request->server('SERVER_NAME', 'UNKNOWN'),
            'sub' => $user->uuid,
            'jti' => base64_encode(random_bytes(16)),
            'iat' => $issued_at,
            'exp' => $expires_at,
            'data' => [
                'user_uuid' => $user->uuid,
                'user_email' => $user->email,
                'user_is_admin' => $user->is_admin,
            ]
        ];

        // use private key to encode and generate token
        $token = JWT::encode($payload, file_get_contents(config('jwt.keys.private')), config('jwt.algorithm'));

        // create or update token in database
        JwtToken::updateOrCreate([
            'user_id' => $user->id,
        ], [
            'unique_id' => md5($token),
            'token_title' => 'access-token: ' . str_replace(' ', '-', mb_strtolower($user->first_name . '-' . $user->last_name)),
            'expires_at' => $expires_at,
            'refreshed_at' => $issued_at,
        ]);

        return (object) [
            'access_token' => $token,
            'expires_at' => $expires_at,
        ];
    }
}
