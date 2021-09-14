<?php

namespace Tests\Unit\Domain;

use App\Domain\ItemPrice;
use App\Utils\Arr;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD)
 */
class ItemPriceTest extends TestCase
{
    private $baseItem = [
        'retail_price' => 1000,
        'term_id' => 1,
        'organization_id' => 1,
        'division_id' => 1,
        'department_id' => 1,
        'brand_id' => 1,
        'discount_rate' => 0,
        'is_member_discount' => 0,
        'member_discount_rate' => 0,
    ];

    public function setUp(): void
    {
        parent::setUp();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->truncateTables();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function tearDown(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->truncateTables();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        parent::tearDown();
    }

    public function truncateTables()
    {
        \App\Models\Item::truncate();
        \App\Models\ItemDetail::truncate();
        \App\Models\ItemDetailIdentification::truncate();

        \App\Models\Event::truncate();
        \App\Models\EventItem::truncate();
        \App\Models\EventUser::truncate();
        \App\Models\EventBundleSale::truncate();

        \App\Models\Order::truncate();
        \App\Models\OrderDetail::truncate();
        \App\Models\OrderDetailUnit::truncate();
        \App\Models\OrderLog::truncate();
        \App\Models\OrderDetailLog::truncate();
        \App\Models\OrderDetailUnitLog::truncate();

        \App\Models\Item::truncate();
        \App\Models\ItemReserve::truncate();

        \App\Models\Cart::truncate();
        \App\Models\CartItem::truncate();
    }

    public function provideTestFillDisplayedSalePrice()
    {
        return [
            // イベント
            'イベントセール' => [
                'params' => [
                    'item' => [],
                    'event' => [
                        'period_from' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                        'period_to' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
                        'sale_type' => \App\Enums\Event\SaleType::Normal,
                        'discount_type' => \App\Enums\Event\DiscountType::Flat,
                        'target_user_type' => \App\Enums\Event\TargetUserType::All,
                        'published' => 1,
                        'discount_rate' => 0.1,
                    ],
                    'event_item' => ['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                ],
                'expected' => [
                    'displayed_discount_rate' => 0.1,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::Event,
                    'displayed_sale_price' => 900,
                ],
            ],
            'イベントセール 期間外' => [
                'params' => [
                    'item' => [],
                    'event' => [
                        'period_from' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                        'period_to' => Carbon::now()->subDays(1)->format('Y-m-d H:i:s'),
                        'sale_type' => \App\Enums\Event\SaleType::Normal,
                        'discount_type' => \App\Enums\Event\DiscountType::Flat,
                        'target_user_type' => \App\Enums\Event\TargetUserType::All,
                        'published' => 1,
                        'discount_rate' => 0.1,
                    ],
                    'event_item' => ['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                ],
                'expected' => [
                    'displayed_discount_rate' => 0,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::None,
                    'displayed_sale_price' => 1000,
                ],
            ],
            'イベントセール 異なるsale_type' => [
                'params' => [
                    'item' => [],
                    'event' => [
                        'period_from' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                        'period_to' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
                        'sale_type' => \App\Enums\Event\SaleType::Bundle,
                        'discount_type' => \App\Enums\Event\DiscountType::Flat,
                        'target_user_type' => \App\Enums\Event\TargetUserType::All,
                        'published' => 1,
                        'discount_rate' => 0.1,
                    ],
                    'event_item' => ['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                ],
                'expected' => [
                    'displayed_discount_rate' => 0,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::None,
                    'displayed_sale_price' => 1000,
                ],
            ],
            'イベントセール 商品ごとの割引' => [
                'params' => [
                    'item' => [],
                    'event' => [
                        'period_from' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                        'period_to' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
                        'sale_type' => \App\Enums\Event\SaleType::Normal,
                        'discount_type' => \App\Enums\Event\DiscountType::EachProduct,
                        'target_user_type' => \App\Enums\Event\TargetUserType::All,
                        'published' => 1,
                        'discount_rate' => 0.1,
                    ],
                    'event_item' => ['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                ],
                'expected' => [
                    'displayed_discount_rate' => 0.5,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::Event,
                    'displayed_sale_price' => 500,
                ],
            ],
            'イベントセール 会員のみ（対象外）' => [
                'params' => [
                    'item' => [],
                    'event' => [
                        'period_from' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                        'period_to' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
                        'sale_type' => \App\Enums\Event\SaleType::Normal,
                        'discount_type' => \App\Enums\Event\DiscountType::Flat,
                        'target_user_type' => \App\Enums\Event\TargetUserType::MemberOnly,
                        'published' => 1,
                        'discount_rate' => 0.1,
                    ],
                    'event_item' => ['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                ],
                'expected' => [
                    'displayed_discount_rate' => 0,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::None,
                    'displayed_sale_price' => 1000,
                ],
            ],
            'イベントセール 会員のみ（対象）' => [
                'params' => [
                    'item' => [],
                    'event' => [
                        'period_from' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                        'period_to' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
                        'sale_type' => \App\Enums\Event\SaleType::Normal,
                        'discount_type' => \App\Enums\Event\DiscountType::Flat,
                        'target_user_type' => \App\Enums\Event\TargetUserType::MemberOnly,
                        'published' => 1,
                        'discount_rate' => 0.1,
                    ],
                    'event_item' => ['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'member' => ['id' => 1000],
                ],
                'expected' => [
                    'displayed_discount_rate' => 0.1,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::Event,
                    'displayed_sale_price' => 900,
                ],
            ],
            'イベントセール 特定会員（対象外）' => [
                'params' => [
                    'item' => [],
                    'event' => [
                        'period_from' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                        'period_to' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
                        'sale_type' => \App\Enums\Event\SaleType::Normal,
                        'discount_type' => \App\Enums\Event\DiscountType::Flat,
                        'target_user_type' => \App\Enums\Event\TargetUserType::Limit,
                        'published' => 1,
                        'discount_rate' => 0.1,
                    ],
                    'event_item' => ['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'member' => ['id' => 1000],
                ],
                'expected' => [
                    'displayed_discount_rate' => 0,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::None,
                    'displayed_sale_price' => 1000,
                ],
            ],
            'イベントセール 特定会員（対象外）' => [
                'params' => [
                    'item' => [],
                    'event' => [
                        'period_from' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                        'period_to' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
                        'sale_type' => \App\Enums\Event\SaleType::Normal,
                        'discount_type' => \App\Enums\Event\DiscountType::Flat,
                        'target_user_type' => \App\Enums\Event\TargetUserType::Limit,
                        'published' => 1,
                        'discount_rate' => 0.1,
                    ],
                    'event_item' => ['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'member' => ['id' => 1],
                ],
                'expected' => [
                    'displayed_discount_rate' => 0.1,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::Event,
                    'displayed_sale_price' => 900,
                ],
            ],
            'イベントセール 受注日指定 (対象外)' => [
                'params' => [
                    'item' => [],
                    'event' => [
                        'period_from' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                        'period_to' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
                        'sale_type' => \App\Enums\Event\SaleType::Normal,
                        'discount_type' => \App\Enums\Event\DiscountType::Flat,
                        'target_user_type' => \App\Enums\Event\TargetUserType::All,
                        'published' => 1,
                        'discount_rate' => 0.1,
                    ],
                    'event_item' => ['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_date' => Carbon::now()->subDays(8)->format('Y-m-d H:i:s'),
                ],
                'expected' => [
                    'displayed_discount_rate' => 0,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::None,
                    'displayed_sale_price' => 1000,
                ],
            ],
            'イベントセール 受注日指定 (対象内)' => [
                'params' => [
                    'item' => [],
                    'event' => [
                        'period_from' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                        'period_to' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
                        'sale_type' => \App\Enums\Event\SaleType::Normal,
                        'discount_type' => \App\Enums\Event\DiscountType::Flat,
                        'target_user_type' => \App\Enums\Event\TargetUserType::All,
                        'published' => 1,
                        'discount_rate' => 0.1,
                    ],
                    'event_item' => ['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_date' => Carbon::now()->subDays(7)->addMinutes(1)->format('Y-m-d H:i:s'),
                ],
                'expected' => [
                    'displayed_discount_rate' => 0.1,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::Event,
                    'displayed_sale_price' => 900,
                ],
            ],

            // 通常割引
            '通常割引' => [
                'params' => [
                    'item' => ['discount_rate' => 0.2],
                    'member' => ['id' => 1],
                ],
                'expected' => [
                    'displayed_discount_rate' => 0.2,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::Normal,
                    'displayed_sale_price' => 800,
                ],
            ],
            '会員割引' => [
                'params' => [
                    'item' => ['discount_rate' => 0.2, 'is_member_discount' => 1, 'member_discount_rate' => 0.4],
                    'member' => ['id' => 1],
                ],
                'expected' => [
                    'displayed_discount_rate' => 0.4,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::Member,
                    'displayed_sale_price' => 600,
                ],
            ],
            '通常割引 (会員割引フラグ0)' => [
                'params' => [
                    'item' => ['discount_rate' => 0.2, 'is_member_discount' => 0, 'member_discount_rate' => 0.4],
                    'member' => ['id' => 1],
                ],
                'expected' => [
                    'displayed_discount_rate' => 0.2,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::Normal,
                    'displayed_sale_price' => 800,
                ],
            ],
            '通常割引 (会員割引率 < 通常割引率)' => [
                'params' => [
                    'item' => ['discount_rate' => 0.2, 'is_member_discount' => 1, 'member_discount_rate' => 0.1],
                    'member' => ['id' => 1],
                ],
                'expected' => [
                    'displayed_discount_rate' => 0.1,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::Member,
                    'displayed_sale_price' => 900,
                ],
            ],

            '社員割引 他社ブランド' => [
                'params' => [
                    'item' => ['discount_rate' => 0.2, 'maker_product_number' => '0080-1000'],
                    'member' => ['id' => 1, 'staff_code' => 10000],
                ],
                'expected' => [
                    'displayed_discount_rate' => 0.3,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::Staff,
                    'displayed_sale_price' => 700,
                ],
            ],
            '社員割引 自社ブランド' => [
                'params' => [
                    'item' => ['discount_rate' => 0.2, 'maker_product_number' => '0088-1000'],
                    'member' => ['id' => 1, 'staff_code' => 10000],
                ],
                'expected' => [
                    'displayed_discount_rate' => 0.5,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::Staff,
                    'displayed_sale_price' => 500,
                ],
            ],
            '社員割引 他の割引が社割りを上回る' => [
                'params' => [
                    'item' => ['discount_rate' => 0.4, 'maker_product_number' => '0080-1000'],
                    'member' => ['id' => 1, 'staff_code' => 10000],
                ],
                'expected' => [
                    'displayed_discount_rate' => 0.4,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::Normal,
                    'displayed_sale_price' => 600,
                ],
            ],

            '予約販売' => [
                'params' => [
                    'item' => ['retail_price' => 1000],
                    'item_reserve' => ['reserve_price' => 900],
                ],
                'expected' => [
                    'displayed_discount_rate' => null,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::Reservation,
                    'displayed_sale_price' => 900,
                    'displayed_discount_price' => 100,
                ],
            ],
        ];
    }

    /**
     * @param array $params
     * @param array $expected
     *
     * @return void
     *
     * @dataProvider provideTestFillDisplayedSalePrice
     */
    public function testFillDisplayedSalePrice($params, $expected)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $item = factory(\App\Models\Item::class)->create(array_merge($this->baseItem, $params['item'] ?? []));

        if (isset($params['event'])) {
            factory(\App\Models\Event::class)->create($params['event']);
        }
        if (isset($params['event_item'])) {
            \App\Models\EventItem::create($params['event_item']);
        }
        if (isset($params['event_user'])) {
            \App\Models\EventUser::create($params['event_user']);
        }
        if (isset($params['item_reserve'])) {
            \App\Models\ItemReserve::create(array_merge([
                'item_id' => $item->id,
                'is_enable' => true,
                'period_from' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                'period_to' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
                'reserve_price' => 1000,
                'is_free_delivery' => true,
                'limited_stock_threshold' => 1,
                'out_of_stock_threshold' => 1,
                'expected_arrival_date' => 'aaaa',
                'note' => 'aaaa',
            ], $params['item_reserve']));
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $member = $params['member'] ?? null;
        $orderedDate = $params['ordered_date'] ?? null;

        $item = \App\Models\Item::find(1);
        $service = new ItemPrice();
        $item = $service->fillDisplayedSalePrice($item, $member, $orderedDate);

        $this->assertEquals($expected['displayed_discount_rate'], $item->displayed_discount_rate);
        $this->assertEquals($expected['displayed_discount_type'], $item->displayed_discount_type);
        $this->assertEquals($expected['displayed_discount_price'] ?? null, $item->displayed_discount_price);
        $this->assertEquals($expected['displayed_sale_price'], $item->displayed_sale_price);
    }

    public function provideTestFillDisplayedSalePriceMultipleEvents()
    {
        return [
            'イベントセール' => [
                'params' => [
                    'item' => [],
                    'events' => [
                        [
                            'event' => [
                                'period_from' => Carbon::now()->subDays(6)->format('Y-m-d H:i:s'),
                                'discount_rate' => 0.3,
                            ],
                            'event_item' => ['item_id' => 1, 'discount_rate' => 0.5],
                            'event_user' => ['member_id' => 1],
                        ],
                        [
                            'event' => [
                                'period_from' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                                'discount_rate' => 0.2,
                            ],
                            'event_item' => ['item_id' => 1, 'discount_rate' => 0.5],
                            'event_user' => ['member_id' => 1],
                        ],
                        [
                            'event' => [
                                'period_from' => Carbon::now()->subDays(5)->format('Y-m-d H:i:s'),
                                'discount_rate' => 0.1,
                            ],
                            'event_item' => ['item_id' => 1, 'discount_rate' => 0.5],
                            'event_user' => ['member_id' => 1],
                        ],
                    ],
                ],
                'expected' => [
                    'displayed_discount_rate' => 0.2,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::Event,
                    'displayed_sale_price' => 800,
                ],
            ],
        ];
    }

    /**
     * @param array $params
     * @param array $expected
     *
     * @return void
     *
     * @dataProvider provideTestFillDisplayedSalePriceMultipleEvents
     */
    public function testFillDisplayedSalePriceMultipleEvents($params, $expected)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        factory(\App\Models\Item::class)->create(array_merge($this->baseItem, $params['item'] ?? []));
        foreach ($params['events'] as $p) {
            $event = factory(\App\Models\Event::class)->create(array_merge([
                'period_from' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                'period_to' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
                'sale_type' => \App\Enums\Event\SaleType::Normal,
                'discount_type' => \App\Enums\Event\DiscountType::Flat,
                'target_user_type' => \App\Enums\Event\TargetUserType::All,
                'published' => 1,
                'discount_rate' => 0.1,
            ], $p['event']));
            \App\Models\EventItem::create(array_merge($p['event_item'], ['event_id' => $event->id]));
            \App\Models\EventUser::create(array_merge($p['event_user'], ['event_id' => $event->id]));
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $item = \App\Models\Item::find(1);
        $service = new ItemPrice();
        $item = $service->fillDisplayedSalePrice($item);

        $this->assertEquals($expected['displayed_discount_rate'], $item->displayed_discount_rate);
        $this->assertEquals($expected['displayed_discount_type'], $item->displayed_discount_type);
        $this->assertEquals($expected['displayed_sale_price'], $item->displayed_sale_price);
    }

    public function provideTestFillPriceBeforeOrderAfterOrdered()
    {
        return [
            'バンドル販売 個数3' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'event_items' => [['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5]],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_items' => [['id' => 1, 'amount' => 3]],
                    'member' => ['id' => 1000],
                ],
                'expected' => [
                    'bundle_discount_rate' => 0.2,
                    'bundle_sale_price' => 720,
                    'price_before_order' => 720,
                ],
            ],
            'バンドル販売 個数6' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'event_items' => [['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5]],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_items' => [['id' => 1, 'amount' => 6]],
                    'member' => ['id' => 1000],
                ],
                'expected' => [
                    'bundle_discount_rate' => 0.4,
                    'bundle_sale_price' => 540,
                    'price_before_order' => 540,
                ],
            ],
            'バンドル販売 個数2 (対象外)' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'event_items' => [['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5]],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_items' => [['id' => 1, 'amount' => 2]],
                    'member' => ['id' => 1000],
                ],
                'expected' => [
                    'bundle_discount_rate' => null,
                    'bundle_sale_price' => null,
                    'price_before_order' => 900,
                ],
            ],
            'バンドル販売 複数の対象商品' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'event_items' => [
                        ['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5],
                        ['event_id' => 1, 'item_id' => 2, 'discount_rate' => 0.5],
                    ],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_items' => [['id' => 1, 'amount' => 1], ['id' => 1, 'amount' => 2]],
                    'member' => ['id' => 1000],
                ],
                'expected' => [
                    'bundle_discount_rate' => 0.2,
                    'bundle_sale_price' => 720,
                    'price_before_order' => 720,
                ],
            ],
            'バンドル販売 複数の対象商品 (対象外)' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'event_items' => [
                        ['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5],
                        ['event_id' => 1, 'item_id' => 2, 'discount_rate' => 0.5],
                    ],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_items' => [['id' => 1, 'amount' => 1], ['id' => 1, 'amount' => 1]],
                    'member' => ['id' => 1000],
                ],
                'expected' => [
                    'bundle_discount_rate' => null,
                    'bundle_sale_price' => null,
                    'price_before_order' => 900,
                ],
            ],

            // 共通の設定項目のチェック
            'バンドル販売 期間外' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'event' => [
                        'period_from' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                        'period_to' => Carbon::now()->subDays(1)->format('Y-m-d H:i:s'),
                    ],
                    'event_items' => [['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5]],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_items' => [['id' => 1, 'amount' => 3]],
                    'member' => ['id' => 1000],
                ],
                'expected' => [
                    'bundle_discount_rate' => null,
                    'bundle_sale_price' => null,
                    'price_before_order' => 900,
                ],
            ],
            // 'バンドル販売 会員のみ（対象外）' => [
            //     'params' => [
            //         'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
            //         'event' => [
            //             'target_user_type' => \App\Enums\Event\TargetUserType::MemberOnly,
            //         ],
            //         'event_items' => [['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5]],
            //         'event_user' => ['event_id' => 1, 'member_id' => 1],
            //         'ordered_items' => [['id' => 1, 'amount' => 3]],
            //     ],
            //     'expected' => [
            //         'bundle_discount_rate' => null,
            //         'bundle_sale_price' => null,
            //         'price_before_order' => 900,
            //     ],
            // ],
            'バンドル販売 会員のみ（対象）' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'event' => [
                        'target_user_type' => \App\Enums\Event\TargetUserType::MemberOnly,
                    ],
                    'event_items' => [['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5]],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_items' => [['id' => 1, 'amount' => 3]],
                    'member' => ['id' => 1000],
                ],
                'expected' => [
                    'bundle_discount_rate' => 0.2,
                    'bundle_sale_price' => 720,
                    'price_before_order' => 720,
                ],
            ],
            'バンドル販売 特定会員（対象外）' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'event' => [
                        'target_user_type' => \App\Enums\Event\TargetUserType::Limit,
                    ],
                    'event_items' => [['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5]],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_items' => [['id' => 1, 'amount' => 3]],
                    'member' => ['id' => 1000],
                ],
                'expected' => [
                    'bundle_discount_rate' => null,
                    'bundle_sale_price' => null,
                    'price_before_order' => 900,
                ],
            ],
            'バンドル販売 特定会員（対象）' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'event' => [
                        'target_user_type' => \App\Enums\Event\TargetUserType::Limit,
                    ],
                    'event_items' => [['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5]],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_items' => [['id' => 1, 'amount' => 3]],
                    'member' => ['id' => 1],
                ],
                'expected' => [
                    'bundle_discount_rate' => 0.2,
                    'bundle_sale_price' => 720,
                    'price_before_order' => 720,
                ],
            ],
        ];
    }

    /**
     * @param array $params
     * @param array $expected
     *
     * @return void
     *
     * @dataProvider provideTestFillPriceBeforeOrderAfterOrdered
     */
    public function testFillPriceBeforeOrderAfterOrdered($params, $expected)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $item = factory(\App\Models\Item::class)->create(array_merge($this->baseItem, $params['item'] ?? []));
        factory(\App\Models\Event::class)->create(array_merge([
            'period_from' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
            'period_to' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
            'sale_type' => \App\Enums\Event\SaleType::Bundle,
            'discount_type' => \App\Enums\Event\DiscountType::Flat,
            'target_user_type' => \App\Enums\Event\TargetUserType::All,
            'published' => 1,
            'discount_rate' => 0.1,
        ], $params['event'] ?? []));

        if (isset($params['event_items'])) {
            foreach ($params['event_items'] as $p) {
                \App\Models\EventItem::create($p);
            }
        }
        if (isset($params['event_user'])) {
            \App\Models\EventUser::create($params['event_user']);
        }
        \App\Models\EventBundleSale::create(['event_id' => 1, 'count' => 4, 'rate' => 0.2]);
        \App\Models\EventBundleSale::create(['event_id' => 1, 'count' => 7, 'rate' => 0.4]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $order = new \App\Models\Order(['order_date' => Carbon::now()->format('Y-m-d H:i:s')]);
        $orderDetails = (new \App\Models\OrderDetail())->newCollection(Arr::map($params['ordered_items'], function ($orderedItem) use ($item) {
            $itemDetail = new \App\Models\ItemDetail();
            $itemDetail->id = $orderedItem['id'];
            $itemDetail->item_id = $item->id;

            $unit = new \App\Models\OrderDetailUnit(['amount' => $orderedItem['amount']]);

            $orderDetail = (new \App\Models\OrderDetail(['item_detail_id' => $orderedItem['id']]))
                ->setRelation('orderDetailUnits', $unit->newCollection([$unit]))
                ->setRelation('itemDetail', $itemDetail);

            return $orderDetail;
        }));
        $order->setRelation('orderDetails', $orderDetails);

        $member = $params['member'];
        $item = \App\Models\Item::find(1);
        $service = new ItemPrice();
        $item = $service->fillPriceBeforeOrderAfterOrdered($item, $order, $member, 1);

        $this->assertEquals($expected['bundle_discount_rate'], $item->bundle_discount_rate);
        $this->assertEquals($expected['bundle_sale_price'], $item->bundle_sale_price);
        $this->assertEquals($expected['price_before_order'], $item->price_before_order);
    }

    public function provideTestFillPriceBeforeOrderAfterOrderedMultipleEvents()
    {
        return [
            'バンドル販売 個数3' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'events' => [
                        [
                            'event' => [
                                'period_from' => Carbon::now()->subDays(6)->format('Y-m-d H:i:s'),
                            ],
                            'event_item' => ['item_id' => 1, 'discount_rate' => 0.5],
                            'event_user' => ['member_id' => 1],
                            'event_bundle_sale_1' => ['rate' => 0.3],
                            'event_bundle_sale_2' => ['rate' => 0.6],
                        ],
                        [
                            'event' => [
                                'period_from' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                            'event_item' => ['item_id' => 1, 'discount_rate' => 0.5],
                            'event_user' => ['member_id' => 1],
                            'event_bundle_sale_1' => ['rate' => 0.2],
                            'event_bundle_sale_2' => ['rate' => 0.4],
                        ],
                        [
                            'event' => [
                                'period_from' => Carbon::now()->subDays(5)->format('Y-m-d H:i:s'),
                            ],
                            'event_item' => ['item_id' => 1, 'discount_rate' => 0.5],
                            'event_user' => ['member_id' => 1],
                            'event_bundle_sale_1' => ['rate' => 0.5],
                            'event_bundle_sale_2' => ['rate' => 0.7],
                        ],
                    ],
                    'ordered_items' => [['id' => 1, 'amount' => 3]],
                    'member' => ['id' => 1000],
                ],
                'expected' => [
                    'bundle_discount_rate' => 0.2,
                    'bundle_sale_price' => 720,
                    'price_before_order' => 720,
                ],
            ],
        ];
    }

    /**
     * @param array $params
     * @param array $expected
     *
     * @return void
     *
     * @dataProvider provideTestFillPriceBeforeOrderAfterOrderedMultipleEvents
     */
    public function testFillPriceBeforeOrderAfterOrderedMultipleEvents($params, $expected)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $item = factory(\App\Models\Item::class)->create(array_merge($this->baseItem, $params['item'] ?? []));
        foreach ($params['events'] as $p) {
            $event = factory(\App\Models\Event::class)->create(array_merge([
                'period_from' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                'period_to' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
                'sale_type' => \App\Enums\Event\SaleType::Bundle,
                'discount_type' => \App\Enums\Event\DiscountType::Flat,
                'target_user_type' => \App\Enums\Event\TargetUserType::All,
                'published' => 1,
                'discount_rate' => 0.1,
            ], $p['event']));
            \App\Models\EventItem::create(array_merge($p['event_item'], ['event_id' => $event->id]));
            \App\Models\EventUser::create(array_merge($p['event_user'], ['event_id' => $event->id]));
            \App\Models\EventBundleSale::create(array_merge(['event_id' => $event->id, 'count' => 4, 'rate' => 0.2], $p['event_bundle_sale_1']));
            \App\Models\EventBundleSale::create(array_merge(['event_id' => $event->id, 'count' => 7, 'rate' => 0.4], $p['event_bundle_sale_2']));
        }
        $order = new \App\Models\Order(['order_date' => Carbon::now()->format('Y-m-d H:i:s')]);
        $orderDetails = (new \App\Models\OrderDetail())->newCollection(Arr::map($params['ordered_items'], function ($orderedItem) use ($item) {
            $itemDetail = new \App\Models\ItemDetail();
            $itemDetail->id = $orderedItem['id'];
            $itemDetail->item_id = $item->id;

            $unit = new \App\Models\OrderDetailUnit(['amount' => $orderedItem['amount']]);

            $orderDetail = (new \App\Models\OrderDetail(['item_detail_id' => $orderedItem['id']]))
                ->setRelation('orderDetailUnits', $unit->newCollection([$unit]))
                ->setRelation('itemDetail', $itemDetail);

            return $orderDetail;
        }));
        $order->setRelation('orderDetails', $orderDetails);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $member = $params['member'];
        $item = \App\Models\Item::find(1);
        $service = new ItemPrice();
        $item = $service->fillPriceBeforeOrderAfterOrdered($item, $order, $member, 1);

        $this->assertEquals($expected['bundle_discount_rate'], $item->bundle_discount_rate);
        $this->assertEquals($expected['bundle_sale_price'], $item->bundle_sale_price);
        $this->assertEquals($expected['price_before_order'], $item->price_before_order);
    }

    public function provideTestFillPriceBeforeOrderToMakeNewOrder()
    {
        return [
            'バンドル販売 個数3' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'event_items' => [['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5]],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_items' => [['id' => 1, 'amount' => 3]],
                ],
                'expected' => [
                    'bundle_discount_rate' => 0.2,
                    'bundle_sale_price' => 720,
                    'price_before_order' => 720,
                ],
            ],
            'バンドル販売 個数6' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'event_items' => [['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5]],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_items' => [['id' => 1, 'amount' => 6]],
                ],
                'expected' => [
                    'bundle_discount_rate' => 0.4,
                    'bundle_sale_price' => 540,
                    'price_before_order' => 540,
                ],
            ],
            'バンドル販売 個数2 (対象外)' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'event_items' => [['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5]],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_items' => [['id' => 1, 'amount' => 2]],
                ],
                'expected' => [
                    'bundle_discount_rate' => null,
                    'bundle_sale_price' => null,
                    'price_before_order' => 900,
                ],
            ],
            'バンドル販売 複数の対象商品' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'event_items' => [
                        ['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5],
                        ['event_id' => 1, 'item_id' => 2, 'discount_rate' => 0.5],
                    ],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_items' => [['id' => 1, 'amount' => 1], ['id' => 1, 'amount' => 2]],
                ],
                'expected' => [
                    'bundle_discount_rate' => 0.2,
                    'bundle_sale_price' => 720,
                    'price_before_order' => 720,
                ],
            ],
            'バンドル販売 複数の対象商品 (対象外)' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'event_items' => [
                        ['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5],
                        ['event_id' => 1, 'item_id' => 2, 'discount_rate' => 0.5],
                    ],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_items' => [['id' => 1, 'amount' => 1], ['id' => 1, 'amount' => 1]],
                ],
                'expected' => [
                    'bundle_discount_rate' => null,
                    'bundle_sale_price' => null,
                    'price_before_order' => 900,
                ],
            ],

            // 共通の設定項目のチェック
            'バンドル販売 期間外' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'event' => [
                        'period_from' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                        'period_to' => Carbon::now()->subDays(1)->format('Y-m-d H:i:s'),
                    ],
                    'event_items' => [['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5]],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_items' => [['id' => 1, 'amount' => 3]],
                ],
                'expected' => [
                    'bundle_discount_rate' => null,
                    'bundle_sale_price' => null,
                    'price_before_order' => 900,
                ],
            ],
            'バンドル販売 会員のみ（対象外）' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'event' => [
                        'target_user_type' => \App\Enums\Event\TargetUserType::MemberOnly,
                    ],
                    'event_items' => [['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5]],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_items' => [['id' => 1, 'amount' => 3]],
                ],
                'expected' => [
                    'bundle_discount_rate' => null,
                    'bundle_sale_price' => null,
                    'price_before_order' => 900,
                ],
            ],
            'バンドル販売 会員のみ（対象）' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'event' => [
                        'target_user_type' => \App\Enums\Event\TargetUserType::MemberOnly,
                    ],
                    'event_items' => [['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5]],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_items' => [['id' => 1, 'amount' => 3]],
                    'member' => ['id' => 1000],
                ],
                'expected' => [
                    'bundle_discount_rate' => 0.2,
                    'bundle_sale_price' => 720,
                    'price_before_order' => 720,
                ],
            ],
            'バンドル販売 特定会員（対象外）' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'event' => [
                        'target_user_type' => \App\Enums\Event\TargetUserType::Limit,
                    ],
                    'event_items' => [['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5]],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_items' => [['id' => 1, 'amount' => 3]],
                    'member' => ['id' => 1000],
                ],
                'expected' => [
                    'bundle_discount_rate' => null,
                    'bundle_sale_price' => null,
                    'price_before_order' => 900,
                ],
            ],
            'バンドル販売 特定会員（対象）' => [
                'params' => [
                    'item' => ['discount_rate' => 0.1, 'retail_price' => 1000],
                    'event' => [
                        'target_user_type' => \App\Enums\Event\TargetUserType::Limit,
                    ],
                    'event_items' => [['event_id' => 1, 'item_id' => 1, 'discount_rate' => 0.5]],
                    'event_user' => ['event_id' => 1, 'member_id' => 1],
                    'ordered_items' => [['id' => 1, 'amount' => 3]],
                    'member' => ['id' => 1],
                ],
                'expected' => [
                    'bundle_discount_rate' => 0.2,
                    'bundle_sale_price' => 720,
                    'price_before_order' => 720,
                ],
            ],
        ];
    }

    /**
     * @param array $params
     * @param array $expected
     *
     * @return void
     *
     * @dataProvider provideTestFillPriceBeforeOrderToMakeNewOrder
     */
    public function testFillPriceBeforeOrderToMakeNewOrder($params, $expected)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $item = factory(\App\Models\Item::class)->create(array_merge($this->baseItem, $params['item'] ?? []));
        factory(\App\Models\Event::class)->create(array_merge([
            'period_from' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
            'period_to' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
            'sale_type' => \App\Enums\Event\SaleType::Bundle,
            'discount_type' => \App\Enums\Event\DiscountType::Flat,
            'target_user_type' => \App\Enums\Event\TargetUserType::All,
            'published' => 1,
            'discount_rate' => 0.1,
        ], $params['event'] ?? []));

        if (isset($params['event_items'])) {
            foreach ($params['event_items'] as $p) {
                \App\Models\EventItem::create($p);
            }
        }
        if (isset($params['event_user'])) {
            \App\Models\EventUser::create($params['event_user']);
        }
        \App\Models\EventBundleSale::create(['event_id' => 1, 'count' => 3, 'rate' => 0.2]);
        \App\Models\EventBundleSale::create(['event_id' => 1, 'count' => 6, 'rate' => 0.4]);

        $cart = \App\Models\Cart::create([
            'member_id' => $params['member']['id'] ?? null,
            'use_coupon_ids' => '[]',
            'order_type' => 1,
        ]);
        $cart->member = $params['member'] ?? null;

        collect($params['ordered_items'])->each(function ($orderingItem) use ($item, $cart) {
            $itemDetail = \App\Models\ItemDetail::find($orderingItem['id']);

            if (empty($itemDetail)) {
                $itemDetail = factory(\App\Models\ItemDetail::class)->create([
                    'item_id' => $item->id,
                    'color_id' => 1,
                    'size_id' => 1,
                ]);
                $itemDetail->id = $orderingItem['id'];
                $itemDetail->save();
            }

            \App\Models\CartItem::create([
                'cart_id' => $cart->id,
                'count' => $orderingItem['amount'],
                'item_detail_id' => $itemDetail->id,
                'closed_market_id' => 0,
                'posted_at' => Carbon::now(),
            ]);
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $service = new ItemPrice();
        $cart = $service->fillPriceBeforeOrderToCreateNewOrder($cart);
        $item = $cart->cartItems->first()->itemDetail->item;

        $this->assertEquals($expected['bundle_discount_rate'], $item->bundle_discount_rate);
        $this->assertEquals($expected['bundle_sale_price'], $item->bundle_sale_price);
        $this->assertEquals($expected['price_before_order'], $item->price_before_order);
    }

    private function createItem($params = [])
    {
        $baseParams = [
            'retail_price' => 1000,
            'term_id' => 1,
            'organization_id' => 1,
            'division_id' => 1,
            'department_id' => 1,
            'brand_id' => 1,
            'discount_rate' => 0,
            'is_member_discount' => 0,
            'member_discount_rate' => 0,
            'status' => 1,
            'sales_status' => 1,
        ];

        return factory(\App\Models\Item::class)->create(array_merge($baseParams, $params));
    }

    private function createEvent($params = [])
    {
        $event = \App\Models\Event::create(array_merge([
            'title' => 'aaaaaaaaaaa',
            'target' => \App\Enums\Event\Target::Sale,
            'period_from' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
            'period_to' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
            'sale_type' => \App\Enums\Event\SaleType::Normal,
            'discount_type' => \App\Enums\Event\DiscountType::Flat,
            'target_user_type' => \App\Enums\Event\TargetUserType::All,
            'published' => 1,
            'discount_rate' => 0.4,
            'is_delivery_setting' => 0,
        ], $params['event'] ?? []));

        foreach ($params['event_items'] as $setting) {
            \App\Models\EventItem::create(array_merge(
                ['event_id' => $event->id, 'discount_rate' => 0],
                $setting
            ));
        }

        if (isset($params['event_user'])) {
            \App\Models\EventUser::create(array_merge(
                ['event_id' => $event->id],
                $params['event_user']
            ));
        }

        if (isset($params['bundle'])) {
            foreach ($params['bundle'] as $setting) {
                \App\Models\EventBundleSale::create(array_merge(
                    ['event_id' => $event->id],
                    $setting
                ));
            }
        }

        return $event;
    }

    /**
     * @param int $itemId
     * @param array $params
     *
     * @return void
     */
    private function createItemReserve($itemId, array $params = [])
    {
        return \App\Models\ItemReserve::create(array_merge([
            'item_id' => $itemId,
            'is_enable' => true,
            'period_from' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
            'period_to' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
            'reserve_price' => 1000,
            'is_free_delivery' => true,
            'limited_stock_threshold' => 1,
            'out_of_stock_threshold' => 1,
            'expected_arrival_date' => 'aaaa',
            'note' => 'aaaa',
        ], $params));
    }

    public function testGetMemberSearchScopeQueryWithOrder()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $order = factory(\App\Models\Order::class)->create(['order_date' => Carbon::now()->format('Y-m-d H:i:s')]);

        // 1 => rate: 0.0, price: 1000, type: 割引なし
        $this->createItem();

        // 2 => rate: 0.2, price: 800, type: 通常割引
        $this->createItem(['discount_rate' => 0.2]);

        // 3 => rate: 0.3, price: 700, type: 会員割引
        $this->createItem(['discount_rate' => 0.2, 'is_member_discount' => 1, 'member_discount_rate' => 0.3]);

        // 4 => rate: 0.2, price: 800, type: 通常割引
        $this->createItem(['discount_rate' => 0.2, 'is_member_discount' => 0, 'member_discount_rate' => 0.3]);

        // 5 => rate: 0.1, price: 900, type: 会員割引
        $this->createItem(['discount_rate' => 0.2, 'is_member_discount' => 1, 'member_discount_rate' => 0.1]);

        // 6 => rate: 0.4, price: 600, type: イベント
        $this->createItem(['discount_rate' => 0.2]);
        $this->createEvent(['event_items' => [['item_id' => 6]]]);

        // 7 => rate: 0, price: 1000, type: 割引なし (イベント 期間外)
        $this->createItem();
        $this->createEvent([
            'event_items' => [['item_id' => 7]],
            'event' => [
                'period_to' => Carbon::now()->subDays(8)->format('Y-m-d H:i:s'),
                'period_from' => Carbon::now()->subDays(1)->format('Y-m-d H:i:s'),
            ],
        ]);

        // 8 => rate: 0, price: 1000, type: 割引なし (イベント 異なるsale_type)
        $this->createItem();
        $this->createEvent([
            'event_items' => [['item_id' => 8]],
            'event' => ['sale_type' => \App\Enums\Event\SaleType::Bundle],
            'bundle' => [['count' => 1000, 'rate' => 0.1]],
        ]);

        // 9 => rate: 0.5, price: 500, type: 割引なし (イベント 商品ごとに割引率を設定)
        $this->createItem();
        $this->createEvent([
            'event_items' => [['item_id' => 9, 'discount_rate' => 0.5]],
            'event' => ['discount_type' => \App\Enums\Event\DiscountType::EachProduct],
        ]);

        // 10 => rate: 0, price: 1000, type: イベント 特定会員（対象外）
        $this->createItem();
        $this->createEvent([
            'event_items' => [['item_id' => 10]],
            'event_user' => ['item_id' => 10, 'member_id' => 5000],
            'event' => ['target_user_type' => \App\Enums\Event\TargetUserType::Limit],
        ]);

        // 11 => rate: 0.4, price: 600, type: イベント 特定会員（対象）
        $this->createItem();
        $this->createEvent([
            'event_items' => [['item_id' => 11]],
            'event_user' => ['item_id' => 11, 'member_id' => 1],
            'event' => ['target_user_type' => \App\Enums\Event\TargetUserType::Limit],
        ]);

        // 12 => rate: 0.4, price: 600, type: イベント 会員のみ
        $this->createItem();
        $this->createEvent([
            'event_items' => [['item_id' => 12]],
            'event_user' => ['item_id' => 12, 'member_id' => 1],
            'event' => ['target_user_type' => \App\Enums\Event\TargetUserType::MemberOnly],
        ]);

        // 13 => rate: 0.3, price: 700, type: 社割り (他社ブランド)
        $this->createItem(['discount_rate' => 0.2, 'maker_product_number' => '0080-1000']);

        // 14 => rate: 0.5, price: 500, type: 社割り (自社ブランド)
        $this->createItem(['discount_rate' => 0.2, 'maker_product_number' => '0088-1000']);

        // 15 => rate: 0.4, price: 600, type: 社割り (他の割引が社割りを上回る)
        $this->createItem(['discount_rate' => 0.4, 'maker_product_number' => '0081-1000']);

        // 16 => discount_price: 100, discounted_price: 1000, type: 予約販売
        $item = $this->createItem();
        $this->createItemReserve($item->id, ['reserve_price' => 900]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $member = ['id' => 1];

        $service = new ItemPrice();
        $query = $service->getMemberSearchScopeQuery($member, $order);
        $repository = resolve(\App\Repositories\ItemRepositoryEloquent::class);
        $repository->scopeQuery($query);
        $items = Arr::dict($repository->all(), 'id');

        $this->assertEquals(1000, $items[1]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::None, $items[1]->displayed_discount_type);

        $this->assertEquals(800, $items[2]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Normal, $items[2]->displayed_discount_type);

        $this->assertEquals(700, $items[3]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Member, $items[3]->displayed_discount_type);

        $this->assertEquals(800, $items[4]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Normal, $items[4]->displayed_discount_type);

        $this->assertEquals(900, $items[5]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Member, $items[5]->displayed_discount_type);

        $this->assertEquals(600, $items[6]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Event, $items[6]->displayed_discount_type);

        $this->assertEquals(1000, $items[7]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::None, $items[7]->displayed_discount_type);

        $this->assertEquals(1000, $items[8]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::None, $items[8]->displayed_discount_type);

        $this->assertEquals(500, $items[9]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Event, $items[9]->displayed_discount_type);

        $this->assertEquals(1000, $items[10]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::None, $items[10]->displayed_discount_type);

        $this->assertEquals(600, $items[11]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Event, $items[11]->displayed_discount_type);

        $this->assertEquals(600, $items[12]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Event, $items[12]->displayed_discount_type);

        $this->assertEquals(800, $items[13]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Normal, $items[13]->displayed_discount_type);

        $this->assertEquals(800, $items[14]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Normal, $items[14]->displayed_discount_type);

        $this->assertEquals(600, $items[15]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Normal, $items[15]->displayed_discount_type);

        $this->assertEquals(900, $items[16]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Reservation, $items[16]->displayed_discount_type);
    }

    public function testGetMemberSearchScopeQueryWithOrderStaffAccount()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $order = factory(\App\Models\Order::class)->create(['order_date' => Carbon::now()->format('Y-m-d H:i:s')]);

        // 1 => rate: 0.3, price: 700, type: 社割り (他社ブランド)
        $this->createItem(['product_number' => 1, 'maker_product_number' => '0080-1001']);

        // 2 => rate: 0.3, price: 700, type: 社割り (他社ブランド)
        $this->createItem(['product_number' => 2, 'maker_product_number' => '0080-1002', 'discount_rate' => 0.1]);

        // 3 => rate: 0.3, price: 700, type: 社割り (他社ブランド)
        $this->createItem(['product_number' => 3, 'maker_product_number' => '0080-1003', 'is_member_discount' => 1, 'member_discount_rate' => 0.2]);

        // 4 => rate: 0.3, price: 700, type: 社割り (他社ブランド)
        $this->createItem(['product_number' => 4, 'maker_product_number' => '0080-1004']);
        $this->createEvent(['event_items' => [['item_id' => 6]], 'event' => ['discount_rate' => 0.2]]);

        // 5 => rate: 0.3, price: 700, type: 社割り (他社ブランド)
        $this->createItem(['product_number' => 5, 'discount_rate' => 0.2, 'maker_product_number' => '0080-1005']);

        // 6 => rate: 0.5, price: 500, type: 社割り (自社ブランド)
        $this->createItem(['product_number' => 6, 'discount_rate' => 0.2, 'maker_product_number' => '0088-1000']);

        // 7 => rate: 0.4, price: 600, type: 社割り (他の割引が社割りを上回る)
        $this->createItem(['product_number' => 7, 'discount_rate' => 0.4, 'maker_product_number' => '0081-1006']);

        // 8 => discount_price: 400, discounted_price: 600, type: 予約販売
        $item = $this->createItem(['product_number' => 8, 'maker_product_number' => '0081-1007']);
        $this->createItemReserve($item->id, ['reserve_price' => 600]);

        // 9 => discount_price: 300, discounted_price: 700, type: 社員割引
        $item = $this->createItem(['product_number' => 9, 'maker_product_number' => '0081-1009']);
        $this->createItemReserve($item->id, ['reserve_price' => 800]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $member = ['id' => 1, 'staff_code' => 1111];
        $service = new ItemPrice();
        $query = $service->getMemberSearchScopeQuery($member, $order);
        $repository = resolve(\App\Repositories\ItemRepositoryEloquent::class);
        $repository->scopeQuery($query);
        $items = Arr::dict($repository->all(), 'product_number');

        $this->assertEquals(700, $items[1]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Staff, $items[1]->displayed_discount_type);

        $this->assertEquals(700, $items[2]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Staff, $items[2]->displayed_discount_type);

        $this->assertEquals(700, $items[3]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Staff, $items[3]->displayed_discount_type);

        $this->assertEquals(700, $items[4]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Staff, $items[4]->displayed_discount_type);

        $this->assertEquals(700, $items[5]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Staff, $items[5]->displayed_discount_type);

        $this->assertEquals(500, $items[6]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Staff, $items[6]->displayed_discount_type);

        $this->assertEquals(600, $items[7]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Normal, $items[7]->displayed_discount_type);

        $this->assertEquals(600, $items[8]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Reservation, $items[8]->displayed_discount_type);

        $this->assertEquals(700, $items[9]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Staff, $items[9]->displayed_discount_type);
    }

    public function testGetMemberSearchScopeQuery()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1 => rate: 0.0, price: 1000, type: 割引なし
        $this->createItem();

        // 2 => rate: 0.2, price: 800, type: 通常割引
        $this->createItem(['discount_rate' => 0.2]);

        // 3 => rate: 0.3, price: 700, type: 会員割引
        $this->createItem(['discount_rate' => 0.2, 'is_member_discount' => 1, 'member_discount_rate' => 0.3]);

        // 4 => rate: 0.2, price: 800, type: 通常割引
        $this->createItem(['discount_rate' => 0.2, 'is_member_discount' => 0, 'member_discount_rate' => 0.3]);

        // 5 => rate: 0.1, price: 900, type: 会員割引
        $this->createItem(['discount_rate' => 0.2, 'is_member_discount' => 1, 'member_discount_rate' => 0.1]);

        // 6 => rate: 0.4, price: 600, type: イベント
        $this->createItem(['discount_rate' => 0.2]);
        $this->createEvent(['event_items' => [['item_id' => 6]]]);

        // 7 => rate: 0, price: 1000, type: 割引なし (イベント 期間外)
        $this->createItem();
        $this->createEvent([
            'event_items' => [['item_id' => 7]],
            'event' => [
                'period_to' => Carbon::now()->subDays(8)->format('Y-m-d H:i:s'),
                'period_from' => Carbon::now()->subDays(1)->format('Y-m-d H:i:s'),
            ],
        ]);

        // 8 => rate: 0, price: 1000, type: 割引なし (イベント 異なるsale_type)
        $this->createItem();
        $this->createEvent([
            'event_items' => [['item_id' => 8]],
            'event' => ['sale_type' => \App\Enums\Event\SaleType::Bundle],
            'bundle' => [['count' => 1000, 'rate' => 0.1]],
        ]);

        // 9 => rate: 0.5, price: 500, type: 割引なし (イベント 商品ごとに割引率を設定)
        $this->createItem();
        $this->createEvent([
            'event_items' => [['item_id' => 9, 'discount_rate' => 0.5]],
            'event' => ['discount_type' => \App\Enums\Event\DiscountType::EachProduct],
        ]);

        // 10 => rate: 0, price: 1000, type: イベント 特定会員（対象外）
        $this->createItem();
        $this->createEvent([
            'event_items' => [['item_id' => 10]],
            'event_user' => ['item_id' => 10, 'member_id' => 5000],
            'event' => ['target_user_type' => \App\Enums\Event\TargetUserType::Limit],
        ]);

        // 11 => rate: 0.4, price: 600, type: イベント 特定会員（対象）
        $this->createItem();
        $this->createEvent([
            'event_items' => [['item_id' => 11]],
            'event_user' => ['item_id' => 11, 'member_id' => 1],
            'event' => ['target_user_type' => \App\Enums\Event\TargetUserType::Limit],
        ]);

        // 12 => rate: 0.4, price: 600, type: イベント 会員のみ
        $this->createItem();
        $this->createEvent([
            'event_items' => [['item_id' => 12]],
            'event_user' => ['item_id' => 12, 'member_id' => 1],
            'event' => ['target_user_type' => \App\Enums\Event\TargetUserType::MemberOnly],
        ]);

        // 13 => rate: 0.3, price: 700, type: 社割り (他社ブランド)
        $this->createItem(['discount_rate' => 0.2, 'maker_product_number' => '0080-1000']);

        // 14 => rate: 0.5, price: 500, type: 社割り (自社ブランド)
        $this->createItem(['discount_rate' => 0.2, 'maker_product_number' => '0088-1000']);

        // 15 => rate: 0.4, price: 600, type: 社割り (他の割引が社割りを上回る)
        $this->createItem(['discount_rate' => 0.4, 'maker_product_number' => '0081-1000']);

        // 16 => rate: 0.2, price: 800, type: 複数イベントの優先順位
        $item = $this->createItem();
        $this->createEvent([
            'event_items' => [['item_id' => $item->id]],
            'event' => ['discount_rate' => 0.3, 'period_from' => Carbon::now()->subDays(6)->format('Y-m-d H:i:s')],
        ]);
        $this->createEvent([
            'event_items' => [['item_id' => $item->id]],
            'event' => ['discount_rate' => 0.2, 'period_from' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s')],
        ]);
        $this->createEvent([
            'event_items' => [['item_id' => $item->id]],
            'event' => ['discount_rate' => 0.1, 'period_from' => Carbon::now()->subDays(5)->format('Y-m-d H:i:s')],
        ]);

        // 17 => discount_price: 100, discounted_price: 900, type: 予約販売
        $item = $this->createItem();
        $this->createItemReserve($item->id, ['reserve_price' => 900]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $member = ['id' => 1];

        $service = new ItemPrice();
        $query = $service->getMemberSearchScopeQuery($member);
        $repository = resolve(\App\Repositories\ItemRepositoryEloquent::class);
        $repository->scopeQuery($query);
        $items = Arr::dict($repository->all(), 'id');

        $this->assertEquals(1000, $items[1]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::None, $items[1]->displayed_discount_type);

        $this->assertEquals(800, $items[2]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Normal, $items[2]->displayed_discount_type);

        $this->assertEquals(700, $items[3]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Member, $items[3]->displayed_discount_type);

        $this->assertEquals(800, $items[4]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Normal, $items[4]->displayed_discount_type);

        $this->assertEquals(900, $items[5]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Member, $items[5]->displayed_discount_type);

        $this->assertEquals(600, $items[6]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Event, $items[6]->displayed_discount_type);

        $this->assertEquals(1000, $items[7]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::None, $items[7]->displayed_discount_type);

        $this->assertEquals(1000, $items[8]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::None, $items[8]->displayed_discount_type);

        $this->assertEquals(500, $items[9]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Event, $items[9]->displayed_discount_type);

        $this->assertEquals(1000, $items[10]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::None, $items[10]->displayed_discount_type);

        $this->assertEquals(600, $items[11]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Event, $items[11]->displayed_discount_type);

        $this->assertEquals(600, $items[12]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Event, $items[12]->displayed_discount_type);

        $this->assertEquals(800, $items[13]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Normal, $items[13]->displayed_discount_type);

        $this->assertEquals(800, $items[14]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Normal, $items[14]->displayed_discount_type);

        $this->assertEquals(600, $items[15]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Normal, $items[15]->displayed_discount_type);

        $this->assertEquals(800, $items[16]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Event, $items[16]->displayed_discount_type);

        $this->assertEquals(900, $items[17]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Reservation, $items[17]->displayed_discount_type);
    }

    public function testGetNonMemberSearchScopeQuery()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1 => rate: 0.0, price: 1000, type: 割引なし
        $this->createItem();

        // 2 => rate: 0.2, price: 800, type: 通常割引
        $this->createItem(['discount_rate' => 0.2]);

        // 3 => rate: 0.2, price: 800, type: 通常割引 (会員割引対象外)
        $this->createItem(['discount_rate' => 0.2, 'is_member_discount' => 1, 'member_discount_rate' => 0.3]);

        // 4 => rate: 0.4, price: 600, type: イベント
        $this->createItem(['discount_rate' => 0.2]);
        $this->createEvent(['event_items' => [['item_id' => 4]]]);

        // 5 => rate: 0, price: 1000, type: 割引なし (イベント 期間外)
        $this->createItem();
        $this->createEvent([
            'event_items' => [['item_id' => 5]],
            'event' => [
                'period_to' => Carbon::now()->subDays(8)->format('Y-m-d H:i:s'),
                'period_from' => Carbon::now()->subDays(1)->format('Y-m-d H:i:s'),
            ],
        ]);

        // 6 => rate: 0, price: 1000, type: 割引なし (イベント 異なるsale_type)
        $this->createItem();
        $this->createEvent([
            'event_items' => [['item_id' => 6]],
            'event' => ['sale_type' => \App\Enums\Event\SaleType::Bundle],
            'bundle' => [['count' => 1000, 'rate' => 0.1]],
        ]);

        // 7 => rate: 0.5, price: 500, type: 割引なし (イベント 商品ごとに割引率を設定)
        $this->createItem();
        $this->createEvent([
            'event_items' => [['item_id' => 7, 'discount_rate' => 0.5]],
            'event' => ['discount_type' => \App\Enums\Event\DiscountType::EachProduct],
        ]);

        // 8 => rate: 0, price: 1000, type: イベント 特定会員（対象外）
        $this->createItem();
        $this->createEvent([
            'event_items' => [['item_id' => 8]],
            'event_user' => ['item_id' => 8, 'member_id' => 1],
            'event' => ['target_user_type' => \App\Enums\Event\TargetUserType::Limit],
        ]);

        // 9 => rate: 0, price: 1000, type: イベント 会員のみ (対象外)
        $this->createItem();
        $this->createEvent([
            'event_items' => [['item_id' => 9]],
            'event_user' => ['item_id' => 9, 'member_id' => 1],
            'event' => ['target_user_type' => \App\Enums\Event\TargetUserType::MemberOnly],
        ]);

        // 10 => rate: 0.2, price: 800, type: 複数イベントの優先順位
        $item = $this->createItem();
        $this->createEvent([
            'event_items' => [['item_id' => $item->id]],
            'event' => ['discount_rate' => 0.3, 'period_from' => Carbon::now()->subDays(6)->format('Y-m-d H:i:s')],
        ]);
        $this->createEvent([
            'event_items' => [['item_id' => $item->id]],
            'event' => ['discount_rate' => 0.2, 'period_from' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s')],
        ]);
        $this->createEvent([
            'event_items' => [['item_id' => $item->id]],
            'event' => ['discount_rate' => 0.1, 'period_from' => Carbon::now()->subDays(5)->format('Y-m-d H:i:s')],
        ]);

        // 11 => discount_price: 100, discounted_price: 900, type: 予約販売
        $item = $this->createItem();
        $this->createItemReserve($item->id, ['reserve_price' => 900]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $service = new ItemPrice();
        $query = $service->getNonMemberSearchScopeQuery();
        $repository = resolve(\App\Repositories\ItemRepositoryEloquent::class);
        $repository->scopeQuery($query);
        $items = Arr::dict($repository->all(), 'id');

        $this->assertEquals(1000, $items[1]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::None, $items[1]->displayed_discount_type);

        $this->assertEquals(800, $items[2]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Normal, $items[2]->displayed_discount_type);

        $this->assertEquals(800, $items[3]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Normal, $items[3]->displayed_discount_type);

        $this->assertEquals(600, $items[4]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Event, $items[4]->displayed_discount_type);

        $this->assertEquals(1000, $items[5]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::None, $items[5]->displayed_discount_type);

        $this->assertEquals(1000, $items[6]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::None, $items[6]->displayed_discount_type);

        $this->assertEquals(500, $items[7]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Event, $items[7]->displayed_discount_type);

        $this->assertEquals(1000, $items[8]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::None, $items[8]->displayed_discount_type);

        $this->assertEquals(1000, $items[9]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::None, $items[9]->displayed_discount_type);

        $this->assertEquals(800, $items[10]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Event, $items[10]->displayed_discount_type);

        $this->assertEquals(900, $items[11]->displayed_sale_price);
        $this->assertEquals(\App\Enums\Item\DiscountType::Reservation, $items[11]->displayed_discount_type);
    }
}
