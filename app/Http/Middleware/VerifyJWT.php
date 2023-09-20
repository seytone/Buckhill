<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\JwtToken;

class VerifyJWT
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // extract token if exists
            if (! $jwt = $request->bearerToken())
                throw new Exception('could not extract token');

            // use public key to decode token
            if (! is_object(JWT::decode($jwt, new Key(file_get_contents(config('jwt.keys.public')), config('jwt.algorithm')))))
                throw new Exception('invalid token decoding');

            // check if token exists in database
            if (! $token = JwtToken::where('unique_id', md5($jwt))->first())
                throw new Exception('unauthorized token');

            $request->merge(['user' => $token->user ?? null]);

        } catch(Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 401);
        }

        return $next($request);
    }
}
