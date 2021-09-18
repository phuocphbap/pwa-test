<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. You may choose one of them according to your PHP
    | configuration. By default PHP's "GD Library" implementation is used.
    |
    | Supported: "gd", "imagick"
    |
    */

    'driver' => 'gd',
    'avatar' => [
        'type' => [
            'original' => [
                'path' => 'avatar/original/',
                'width' => null,
            ],
        ],
    ],
    'service' => [
        'type' => [
            'original' => [
                'path' => 'service/original/',
                'width' => null,
            ],
        ],
    ],
    'store' => [
        'type' => [
            'original' => [
                'path' => 'store/original/',
                'width' => null,
            ],
        ],
    ],
    'store_articles' => [
        'type' => [
            'original' => [
                'path' => 'store_articles/original/',
                'width' => null,
            ],
        ],
    ],
    'store_introduction' => [
        'type' => [
            'original' => [
                'path' => 'store_introduction/original/',
                'width' => null,
            ],
        ],
    ],
    'store_image' => [
        'type' => [
            'original' => [
                'path' => 'store_image/original/',
                'width' => null,
            ],
        ],
    ],
    'identity_card' => [
        'type' => [
            'original' => [
                'path' => 'identity_card/original/',
                'width' => null,
            ],
        ],
    ],
    'advertising' => [
        'type' => [
            'original' => [
                'path' => 'advertising/original/',
                'width' => null,
            ],
        ],
    ],
    'company_terms' => [
        'type' => [
            'original' => [
                'path' => 'company_terms/original/',
                'width' => null,
            ],
        ],
    ],
];
