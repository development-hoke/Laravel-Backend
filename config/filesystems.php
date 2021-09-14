<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | 画像保存用Disk
    |--------------------------------------------------------------------------
    */
    'image' => env('FILESYSTEM_IMAGE', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],

        'order' => [
            'driver' => 'local',
            'root' => storage_path('logs/orders'),
            'visibility' => 'private',
        ],
    ],

    // ディレクトリの管理
    // ディスクごとにまとめる。
    'dirs' => [
        'local' => [
            // 一時ディレクトリ
            'tmp' => 'tmp',
        ],
        'image' => [
            // 商品
            'item' => 'items',
            // 過去商品
            'past_item' => 'past-items',
            // 商品プレビュー
            'item_preview' => 'item-preview',
            // 企画
            'plan_thumb' => 'plan-thumbnail',
            // トップ
            'top_content_main_visual' => 'main-visulas',
            // wysiwygエディタ全般
            'content' => 'contents',
        ],
    ],
];
