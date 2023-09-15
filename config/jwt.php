<?php

return [
    'secret' => env('JWT_SECRET'),
    'keys' => [
        'private' => storage_path('keys/jwtRS256.key'),
        'public' => storage_path('keys/jwtRS256.key.pub'),
    ],
    'algorithm' => env('JWT_ALGORITHM', 'RS256'),
];
