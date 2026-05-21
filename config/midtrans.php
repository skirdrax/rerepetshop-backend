<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'isproduction' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED'),
    'is3ds' => env('MIDTRANS_IS_3DS'),
    
];