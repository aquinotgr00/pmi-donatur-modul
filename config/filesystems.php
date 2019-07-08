<?php

return [
    'disks' => [
        'public' => [
            'driver'     => 'local',
            'root'       => public_path() . '/images/campaign',
            'url'        => env('APP_URL') . '/public',
            'visibility' => 'public',
        ],
    ],
];