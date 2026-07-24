<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AppSection Section Media Container
    |--------------------------------------------------------------------------
    |
    |
    |
    */
    'disk' => 'public',

    'image' => [
        'mimes' => ['jpg', 'jpeg', 'png', 'webp'],
        'max_size_kb' => 5120,
    ],

    'limits' => [
        'product' => 9,
        'customer' => 1,
    ],
];
