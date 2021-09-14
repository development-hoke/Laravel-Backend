<?php

namespace Tests\Unit\Models;

use App\Models\Cart;
use Tests\TestCase;

class CartTest extends TestCase
{
    public function dataChangeItems()
    {
        return [
            '商品が入っていなかった場合' => [
                'cartItems' => [],
                'expected' => [
                    [
                        'item_detail_id' => 1,
                        'status' => 1,
                        'count' => 1,
                    ],
                ],
            ],
            '同じ商品が1つ入っている場合 数量変更' => [
                'cartItems' => [
                    [
                        'item_detail_id' => 1,
                        'status' => 1,
                        'count' => 2,
                    ],
                ],
                'expected' => [
                    [
                        'item_detail_id' => 1,
                        'status' => 1,
                        'count' => 1,
                    ],
                ],
            ],
            '異なる商品が1つ入っている場合(入荷日による並び替えも含む)' => [
                'cartItems' => [
                    [
                        'item_detail_id' => 2,
                        'status' => 1,
                        'count' => 2,
                    ],
                ],
                'expected' => [
                    [
                        'item_detail_id' => 1,
                        'status' => 1,
                        'count' => 1,
                    ],
                    [
                        'item_detail_id' => 2,
                        'status' => 1,
                        'count' => 2,
                    ],
                ],
            ],
        ];
    }

    /**
     * カート商品の変更テスト
     *
     * @param $cartItems
     * @param $expected
     * @dataProvider dataChangeItems
     */
    public function testChangeItems($cartItems, $expected)
    {
        // 追加する商品
        $cartItem = [
            'item_detail_id' => 1,
            'status' => 1,
            'count' => 1,
        ];
        // 既存カート
        $cart = new Cart([
            'items' => $cartItems,
        ]);
        $cart->changeItems($cartItem);
        $this->assertEquals($expected, $cart->items);
    }
}
