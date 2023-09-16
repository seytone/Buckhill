<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *    version="1.0.0",
 *    title="Buckhill Pet Shop API Documentation",
 *    description="This is a simple API documentation for Buckhill Pet Shop API v1.",
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * @OA\Server(
 *     url="/api/v1",
 * ),

 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
