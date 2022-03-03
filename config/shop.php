<?php

return [
    'theme' => env('THEME','with'),
    'image_svr' => env('IMAGE_SVR',''),
    'partner' => [
        'view' => env('PARTNER_VIEW','partner')
    ],
    'head' => [
        'view' => env('HEAD_VIEW','head')
    ]
];
