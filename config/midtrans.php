<?php

return [
    'server_key'     => env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-8XxcuevGV2yDcvXVEHWQkNLl'),
    'client_key'     => env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-_Zd6crFAfyuAw9jG'),
    'is_production'  => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized'   => true,
    'is3ds'          => true,
];
