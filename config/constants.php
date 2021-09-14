<?php

return [
    // todo http_communicationに統合
    'f_regi' => [
        'shop_id' => '20563',
        'api_key' => 'efc92ffcb0dd007b',
    ],
    'order' => [
        // 配送希望日
        'delivery_date' => [
            'to' => 4,
            'from' => 14,
        ],
        'payment_fee' => [
            // 代引き手数料
            \App\Enums\Order\PaymentType::NP => 210,
            \App\Enums\Order\PaymentType::Cod => 330,
        ],
        // 受注コード
        'code' => [
            'shop_no' => '09967',
            'pos_no' => '999',
        ],
    ],
    'delivery_fee' => [
        // 配送料初期値
        'default_price' => 510,
        // 送料無料下限金額
        'free_delivery_criteria' => 10000,
    ],
    'cart' => [
        // 製品番号prefix
        'maker_product_number' => [
            'owns' => [
                '0088',
                '3500',
            ],
        ],
        // 社割
        'employee_discount' => [
            'own' => 0.5,
            'other' => 0.3,
        ],
    ],
    'item_price' => [
        // 公取委の制御。2週間以上値引き価格で売ったら元値が消える。
        'display_original_price_period_days' => 14,
    ],
    'stock' => [
        // 取り寄せできる在庫下限数量(最低有効在庫数)
        'back_orderble_min_stock' => 30,
        // EC在庫下限点数(最低有効在庫数)
        'ec_min_stock' => 3,
    ],
    'store' => [
        // 経営基幹のオンラインストアID
        'ec_store_id' => 5,
        // 本部のストアID
        'headquarter_store_id' => 11,
    ],
    'point' => [
        // 商品マスタにない商品を販売したときに true になる。実店舗向けのフラグになるので、ECからはfalse固定。
        'not_item_sales_div' => false,
    ],
    'default_tax_rate_id' => [
        // 指定日 => 消費税率
        '2020-11-01' => App\Enums\OrderDetail\TaxRateId::Rate10,
        // 2025年11月1日から消費税率を変更したい場合
        // '2025-11-01' => 0.10,
    ],
    'tax_rates' => [
        App\Enums\OrderDetail\TaxRateId::Rate10 => 0.10,
    ],
    'per_page' => [
        'items' => 40,
        'stores' => 40,
        'news' => 20,
    ],
    'order_discount' => [
        'priority' => [
            'default' => 10000,

            // 配送タイプ
            \App\Enums\OrderDiscount\Type::CouponDeliveryFee => 1,
            \App\Enums\OrderDiscount\Type::ReservationDeliveryFee => 2,
            \App\Enums\OrderDiscount\Type::DeliveryFee => 3,

            // 商品タイプ
            // 商品タイプの適用優先度がある場合ここに追加する
        ],
    ],
];
