<?php

return [
    'GetAuthorizationDetailsResult' => [
        'AuthorizationDetails' => [
            'AmazonAuthorizationId' => 'S03-7494578-2241002-A067527',
            'AuthorizationReferenceId' => '415f2753040941cbab45fbe251640a19',
            'SellerAuthorizationNote' => null,
            'AuthorizationAmount' => [
                'Amount' => 3874,
                'CurrencyCode' => 'JPY',
            ],
            'CaptureAmount' => [
                'Amount' => 0,
                'CurrencyCode' => 'JPY',
            ],
            'AuthorizationFee' => [
                'Amount' => 0,
                'CurrencyCode' => 'JPY',
            ],
            'AuthorizationStatus' => [
                'State' => \App\Enums\AmazonPay\Status\Authorization::Closed,
                'LastUpdateTimestamp' => 1616482585,
            ],
            'ExpirationTimestamp' => 1616482585,
            'SoftDecline' => false,
            'CaptureNow' => false,
        ],
    ],
];
