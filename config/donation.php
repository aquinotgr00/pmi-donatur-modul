<?php

return [
    'status' => [
        1 => 'Pending',
        2 => 'Menunggu',
        3 => 'Berhasil',
        4 => 'Dibatalkan',
        5 => 'Selesai'
    ],
    'pick_method'=> [
        1 => 'Diantar',
        2 => 'Dikirim'
    ],
    'payment_method'=> [
        1 => 'Manual Transfer',
        2 => 'Otomatis Transfer'
    ],
    'push_notification' => [
        'app_id' => 'f15f0f81-9c61-4cf1-ac1d-74e25525ff5a',
        'rest_api_key' => 'ZmU3MWM4MTgtZTEzZi00YWE1LWEzYWUtZGI5MDkwMzQ2Yzk3'
    ],
    'payment_gateway' => [
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
        'client_key' => env('MIDTRANS_CLIENT_KEY','SB-Mid-client-5cSArh5V34nHg_JD'),
        'server_key' => env('MIDTRANS_SERVER_KEY','SB-Mid-server-baMLstdpTA09vZusn5hDr69e')
    ]
];