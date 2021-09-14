<?php

namespace Tests\Unit\Services\Front;

use App\Models\Cart;
use App\Services\Front\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Cart::truncate();
        foreach ($this->samples() as $sample) {
            factory(Cart::class)->create($sample);
        }
    }

    private function samples()
    {
        return [
            // 会員カート商品なし
            [
                'token' => '',
                'member_id' => 1,
                'items' => [],
            ],
            // 会員カート商品あり
            [
                'token' => '',
                'member_id' => 2,
                'items' => [
                    ['member_item'],
                ],
            ],
            // ゲストカート商品なし
            [
                'token' => 'test1',
                'member_id' => null,
                'items' => [],
            ],
            // ゲストカート商品あり
            [
                'token' => 'test2',
                'member_id' => null,
                'items' => [
                    ['guest_item'],
                ],
            ],
        ];
    }

    private function changeSamples(array $samples, array $data)
    {
        foreach ($data as $index => $datum) {
            $samples[$index] = $datum;
        }

        return $samples;
    }

    private function remove(array $samples, int $index)
    {
        unset($samples[$index]);

        return array_values($samples);
    }

    public function dataMergeByMemberIdAndToken()
    {
        $samples = $this->samples();

        return [
            '会員カートなし&ゲストカートなしの場合 => 変更なし' => [
                'params' => [
                    'member_id' => 3,
                    'token' => 'test3',
                ],
                'expected' => $samples,
            ],
            '会員カートなし&ゲストカート商品なしの場合 => 有効な会員カートがない場合はゲストカートを会員カートに変更' => [
                'params' => [
                    'member_id' => 3,
                    'token' => 'test1',
                ],
                'expected' => $this->changeSamples(
                    $samples,
                    [
                        2 => [
                            'token' => '',
                            'member_id' => 3,
                            'items' => [],
                        ],
                    ]
                ),
            ],
            '会員カートなし&ゲストカート商品ありの場合 => 有効な会員カートがない場合はゲストカートを会員カートに変更' => [
                'params' => [
                    'member_id' => 3,
                    'token' => 'test2',
                ],
                'expected' => $this->changeSamples(
                    $samples,
                    [
                        3 => [
                            'token' => '',
                            'member_id' => 3,
                            'items' => [
                                ['guest_item'],
                            ],
                        ],
                    ]
                ),
            ],
            '会員カート商品なし&ゲストカートなしの場合 => 変更なし' => [
                'params' => [
                    'member_id' => 1,
                    'token' => 'test3',
                ],
                'expected' => $samples,
            ],
            '会員カート商品なし&ゲストカート商品なしの場合 => ゲストカートの商品を会員カートに移動させて、ゲストカートは削除' => [
                'params' => [
                    'member_id' => 1,
                    'token' => 'test1',
                ],
                'expected' => $this->remove($samples, 2),
            ],
            '会員カート商品なし&ゲストカート商品ありの場合 => ゲストカートの商品を会員カートに移動させて、ゲストカートは削除' => [
                'params' => [
                    'member_id' => 1,
                    'token' => 'test2',
                ],
                'expected' => $this->changeSamples(
                    $this->remove($samples, 3),
                    [
                        0 => [
                            'token' => '',
                            'member_id' => 1,
                            'items' => [
                                ['guest_item'],
                            ],
                        ],
                    ]
                ),
            ],
            '会員カート商品あり&ゲストカートなしの場合 => 変更なし' => [
                'params' => [
                    'member_id' => 2,
                    'token' => 'test3',
                ],
                'expected' => $samples,
            ],
            '会員カート商品あり&ゲストカート商品なしの場合 => ゲストカートの商品を会員カートに移動させて、ゲストカートは削除' => [
                'params' => [
                    'member_id' => 2,
                    'token' => 'test1',
                ],
                'expected' => $this->changeSamples(
                    $this->remove($samples, 2),
                    [
                        1 => [
                            'token' => '',
                            'member_id' => 2,
                            'items' => [],
                        ],
                    ]
                ),
            ],
            '会員カート商品あり&ゲストカート商品ありの場合 => ゲストカートの商品を会員カートに移動させて、ゲストカートは削除' => [
                'params' => [
                    'member_id' => 2,
                    'token' => 'test2',
                ],
                'expected' => $this->changeSamples(
                    $this->remove($samples, 3),
                    [
                        1 => [
                            'token' => '',
                            'member_id' => 2,
                            'items' => [
                                ['guest_item'],
                            ],
                        ],
                    ]
                ),
            ],
        ];
    }

    /**
     * カート統合処理テスト
     *
     * @param $params
     * @param $expected
     * @dataProvider dataMergeByMemberIdAndToken
     */
    public function testMergeByMemberIdAndToken($params, $expected)
    {
        // 処理実行
        $cartService = resolve(CartService::class);
        $this->doPrivateMethod($cartService, 'mergeByMemberIdAndToken', $params);
        // 検証
        Cart::all()
            ->each(function ($cart, $index) use ($expected) {
                $expect = $expected[$index];
                $this->assertEquals($cart->token, $expect['token']);
                $this->assertEquals($cart->member_id, $expect['member_id']);
                $this->assertEquals($cart->items, $expect['items']);
            });
    }
}
