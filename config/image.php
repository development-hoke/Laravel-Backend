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

    // 画像リサイズ設定
    'resize' => [
        'item' => [
            App\Enums\ItemImage\Size::S => ['w' => 240, 'h' => 288],
            App\Enums\ItemImage\Size::M => ['w' => 380, 'h' => 456],
            App\Enums\ItemImage\Size::L => ['w' => 750, 'h' => 900],
            App\Enums\ItemImage\Size::XL => ['w' => 1210, 'h' => 1452],
        ],
    ],
];
