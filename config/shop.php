<?php

return [
    'theme' => env('THEME','with'),
    'front_url' => env('FRONT_URL',''),
    'image_svr' => env('IMAGE_SVR',''),
    'partner' => [
        'view' => env('PARTNER_VIEW','partner')
    ],
    'head' => [
        'view' => env('HEAD_VIEW','head')
    ],
    'store' => [
        'view' => env('STORE_VIEW','store')
    ]
];
