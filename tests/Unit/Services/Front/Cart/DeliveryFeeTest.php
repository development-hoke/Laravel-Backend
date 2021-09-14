<?php

namespace Tests\Unit\Services\Front\Mypage;

use App\Models\Item;
use App\Models\ItemReserve;
use App\Services\Front\CartService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreateTestData;
use Tests\Concerns\RefreshDatabaseLite;
use Tests\TestCase;

class DeliveryFeeTest extends TestCase
{
    use RefreshDatabaseLite;
    use CreateTestData;

    private $items = [];

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->createApplication();
    }

    protected function truncateData()
    {
        $this->truncateBeforeCreateItem();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\Item::truncate();
        \App\Models\ItemDetail::truncate();
        \App\Models\ItemDetailIdentification::truncate();
        \App\Models\ItemReserve::truncate();
        \App\Models\DeliverySetting::truncate();
        \App\Models\Cart::truncate();
        \App\Models\CartItem::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function dataDeliveryFee()
    {
        $this->truncateData();
        $this->beforeCreateItem();

        $this->items[0] = factory(Item::class)->states('published', 'EC在庫1つ')->create(['retail_price' => 3000, 'discount_rate' => 0]);
        $this->items[1] = factory(Item::class)->states('published', 'EC在庫1つ')->create(['retail_price' => 4100, 'discount_rate' => 0]);
        $this->items[2] = factory(Item::class)->states('published', 'EC在庫1つ')->create(['retail_price' => 10000, 'discount_rate' => 0]);
        $this->items[3] = factory(Item::class)->states('published', '予約在庫1つ')->create(['retail_price' => 15000, 'discount_rate' => 0]);
        $this->items[4] = factory(Item::class)->states('published', '予約在庫1つ')->create(['retail_price' => 15000, 'discount_rate' => 0]);
        $this->items[5] = factory(Item::class)->states('published', '予約在庫1つ')->create(['retail_price' => 3000, 'discount_rate' => 0]);

        ItemReserve::where('item_id', $this->items[3]->id)->update(['reserve_price' => 9000, 'is_free_delivery' => 0]);
        ItemReserve::where('item_id', $this->items[4]->id)->update(['reserve_price' => 10000, 'is_free_delivery' => 0]);
        ItemReserve::where('item_id', $this->items[5]->id)->update(['reserve_price' => 3000, 'is_free_delivery' => 1]);

        return [
            '配送無料条件未達成' => [
                'params' => [
                    'delivery_setting' => [
                        'delivery_condition' => 10000,
                        'delivery_price' => 0,
                    ],
                    'carts' => [
                        [
                            'cart_items' => [
                                [
                                    'item_detail_id' => $this->items[0]->id,
                                    'count' => 1,
                                    'posted_at' => Carbon::now(),
                                ],
                            ],
                            'order_type' => \App\Enums\Cart\Status::Add,
                        ],
                    ],
                ],
                'expected' => config('constants.delivery_fee.default_price'),
            ],
            '配送無料条件以上なので無料' => [
                'params' => [
                    'delivery_setting' => [
                        'delivery_condition' => 3000,
                        'delivery_price' => 0,
                    ],
                    'carts' => [
                        [
                            'cart_items' => [
                                [
                                    'item_detail_id' => $this->items[0]->id,
                                    'count' => 1,
                                    'posted_at' => Carbon::now(),
                                ],
                            ],
                            'order_type' => \App\Enums\Cart\Status::Add,
                        ],
                    ],
                ],
                'expected' => 0,
            ],
            '配送無料条件未達成(商品２つ)' => [
                'params' => [
                    'delivery_setting' => [
                        'delivery_condition' => 10000,
                        'delivery_price' => 0,
                    ],
                    'carts' => [
                        [
                            'cart_items' => [
                                [
                                    'item_detail_id' => $this->items[0]->id,
                                    'count' => 1,
                                    'posted_at' => Carbon::now(),
                                ],
                                [
                                    'item_detail_id' => $this->items[1]->id,
                                    'count' => 1,
                                    'posted_at' => Carbon::now(),
                                ],
                            ],
                            'order_type' => \App\Enums\Cart\Status::Add,
                        ],
                    ],
                ],
                'expected' => config('constants.delivery_fee.default_price'),
            ],
            '配送無料条件以上なので無料(商品２つ)' => [
                'params' => [
                    'delivery_setting' => [
                        'delivery_condition' => 10000,
                        'delivery_price' => 0,
                    ],
                    'carts' => [
                        [
                            'cart_items' => [
                                [
                                    'item_detail_id' => $this->items[0]->id,
                                    'count' => 1,
                                    'posted_at' => Carbon::now(),
                                ],
                                [
                                    'item_detail_id' => $this->items[2]->id,
                                    'count' => 1,
                                    'posted_at' => Carbon::now(),
                                ],
                            ],
                            'order_type' => \App\Enums\Cart\Status::Add,
                        ],
                    ],
                ],
                'expected' => 0,
            ],
            '配送無料条件未達成(同じ商品２つ)' => [
                'params' => [
                    'delivery_setting' => [
                        'delivery_condition' => 10000,
                        'delivery_price' => 0,
                    ],
                    'carts' => [
                        [
                            'cart_items' => [
                                [
                                    'item_detail_id' => $this->items[0]->id,
                                    'count' => 2,
                                    'posted_at' => Carbon::now(),
                                ],
                            ],
                            'order_type' => \App\Enums\Cart\Status::Add,
                        ],
                    ],
                ],
                'expected' => config('constants.delivery_fee.default_price'),
            ],
            '配送無料条件料以上なので無料(同じ商品２つ)' => [
                'params' => [
                    'delivery_setting' => [
                        'delivery_condition' => 5000,
                        'delivery_price' => 0,
                    ],
                    'carts' => [
                        [
                            'cart_items' => [
                                [
                                    'item_detail_id' => $this->items[0]->id,
                                    'count' => 2,
                                    'posted_at' => Carbon::now(),
                                ],
                            ],
                            'order_type' => \App\Enums\Cart\Status::Add,
                        ],
                    ],
                ],
                'expected' => 0,
            ],
            '配送無料条件未達成(予約で送料無料設定無し) reserve_priceの方が反映される' => [
                'params' => [
                    'delivery_setting' => [
                        'delivery_condition' => 10000,
                        'delivery_price' => 0,
                    ],
                    'carts' => [
                        [
                            'cart_items' => [
                                [
                                    'item_detail_id' => $this->items[3]->id,
                                    'count' => 1,
                                    'posted_at' => Carbon::now(),
                                ],
                            ],
                            'order_type' => \App\Enums\Cart\Status::Reserve,
                        ],
                    ],
                ],
                'expected' => config('constants.delivery_fee.default_price'),
            ],
            '配送無料条件料以上なので無料(予約で送料無料設定無し) reserve_priceの方が反映される' => [
                'params' => [
                    'delivery_setting' => [
                        'delivery_condition' => 10000,
                        'delivery_price' => 0,
                    ],
                    'carts' => [
                        [
                            'cart_items' => [
                                [
                                    'item_detail_id' => $this->items[4]->id,
                                    'count' => 1,
                                    'posted_at' => Carbon::now(),
                                ],
                            ],
                            'order_type' => \App\Enums\Cart\Status::Reserve,
                        ],
                    ],
                ],
                'expected' => 0,
            ],
            '予約で送料無料設定' => [
                'params' => [
                    'delivery_setting' => [
                        'delivery_condition' => 10000,
                        'delivery_price' => 0,
                    ],
                    'carts' => [
                        [
                            'cart_items' => [
                                [
                                    'item_detail_id' => $this->items[5]->id,
                                    'count' => 1,
                                    'posted_at' => Carbon::now(),
                                ],
                            ],
                            'order_type' => \App\Enums\Cart\Status::Reserve,
                        ],
                    ],
                ],
                'expected' => 0,
            ],
            'クーポンで送料無料' => [
                'params' => [
                    'delivery_setting' => [
                        'delivery_condition' => 10000,
                        'delivery_price' => 0,
                    ],
                    'carts' => [
                        [
                            'cart_items' => [
                                [
                                    'item_detail_id' => $this->items[0]->id,
                                    'count' => 1,
                                    'posted_at' => Carbon::now(),
                                ],
                            ],
                            'order_type' => \App\Enums\Cart\Status::Add,
                        ],
                    ],
                    'has_free_shipping_coupon' => true,
                ],
                'expected' => 0,
            ],
            'delivery_settingsが未登録' => [
                'params' => [
                    'carts' => [
                        [
                            'cart_items' => [
                                [
                                    'item_detail_id' => $this->items[0]->id,
                                    'count' => 1,
                                    'posted_at' => Carbon::now(),
                                ],
                            ],
                            'order_type' => \App\Enums\Cart\Status::Add,
                        ],
                    ],
                ],
                'expected' => \App\Exceptions\FatalException::class,
            ],
        ];
    }

    /**
     * 配送料計算
     *
     * @param $params
     * @param $expected
     * @dataProvider dataDeliveryFee
     */
    public function testDeliveryFee($params, $expected)
    {
        \App\Models\DeliverySetting::truncate();
        if (isset($params['delivery_setting'])) {
            \App\Models\DeliverySetting::create($params['delivery_setting']);
        } else {
            $this->expectException($expected);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\Cart::truncate();
        \App\Models\CartItem::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        foreach ($params['carts'] as $c) {
            $cart = \App\Models\Cart::create(['order_type' => $c['order_type']]);
            foreach ($c['cart_items'] as $item) {
                \App\Models\CartItem::create($item + ['cart_id' => $cart->id]);
            }
        }

        $cart = \App\Models\Cart::all()->first();
        if (@$params['has_free_shipping_coupon']) {
            // todo: ちゃんとモック作ってやる
            // $mock = \Mockery::mock('overload:App\Domain\Coupon');
            // $mock->shouldReceive('calculateCartCouponDiscount')->once();
            $cart->has_free_shipping_coupon = true;
        }
        // var_dump(\App\Models\ItemReserve::all()->toArray());

        // 処理実行
        $cartService = resolve(CartService::class);
        $return = $cartService->calculatePrices($cart);
        // var_dump($return);
        $this->assertEquals($expected, $return['postage']);
    }
}
