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
     *     path="/api/v1/auth/login",
     *     summary="Authenticate user and generate JWT token",
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
     *     @OA\Response(response="200", description="Login successful"),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:users',
            'password' => 'required|string|min:5',
        ]);

        if ($errors = $validator->errors()->all())
            return response()->json([
                'success' => false,
                'message' => $errors,
            ], 401);

        if (! Auth::attempt($request->only(['email', 'password'])))
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);

        try {
            $user = Auth::user();
            $token = $this->generateAccessToken($user);

            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
                'authorization' => [
                    'type' => 'bearer',
                    'access_token' => $token->access_token,
                    'expires_at' => $token->expires_at,
                    'user' => $user,
                ]
            ], 200);
        } catch(Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Logout an user.
     */
    /**
     * @OA\Get(
     *     path="/api/v1/auth/logout",
     *     summary="Log-out user and invalidate JWT token",
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function logout(Request $request)
    {
        JwtToken::where('unique_id', md5($request->bearerToken()))->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful.',
        ], 200);
    }

    /**
     * Generate an user token.
     */
    protected function generateAccessToken($user) : object
    {
        $timestamp = new DateTimeImmutable();
        $issued_at = $timestamp->getTimestamp();
        $expires_at = $timestamp->modify('+3 hour')->getTimestamp();

        // define jwt token claims
        $payload = [
            'iss' => $_SERVER['SERVER_NAME'],
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
