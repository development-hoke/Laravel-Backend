<?php

namespace Tests\Unit\Domain\Utils;

use App\Domain\Utils\ItemPrice;
use Tests\TestCase;

class ItemPriceTest extends TestCase
{
    public function dataCalculateTotal()
    {
        return [
            '商品なし' => [
                [],
                0,
            ],
            '商品 1件' => [
                [
                    [
                        'id' => 1,
                        'status' => [
                            'value' => 2,
                            'label' => '予約する',
                        ],
                        'brand' => [
                            'id' => 4,
                            'name' => 'RADIATE',
                        ],
                        'name' => 'ラップ風 異素材ドッキングワンピース',
                        'product_number' => '1234-1234',
                        'color' => [
                            'id' => 3,
                            'display_name' => 'オレンジ',
                        ],
                        'size' => [
                            'id' => 4,
                            'name' => '7号',
                        ],
                        'price' => 16000,
                        'delivery_datetime' => '2020-01-01 09:00:00',
                        'retail_price' => 0,
                        'images' => [
                            [
                                'id' => 1,
                                'type' => 0,
                                'url' => 'http://localhost:3000/_nuxt/assets/images/goods1.png',
                            ],
                        ],
                        'count' => 1,
                    ],
                ],
                16000,
            ],
            '商品 2件 別種類' => [
                [
                    [
                        'id' => 1,
                        'status' => [
                            'value' => 2,
                            'label' => '予約する',
                        ],
                        'brand' => [
                            'id' => 4,
                            'name' => 'RADIATE',
                        ],
                        'name' => 'ラップ風 異素材ドッキングワンピース',
                        'product_number' => '1234-1234',
                        'color' => [
                            'id' => 3,
                            'display_name' => 'オレンジ',
                        ],
                        'size' => [
                            'id' => 4,
                            'name' => '7号',
                        ],
                        'price' => 16000,
                        'delivery_datetime' => '2020-01-01 09:00:00',
                        'retail_price' => 0,
                        'images' => [
                            [
                                'id' => 1,
                                'type' => 0,
                                'url' => 'http://localhost:3000/_nuxt/assets/images/goods1.png',
                            ],
                        ],
                        'count' => 1,
                    ],
                    [
                        'id' => 2,
                        'status' => [
                            'value' => 1,
                            'label' => '通常購入',
                        ],
                        'brand' => [
                            'id' => 4,
                            'name' => 'RADIATE',
                        ],
                        'name' => 'ラップ風 異素材ドッキングワンピース',
                        'product_number' => '1234-1235',
                        'color' => [
                            'id' => 3,
                            'display_name' => 'オレンジ',
                        ],
                        'size' => [
                            'id' => 4,
                            'name' => '7号',
                        ],
                        'price' => 16000,
                        'delivery_datetime' => '2020-01-01 09:00:00',
                        'retail_price' => 0,
                        'images' => [
                            [
                                'id' => 1,
                                'type' => 0,
                                'url' => 'http://localhost:3000/_nuxt/assets/images/goods2.png',
                            ],
                        ],
                        'count' => 1,
                    ],
                ],
                32000,
            ],
            '商品 2件 同種類' => [
                [
                    [
                        'id' => 1,
                        'status' => [
                            'value' => 2,
                            'label' => '予約する',
                        ],
                        'brand' => [
                            'id' => 4,
                            'name' => 'RADIATE',
                        ],
                        'name' => 'ラップ風 異素材ドッキングワンピース',
                        'product_number' => '1234-1234',
                        'color' => [
                            'id' => 3,
                            'display_name' => 'オレンジ',
                        ],
                        'size' => [
                            'id' => 4,
                            'name' => '7号',
                        ],
                        'price' => 16000,
                        'delivery_datetime' => '2020-01-01 09:00:00',
                        'retail_price' => 0,
                        'images' => [
                            [
                                'id' => 1,
                                'type' => 0,
                                'url' => 'http://localhost:3000/_nuxt/assets/images/goods1.png',
                            ],
                        ],
                        'count' => 2,
                    ],
                ],
                32000,
            ],
        ];
    }

    /**
     * @param $items
     * @param $expected
     * @dataProvider dataCalculateTotal
     */
    public function testCalculateTotal($items, $expected)
    {
        $result = ItemPrice::calculateTotal($items);
        $this->assertEquals($expected, $result);
    }

    public function dataIsOwnItem()
    {
        return [
            '自社製品 前半0088' => [
                'sample' => '0088-1234',
                'expected' => true,
            ],
            '他社製品 後半0088' => [
                'sample' => '1234-0088',
                'expected' => false,
            ],
            '自社製品 前半3500' => [
                'sample' => '3500-1234',
                'expected' => true,
            ],
            '他社製品' => [
                'sample' => '1234-1234',
                'expected' => false,
            ],
        ];
    }

    /**
     * @param $sample
     * @param $expected
     * @dataProvider dataIsOwnItem
     */
    public function testIsOwnItem($sample, $expected)
    {
        $result = $this->doPrivateMethod(new ItemPrice(), 'isOwnItem', $sample);
        $this->assertEquals($expected, $result);
    }
}
