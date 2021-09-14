<?php

namespace Tests\Unit\Domain\Adapters\Ymdy;

use App\Domain\Adapters\Ymdy\CartMemberPurchase;
use App\HttpCommunication\Ymdy\Mock\Purchase as PurchaseHttpCommunication;
use App\Utils\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tests\Unit\Domain\Adapters\Ymdy\CartMemberPurchaseTest\Coupon;

/**
 * @SuppressWarnings(PHPMD)
 */
class CartMemberPurchaseTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->truncateTables();

        $size = factory(\App\Models\Size::class)->create();
        $color = new \App\Models\Color(['name' => 'ホワイト', 'brightness' => 1]);
        $color->id = 1;
        $color->code = 1;
        $color->save();

        $item1 = factory(\App\Models\Item::class)->create(['term_id' => 1, 'organization_id' => 1, 'division_id' => 1, 'department_id' => 1, 'brand_id' => 1, 'retail_price' => 1000, 'is_member_discount' => 0]);
        $item2 = factory(\App\Models\Item::class)->create(['term_id' => 1, 'organization_id' => 1, 'division_id' => 1, 'department_id' => 1, 'brand_id' => 1, 'retail_price' => 2000, 'member_discount_rate' => 0.2, 'is_member_discount' => 1]);
        $item3 = factory(\App\Models\Item::class)->create(['term_id' => 1, 'organization_id' => 1, 'division_id' => 1, 'department_id' => 1, 'brand_id' => 1, 'retail_price' => 3000, 'discount_rate' => 0.3, 'is_member_discount' => 0]);

        $event1 = factory(\App\Models\Event::class)->create([
            'target_user_type' => \App\Enums\Event\TargetUserType::All,
            'target' => \App\Enums\Event\Target::Employee,
            'period_from' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
            'period_to' => Carbon::now()->addDays(4)->format('Y-m-d H:i:s'),
            'discount_rate' => 0.1,
            'discount_type' => \App\Enums\Event\DiscountType::Flat,
            'sale_type' => \App\Enums\Event\SaleType::Normal,
            'published' => 1,
            'is_delivery_setting' => 1,
        ]);
        $event2 = factory(\App\Models\Event::class)->create([
            'target_user_type' => \App\Enums\Event\TargetUserType::All,
            'target' => \App\Enums\Event\Target::Employee,
            'period_from' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
            'period_to' => Carbon::now()->addDays(4)->format('Y-m-d H:i:s'),
            'discount_rate' => 0.0,
            'discount_type' => \App\Enums\Event\DiscountType::Flat,
            'sale_type' => \App\Enums\Event\SaleType::Bundle,
            'published' => 1,
            'is_delivery_setting' => 1,
        ]);
        \App\Models\EventItem::create(['event_id' => $event1->id, 'item_id' => $item1->id, 'discount_rate' => 0.1]);
        \App\Models\EventItem::create(['event_id' => $event2->id, 'item_id' => $item2->id, 'discount_rate' => 0.0]);
        \App\Models\EventBundleSale::create(['event_id' => $event2->id, 'count' => 2, 'rate' => 0.1]);

        $itemDetail1 = factory(\App\Models\ItemDetail::class)->create(['item_id' => $item1->id, 'color_id' => $color->id, 'size_id' => $size->id]);
        $itemDetail2 = factory(\App\Models\ItemDetail::class)->create(['item_id' => $item2->id, 'color_id' => $color->id, 'size_id' => $size->id]);
        $itemDetail3 = factory(\App\Models\ItemDetail::class)->create(['item_id' => $item3->id, 'color_id' => $color->id, 'size_id' => $size->id]);

        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => $itemDetail1->id, 'jan_code' => '101']);
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => $itemDetail2->id, 'jan_code' => '102', 'arrival_date' => Carbon::now()]);
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => $itemDetail2->id, 'jan_code' => '103', 'arrival_date' => Carbon::now()->subDays(1)]);
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => $itemDetail3->id, 'jan_code' => '104']);

        $cart = \App\Models\Cart::create([
            'token' => 'abcdefg',
            'member_id' => 200,
            'use_coupon_ids' => [1],
            'order_type' => 1,
        ]);

        \App\Models\CartItem::create(['cart_id' => $cart->id, 'item_detail_id' => $itemDetail1->id, 'count' => 1, 'posted_at' => Carbon::now()]);
        \App\Models\CartItem::create(['cart_id' => $cart->id, 'item_detail_id' => $itemDetail2->id, 'count' => 2, 'posted_at' => Carbon::now()]);
        \App\Models\CartItem::create(['cart_id' => $cart->id, 'item_detail_id' => $itemDetail3->id, 'count' => 3, 'posted_at' => Carbon::now()]);

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
        \App\Models\EventBundleSale::truncate();
        \App\Models\Cart::truncate();
        \App\Models\CartItem::truncate();
        \App\Models\Size::truncate();
        \App\Models\Color::truncate();
    }

    /**
     * 会員購買登録 POST /api/v1/member/{member_id}/purchase に投げるリクエストのテスト。
     * $sentPayload = $http->lastRequestedParams[1];
     * で投げるリクエスの中身を検証する。
     *
     * @return void
     */
    public function testCalculatePoint()
    {
        $http = resolve(PurchaseHttpCommunication::class);
        $adapter = new CartMemberPurchase($http, resolve(Coupon::class), resolve(\App\Domain\ItemPrice::class));

        $cart = \App\Models\Cart::find(1);
        $cart->discounted_delivery_fee = 0;
        $cart->delivery_fee_discount_type = \App\Enums\OrderDiscount\Type::DeliveryFee;
        $cart->memberCoupons = \App\Entities\Ymdy\Member\MemberCoupon::collection([
            [
                'coupon' => [
                    'discount_amount' => 1000,
                    'usage_amount_term_flag' => false,
                    'discount_item_flag' => 1,
                    'discount_type' => \App\Enums\Coupon\DiscountType::Fixed,
                    'target_item_type' => \App\Enums\Coupon\TargetItemType::Specified,
                    '_item_cd_data' => ['103', '104'],
                ],
            ],
        ]);

        $cart->member = ['id' => 200];

        $deliveryHopeDate = Carbon::now()->addDays(3)->format('Y-m-d');

        $prices = [
            'total_price' => 9080,
            'payment_fee' => 200,
        ];
        $options = [
            'delivery_hope_date' => $deliveryHopeDate,
            'payment_type' => \App\Enums\Order\PaymentType::CreditCard,
            'use_point' => 200,
        ];
        $adapter->calculatePoint($cart, $prices, $options);
        $sentPayload = $http->lastRequestedParams[1];

        // JAN: 101
        $detail = Arr::find($sentPayload['details'], function ($element) {
            return $element['item_jan_code'] === '101';
        });
        $this->assertTrue($detail !== false, 'jan101が存在する');
        $this->assertEquals(1000, $detail['retail_price'], '上代が一致する');
        $this->assertEquals(883, $detail['sales_price'], '販売金額が一致する');
        $this->assertEquals(1, $detail['amount'], '数量が一致する');
        $this->assertEquals(17, $detail['use_point'], 'ポイント按分が一致する');
        $this->assertEquals(\App\Enums\Ymdy\Member\PvDiv::Proper, $detail['pb_div'], 'P/B区分が正しい');
        $this->assertEquals(\App\Enums\Ymdy\Member\CrosspointPvDiv::Bargain, $detail['crosspoint_pb_div'], 'クロスポイントP/B区分が正しい');
        $this->assertEquals(\App\Enums\OrderDetail\TaxRateId::Rate10, $detail['tax_rate_id'], '消費税IDが正しい');
        $this->assertEquals(\App\Enums\Ymdy\Member\TaxType::Included, $detail['tax_type'], '消費税種別が正しい');
        $this->assertEquals(80, $detail['tax'], '消費税が正しい');
        $this->assertEquals(803, $detail['tax_excluded_sale_price'], '税抜販売金額が正しい');
        $this->assertEquals(1, count($detail['discounts']), '割引の件数が正しい');
        $this->assertEquals(1, $detail['color']['id'], '色が一致する');

        $discount = current($detail['discounts']);
        $this->assertEquals(1, count($detail['discounts']));
        $this->assertEquals(\App\Enums\Ymdy\Member\Purchase\DiscountType::EventSale, $discount['type'], '通常割引の割引が存在する');
        $this->assertEquals(100, $discount['price'], '通常割引の割引金額が正しい');

        // JAN: 102
        $detail = Arr::find($sentPayload['details'], function ($element) {
            return $element['item_jan_code'] === '102';
        });
        $this->assertTrue($detail === false, '複数JANでは出荷日の古いデータが選択される');

        // JAN: 103
        $detail = Arr::find($sentPayload['details'], function ($element) {
            return $element['item_jan_code'] === '103';
        });
        $this->assertTrue($detail !== false);
        $this->assertEquals(2000, $detail['retail_price']);
        $this->assertEquals(2510, $detail['sales_price']);
        $this->assertEquals(2, $detail['amount']);
        $this->assertEquals(57, $detail['use_point']);
        $this->assertEquals(\App\Enums\Ymdy\Member\PvDiv::Bargain, $detail['pb_div']);
        $this->assertEquals(\App\Enums\Ymdy\Member\CrosspointPvDiv::Bargain, $detail['crosspoint_pb_div']);
        $this->assertEquals(\App\Enums\OrderDetail\TaxRateId::Rate10, $detail['tax_rate_id']);
        $this->assertEquals(\App\Enums\Ymdy\Member\TaxType::Included, $detail['tax_type']);
        $this->assertEquals(228, $detail['tax']);
        $this->assertEquals(2282, $detail['tax_excluded_sale_price']);
        $this->assertEquals(3, count($detail['discounts']));

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::Member;
        });
        $this->assertTrue($discount !== false);
        $this->assertEquals(800, $discount['price']);

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::EventBundle;
        });
        $this->assertTrue($discount !== false);
        $this->assertEquals(320, $discount['price']);

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::Coupon;
        });
        $this->assertTrue($discount !== false);
        $this->assertEquals(313, $discount['price']);

        // JAN: 104
        $detail = Arr::find($sentPayload['details'], function ($element) {
            return $element['item_jan_code'] === '104';
        });
        $this->assertTrue($detail !== false);
        $this->assertEquals(3000, $detail['retail_price']);
        $this->assertEquals(5487, $detail['sales_price']);
        $this->assertEquals(3, $detail['amount']);
        $this->assertEquals(126, $detail['use_point']);
        $this->assertEquals(\App\Enums\Ymdy\Member\PvDiv::Bargain, $detail['pb_div']);
        $this->assertEquals(\App\Enums\Ymdy\Member\CrosspointPvDiv::Bargain, $detail['crosspoint_pb_div']);
        $this->assertEquals(\App\Enums\OrderDetail\TaxRateId::Rate10, $detail['tax_rate_id']);
        $this->assertEquals(\App\Enums\Ymdy\Member\TaxType::Included, $detail['tax_type']);
        $this->assertEquals(498, $detail['tax']);
        $this->assertEquals(4989, $detail['tax_excluded_sale_price']);
        $this->assertEquals(2, count($detail['discounts']));

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::Normal;
        });
        $this->assertTrue($discount !== false);
        $this->assertEquals(2700, $discount['price']);

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::Coupon;
        });
        $this->assertTrue($discount !== false);
        $this->assertEquals(687, $discount['price']);

        // 受注全体
        $this->assertEquals($deliveryHopeDate, Carbon::parse($sentPayload['delivery_date'])->format('Y-m-d'), '発送日が一致する');
        $this->assertEquals(\App\Enums\Order\PaymentType::CreditCard, $sentPayload['payment']['id'], '支払い方法が一致する');
        $this->assertEquals(\App\Enums\Order\PaymentType::CreditCard()->description, $sentPayload['payment']['name'], '支払い方法名称が一致する');
        $this->assertEquals(9080, $sentPayload['total_price'], '合計金額が一致する');
        $this->assertEquals(0, $sentPayload['delivery_fee'], '配送料金が一致する');
        $this->assertEquals(\App\Enums\OrderDiscount\Type::DeliveryFee()->description, $sentPayload['delivery_fee_memo'], '配送料金メモが一致する');
        $this->assertEquals(200, $sentPayload['payment_fee'], '手数料が一致する');
        $this->assertEquals(824, $sentPayload['total_tax'], '合計消費税が一致する');
        $this->assertEquals(8256, $sentPayload['tax_excluded_total_price'], '税抜合計金額が一致する');
        $this->assertEquals(3, $sentPayload['detail_num'], '詳細件数が一致する');
    }
}
