<?php

namespace Tests\Unit\Domain\Utils;

use App\Domain\Utils\OrderSaleType;
use App\Enums\Event\Target;
use App\Enums\Order\SaleType;
use App\Models\Event;
use Tests\TestCase;

class OrderSaleTypeTest extends TestCase
{
    public function dataGetSaleTypeForEC()
    {
        return [
            '通常 上代のまま販売 : プロパー' => [
                'params' => [
                    'is_employee' => false,
                    'event' => null,
                    'use_coupon' => false,
                ],
                'expected' => SaleType::Employee,
            ],
            '通常 社割を除く値引き価格で販売 : セール' => [
                'params' => [
                    'is_employee' => false,
                    'event' => null,
                    'use_coupon' => true,
                ],
                'expected' => SaleType::Sale,
            ],
            '通常 社割で値引き価格で販売 : プロパー' => [
                'params' => [
                    'is_employee' => true,
                    'event' => null,
                    'use_coupon' => true,
                ],
                'expected' => SaleType::Employee,
            ],
            // イベント
            'イベント自体がプロパーイベント イベント価格で販売 : プロパー' => [
                'params' => [
                    'is_employee' => false,
                    'event' => new Event([
                        'period_from' => '2021-01-10 10:00:00',
                        'sale_type' => Target::Employee,
                    ]),
                    'use_coupon' => false,
                ],
                'expected' => SaleType::Employee,
            ],
            'イベント自体がプロパーイベント 社割価格で販売 : プロパー' => [
                'params' => [
                    'is_employee' => true,
                    'event' => new Event([
                        'period_from' => '2021-01-10 10:00:00',
                        'sale_type' => Target::Employee,
                    ]),
                    'use_coupon' => false,
                ],
                'expected' => SaleType::Employee,
            ],
            'イベント自体がセール・イベント イベント価格で販売 : セール' => [
                'params' => [
                    'is_employee' => false,
                    'event' => new Event([
                        'period_from' => '2021-01-10 10:00:00',
                        'sale_type' => Target::Sale,
                    ]),
                    'use_coupon' => false,
                ],
                'expected' => SaleType::Sale,
            ],
            'イベント自体がセール・イベント 社割価格で販売 : プロパー' => [
                'params' => [
                    'is_employee' => true,
                    'event' => new Event([
                        'period_from' => '2021-01-10 10:00:00',
                        'sale_type' => Target::Sale,
                    ]),
                    'use_coupon' => false,
                ],
                'expected' => SaleType::Employee,
            ],
        ];
    }

    /**
     * @param $params
     * @param $expected
     * @dataProvider dataGetSaleTypeForEC
     */
    public function testGetSaleTypeForEC($params, $expected)
    {
        // パラメータ設定(テスト用のダミーデータ)
        $member = $params['is_employee'] ? ['staff_code' => 'employee'] : null;
        $useCouponIds = $params['use_coupon'] ? [1] : [];

        $result = OrderSaleType::getSaleTypeForEC($member, $params['event'], $useCouponIds);
        $this->assertEquals($expected, $result);
    }

    public function dataGetSaleTypeByItem()
    {
        return [
            '通常割引' => [
                'params' => [
                    'applied_bundle_sale' => null,
                    'applicable_event' => null,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::Normal,
                ],
                'expected' => SaleType::Sale,
            ],
            '会員割引' => [
                'params' => [
                    'applied_bundle_sale' => null,
                    'applicable_event' => null,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::Member,
                ],
                'expected' => SaleType::Sale,
            ],
            'イベントプロパー' => [
                'params' => [
                    'applied_bundle_sale' => null,
                    'applicable_event' => new \App\Models\Event(['sale_type' => Target::Employee]),
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::Event,
                ],
                'expected' => SaleType::Employee,
            ],
            'イベントセール' => [
                'params' => [
                    'applied_bundle_sale' => null,
                    'applicable_event' => new \App\Models\Event(['sale_type' => Target::Sale]),
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::Event,
                ],
                'expected' => SaleType::Sale,
            ],
            'バンドル販売プロパー' => [
                'params' => [
                    'applied_bundle_sale' => new \App\Models\Event(['sale_type' => Target::Employee]),
                    'applicable_event' => null,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::None,
                ],
                'expected' => SaleType::Employee,
            ],
            'バンドル販売セール' => [
                'params' => [
                    'applied_bundle_sale' => new \App\Models\Event(['sale_type' => Target::Sale]),
                    'applicable_event' => null,
                    'displayed_discount_type' => \App\Enums\Item\DiscountType::None,
                ],
                'expected' => SaleType::Sale,
            ],
        ];
    }

    /**
     * @param array $params
     * @param int $expected
     *
     * @return void
     *
     * @dataProvider dataGetSaleTypeByItem
     */
    public function testGetSaleTypeByItem(array $params, int $expected)
    {
        $item = new \App\Models\Item();
        $item->setRelation('appliedBundleSale', $params['applied_bundle_sale']);
        $item->setRelation('applicableEvent', $params['applicable_event']);
        $item->displayed_discount_type = $params['displayed_discount_type'];

        $this->assertEquals($expected, OrderSaleType::getSaleTypeByItem($item));
    }

    /**
     * @return array
     */
    public function dataGetSaleTypeByOrderDetail()
    {
        return [
            'バンドル販売あり & sale_type=Sale' => [
                'params' => [
                    'order_used_coupons' => (new \App\Models\OrderUsedCoupon())->newCollection([]),
                    'bundle_sale_discount' => (new \App\Models\OrderDiscount())->setRelation(
                        'discountable',
                        new \App\Models\Event(['sale_type' => Target::Sale])
                    ),
                    'displayed_discount' => null,
                    'coupon_target_order_detail_ids' => null,
                ],
                'expected' => SaleType::Sale,
            ],
            'バンドル販売あり & sale_type=Employee' => [
                'params' => [
                    'order_used_coupons' => (new \App\Models\OrderUsedCoupon())->newCollection([]),
                    'bundle_sale_discount' => (new \App\Models\OrderDiscount())->setRelation(
                        'discountable',
                        new \App\Models\Event(['sale_type' => Target::Employee])
                    ),
                    'displayed_discount' => null,
                    'coupon_target_order_detail_ids' => null,
                ],
                'expected' => SaleType::Employee,
            ],
            'イベント・セールあり & sale_type=Sale' => [
                'params' => [
                    'order_used_coupons' => (new \App\Models\OrderUsedCoupon())->newCollection([]),
                    'bundle_sale_discount' => null,
                    'displayed_discount' => (new \App\Models\OrderDiscount([
                        'type' => \App\Enums\OrderDiscount\Type::EventSale,
                    ]))->setRelation(
                        'discountable',
                        new \App\Models\Event(['sale_type' => Target::Sale])
                    ),
                    'coupon_target_order_detail_ids' => null,
                ],
                'expected' => SaleType::Sale,
            ],
            'イベント・セールあり & sale_type=Employee' => [
                'params' => [
                    'order_used_coupons' => (new \App\Models\OrderUsedCoupon())->newCollection([]),
                    'bundle_sale_discount' => null,
                    'displayed_discount' => (new \App\Models\OrderDiscount([
                        'type' => \App\Enums\OrderDiscount\Type::EventSale,
                    ]))->setRelation(
                        'discountable',
                        new \App\Models\Event(['sale_type' => Target::Employee])
                    ),
                    'coupon_target_order_detail_ids' => null,
                ],
                'expected' => SaleType::Employee,
            ],
            '通常割引' => [
                'params' => [
                    'order_used_coupons' => (new \App\Models\OrderUsedCoupon())->newCollection([]),
                    'bundle_sale_discount' => null,
                    'displayed_discount' => new \App\Models\OrderDiscount(['type' => \App\Enums\OrderDiscount\Type::Normal]),
                    'coupon_target_order_detail_ids' => null,
                ],
                'expected' => SaleType::Sale,
            ],
            '会員割引' => [
                'params' => [
                    'order_used_coupons' => (new \App\Models\OrderUsedCoupon())->newCollection([]),
                    'bundle_sale_discount' => null,
                    'displayed_discount' => new \App\Models\OrderDiscount(['type' => \App\Enums\OrderDiscount\Type::Member]),
                    'coupon_target_order_detail_ids' => null,
                ],
                'expected' => SaleType::Sale,
            ],
        ];
    }

    /**
     * @param array $params
     * @param int $expected
     *
     * @return void
     *
     * @dataProvider dataGetSaleTypeByOrderDetail
     */
    public function testGetSaleTypeByOrderDetail(array $params, int $expected)
    {
        $orderDetail = new \App\Models\OrderDetail();
        $orderDetail->id = 1;
        $orderDetail->setRelation('order', new \App\Models\Order());
        $orderDetail->setRelation('bundleSaleDiscount', $params['bundle_sale_discount']);
        $orderDetail->setRelation('displayedDiscount', $params['displayed_discount']);

        $this->assertEquals($expected, OrderSaleType::getSaleTypeByOrderDetail($orderDetail));
    }
}
