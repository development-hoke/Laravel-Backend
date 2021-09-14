<?php

return [
    'CaptureResult' => [
        'CaptureDetails' => [
            'AmazonCaptureId' => (string) \Webpatser\Uuid\Uuid::generate(4),
            'CaptureReferenceId' => (string) \Webpatser\Uuid\Uuid::generate(4),
            'SellerCaptureNote' => null,
            'CaptureAmount' => [
                'Amount' => 3874,
                'CurrencyCode' => 'JPY',
            ],
            'RefundAmount' => [
                'Amount' => 0,
                'CurrencyCode' => 'JPY',
            ],
            'CaptureFee' => [
                'Amount' => 0,
                'CurrencyCode' => 'JPY',
            ],
            'CreationTimestamp' => 1616482585,
            'CaptureStatus' => [
                'State' => \App\Enums\AmazonPay\Status\Capture::Completed,
                'LastUpdateTimestamp' => 1616482585,
            ],
        ],
    ],
];
