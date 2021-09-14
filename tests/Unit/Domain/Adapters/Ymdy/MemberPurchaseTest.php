<?php

namespace Tests\Unit\Domain;

use App\Domain\Adapters\Ymdy\MemberPurchase;
use App\Domain\Adapters\Ymdy\Purchase as PurchaseAdapter;
use App\Domain\OrderPortion;
use App\HttpCommunication\Ymdy\Mock\Member as MemberHttpCommunication;
use App\HttpCommunication\Ymdy\Mock\Purchase as PurchaseHttpCommunication;
use App\Repositories\OrderRepository;
use App\Utils\Arr;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD)
 */
class MemberPurchaseTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->truncateTables();

        $color = new \App\Models\Color([
            'name' => 'ホワイト',
            'display_name' => 'ホワイト',
            'color_panel' => '#ffffff',
            'brightness' => 1,
        ]);
        $color->code = '01';
        $color->id = 1;
        $color->save();

        $size = factory(\App\Models\Size::class)->create(['name' => 'フリー']);
        $size->id = 1;
        $size->save();

        factory(\App\Models\Item::class)->create(['term_id' => 1, 'organization_id' => 1, 'division_id' => 1, 'department_id' => 1, 'brand_id' => 1]);
        factory(\App\Models\Item::class)->create(['term_id' => 1, 'organization_id' => 1, 'division_id' => 1, 'department_id' => 1, 'brand_id' => 1]);
        factory(\App\Models\Item::class)->create(['term_id' => 1, 'organization_id' => 1, 'division_id' => 1, 'department_id' => 1, 'brand_id' => 1]);

        factory(\App\Models\ItemDetail::class)->create(['item_id' => 1, 'color_id' => 1, 'size_id' => 1]);
        factory(\App\Models\ItemDetail::class)->create(['item_id' => 2, 'color_id' => 1, 'size_id' => 1]);
        factory(\App\Models\ItemDetail::class)->create(['item_id' => 3, 'color_id' => 1, 'size_id' => 1]);

        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 1, 'jan_code' => '101']);
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 2, 'jan_code' => '102']);
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 2, 'jan_code' => '103']);
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 3, 'jan_code' => '104']);

        factory(\App\Models\Order::class)->create([
            'price' => 9612,
            'fee' => 500,
            'delivery_fee' => 1100,
            'use_point' => 100,
            'add_point' => 1000,
            'member_id' => 1000,
            'code' => '888',
            'delivery_hope_date' => '2020-10-10 00:00:00',
            'payment_type' => \App\Enums\Order\PaymentType::Bank,
        ]);

        factory(\App\Models\OrderDetail::class)->create(['retail_price' => 1000, 'item_detail_id' => 1, 'sale_type' => \App\Enums\Order\SaleType::Sale]);
        factory(\App\Models\OrderDetail::class)->create(['retail_price' => 2000, 'item_detail_id' => 2, 'sale_type' => \App\Enums\Order\SaleType::Sale]);
        factory(\App\Models\OrderDetail::class)->create(['retail_price' => 5000, 'item_detail_id' => 3, 'sale_type' => \App\Enums\Order\SaleType::Employee]);

        factory(\App\Models\OrderDetailUnit::class)->create(['amount' => 2, 'order_detail_id' => 1, 'item_detail_identification_id' => 1]);
        factory(\App\Models\OrderDetailUnit::class)->create(['amount' => 1, 'order_detail_id' => 2, 'item_detail_identification_id' => 2]);
        factory(\App\Models\OrderDetailUnit::class)->create(['amount' => 1, 'order_detail_id' => 2, 'item_detail_identification_id' => 3]);
        factory(\App\Models\OrderDetailUnit::class)->create(['amount' => 1, 'order_detail_id' => 3, 'item_detail_identification_id' => 4]);

        factory(\App\Models\OrderAddress::class)->create(['order_id' => 1, 'type' => \App\Enums\OrderAddress\Type::Member, 'lname' => '会員住所姓']);
        factory(\App\Models\OrderAddress::class)->create(['order_id' => 1, 'type' => \App\Enums\OrderAddress\Type::Delivery, 'lname' => '配送先姓']);
        factory(\App\Models\OrderAddress::class)->create(['order_id' => 1, 'type' => \App\Enums\OrderAddress\Type::Bill, 'lname' => '請求先姓']);

        factory(\App\Models\Event::class)->create(['discount_type' => \App\Enums\Event\SaleType::Bundle]);

        \App\Models\OrderUsedCoupon::create([
            'order_id' => 1,
            'coupon_id' => 1,
            'target_order_detail_ids' => [1, 2],
        ]);

        // 商品割引
        \App\Models\OrderDiscount::create([
            'orderable_id' => 1,
            'orderable_type' => \App\Models\OrderDetail::class,
            'unit_applied_price' => 100,
            'applied_price' => 200,
            'type' => \App\Enums\OrderDiscount\Type::Normal,
            'method' => \App\Enums\OrderDiscount\Method::Percentile,
            'discount_rate' => 0.1,
        ]);
        \App\Models\OrderDiscount::create([
            'orderable_id' => 2,
            'orderable_type' => \App\Models\OrderDetail::class,
            'unit_applied_price' => 400,
            'applied_price' => 800,
            'type' => \App\Enums\OrderDiscount\Type::Member,
            'method' => \App\Enums\OrderDiscount\Method::Percentile,
            'discount_rate' => 0.2,
        ]);

        // バンドル販売
        \App\Models\OrderDiscount::create([
            'orderable_id' => 2,
            'orderable_type' => \App\Models\OrderDetail::class,
            'unit_applied_price' => 160,
            'applied_price' => 320,
            'type' => \App\Enums\OrderDiscount\Type::EventBundle,
            'method' => \App\Enums\OrderDiscount\Method::Percentile,
            'discount_rate' => 0.1,
            'discountable_type' => \App\Models\Event::class,
            'discountable_id' => 1,
        ]);

        // クーポン割引
        \App\Models\OrderDiscount::create([
            'orderable_id' => 1,
            'orderable_type' => \App\Models\Order::class,
            'unit_applied_price' => null,
            'applied_price' => 468,
            'type' => \App\Enums\OrderDiscount\Type::CouponItem,
            'method' => \App\Enums\OrderDiscount\Method::Percentile,
            'discount_rate' => 0.1,
            'discountable_type' => \App\Models\OrderUsedCoupon::class,
            'discountable_id' => 1,
        ]);

        // 送料無料
        \App\Models\OrderDiscount::create([
            'orderable_id' => 1,
            'orderable_type' => \App\Models\Order::class,
            'unit_applied_price' => null,
            'applied_price' => 1100,
            'type' => \App\Enums\OrderDiscount\Type::CouponDeliveryFee,
            'method' => \App\Enums\OrderDiscount\Method::Fixed,
            'discount_price' => 1100,
            'discountable_type' => \App\Models\OrderUsedCoupon::class,
            'discountable_id' => 1,
        ]);

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
        \App\Models\Color::truncate();
        \App\Models\Size::truncate();
        \App\Models\Item::truncate();
        \App\Models\ItemDetail::truncate();
        \App\Models\ItemDetailIdentification::truncate();
        \App\Models\Event::truncate();

        \App\Models\Order::truncate();
        \App\Models\OrderDetail::truncate();
        \App\Models\OrderDetailUnit::truncate();
        \App\Models\OrderDiscount::truncate();
        \App\Models\OrderUsedCoupon::truncate();
        \App\Models\OrderAddress::truncate();

        \App\Models\OrderLog::truncate();
        \App\Models\OrderDetailLog::truncate();
        \App\Models\OrderDetailUnitLog::truncate();
        \App\Models\OrderDiscountLog::truncate();
        \App\Models\OrderUsedCouponLog::truncate();
    }

    /**
     * 会員購買登録 POST /api/v1/member/{member_id}/purchase に投げるリクエストのテスト。
     * $sentPayload = $http->lastRequestedParams[1];
     * で投げるリクエスの中身を検証する。
     *
     * @return void
     */
    public function testCreateMemberPurchase()
    {
        $purchaseAdapter = resolve(PurchaseAdapter::class);
        $http = resolve(MemberHttpCommunication::class);
        $adapter = new MemberPurchase(resolve(OrderPortion::class), resolve(OrderRepository::class), $http, resolve(PurchaseHttpCommunication::class));
        $order = \App\Models\Order::find(1);
        $ecBill = $purchaseAdapter->makeEcBill($order);
        $adapter->createMemberPurchaseAndUpdateTax($order, $ecBill);

        $sentPayload = $http->lastRequestedParams[1];

        // JAN: 101
        $detail = Arr::find($sentPayload['details'], function ($element) {
            return $element['item_jan_code'] === '101';
        });
        $this->assertTrue($detail !== false, 'jan101が存在する');
        $this->assertEquals(1000, $detail['retail_price'], '上代が一致する');
        $this->assertEquals(1602, $detail['sales_price'], '販売金額が一致する');
        $this->assertEquals(2, $detail['amount'], '数量が一致する');
        $this->assertEquals(18, $detail['use_point'], 'ポイント按分が一致する');
        $this->assertEquals(\App\Enums\Ymdy\Member\PvDiv::Bargain, $detail['pb_div'], 'P/B区分が正しい');
        $this->assertEquals(\App\Enums\Ymdy\Member\CrosspointPvDiv::Bargain, $detail['crosspoint_pb_div'], 'クロスポイントP/B区分が正しい');
        $this->assertEquals(\App\Enums\OrderDetail\TaxRateId::Rate10, $detail['tax_rate_id'], '消費税IDが正しい');
        $this->assertEquals(\App\Enums\Ymdy\Member\TaxType::Included, $detail['tax_type'], '消費税種別が正しい');
        $this->assertEquals(145, $detail['tax'], '消費税が正しい');
        $this->assertEquals(1457, $detail['tax_excluded_sale_price'], '税抜販売金額が正しい');
        $this->assertEquals(2, count($detail['discounts']), '割引の件数が正しい');
        $this->assertEquals(1, $detail['color']['id'], '色が一致する');
        $this->assertEquals('ホワイト', $detail['color']['display_name'], '色が一致する');
        $this->assertEquals(1, $detail['size']['id'], 'サイズが一致する');
        $this->assertEquals('フリー', $detail['size']['name'], 'サイズが一致する');
        $this->assertTrue(isset($detail['item_published_date']), '商品公開日が存在する');

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::Normal;
        });
        $this->assertTrue($discount !== false, '通常割引の割引が存在する');
        $this->assertEquals(200, $discount['price'], '通常割引の割引金額が正しい');

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::Coupon;
        });
        $this->assertTrue($discount !== false, 'クーポン割引の割引が存在する');
        $this->assertEquals(180, $discount['price'], 'クーポン割引の割引金額が正しい');

        foreach ($order->orderDetails as $orderDetail) {
            foreach ($orderDetail->orderDetailUnits as $unit) {
                if ((int) $unit->id === 1) {
                    $this->assertTrue((int) $unit->tax === (int) $detail['tax']);
                }
            }
        }

        // JAN: 102
        $detail = Arr::find($sentPayload['details'], function ($element) {
            return $element['item_jan_code'] === '102';
        });
        $this->assertTrue($detail !== false);
        $this->assertEquals(2000, $detail['retail_price']);
        $this->assertEquals(1282, $detail['sales_price']);
        $this->assertEquals(1, $detail['amount']);
        $this->assertEquals(14, $detail['use_point']);
        $this->assertEquals(\App\Enums\Ymdy\Member\PvDiv::Bargain, $detail['pb_div']);
        $this->assertEquals(\App\Enums\Ymdy\Member\CrosspointPvDiv::Bargain, $detail['crosspoint_pb_div']);
        $this->assertEquals(\App\Enums\OrderDetail\TaxRateId::Rate10, $detail['tax_rate_id']);
        $this->assertEquals(\App\Enums\Ymdy\Member\TaxType::Included, $detail['tax_type']);
        $this->assertEquals(116, $detail['tax']);
        $this->assertEquals(1166, $detail['tax_excluded_sale_price']);
        $this->assertEquals(3, count($detail['discounts']));

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::Member;
        });
        $this->assertTrue($discount !== false);
        $this->assertEquals(400, $discount['price']);

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::EventBundle;
        });
        $this->assertTrue($discount !== false);
        $this->assertEquals(160, $discount['price']);

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::Coupon;
        });
        $this->assertTrue($discount !== false);
        $this->assertEquals(144, $discount['price']);

        foreach ($order->orderDetails as $orderDetail) {
            foreach ($orderDetail->orderDetailUnits as $unit) {
                if ((int) $unit->id === 2) {
                    $this->assertTrue((int) $unit->tax === (int) $detail['tax']);
                }
            }
        }

        // JAN: 103
        $detail = Arr::find($sentPayload['details'], function ($element) {
            return $element['item_jan_code'] === '103';
        });
        $this->assertTrue($detail !== false);
        $this->assertEquals(2000, $detail['retail_price']);
        $this->assertEquals(1282, $detail['sales_price']);
        $this->assertEquals(1, $detail['amount']);
        $this->assertEquals(14, $detail['use_point']);
        $this->assertEquals(\App\Enums\Ymdy\Member\PvDiv::Bargain, $detail['pb_div']);
        $this->assertEquals(\App\Enums\Ymdy\Member\CrosspointPvDiv::Bargain, $detail['crosspoint_pb_div']);
        $this->assertEquals(\App\Enums\OrderDetail\TaxRateId::Rate10, $detail['tax_rate_id']);
        $this->assertEquals(\App\Enums\Ymdy\Member\TaxType::Included, $detail['tax_type']);
        $this->assertEquals(116, $detail['tax']);
        $this->assertEquals(1166, $detail['tax_excluded_sale_price']);
        $this->assertEquals(3, count($detail['discounts']));

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::Member;
        });
        $this->assertTrue($discount !== false);
        $this->assertEquals(400, $discount['price']);

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::EventBundle;
        });
        $this->assertTrue($discount !== false);
        $this->assertEquals(160, $discount['price']);

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::Coupon;
        });
        $this->assertTrue($discount !== false);
        $this->assertEquals(144, $discount['price']);

        foreach ($order->orderDetails as $orderDetail) {
            foreach ($orderDetail->orderDetailUnits as $unit) {
                if ((int) $unit->id === 3) {
                    $this->assertTrue((int) $unit->tax === (int) $detail['tax']);
                }
            }
        }

        // JAN: 104
        $detail = Arr::find($sentPayload['details'], function ($element) {
            return $element['item_jan_code'] === '104';
        });
        $this->assertTrue($detail !== false);
        $this->assertEquals(5000, $detail['retail_price']);
        $this->assertEquals(4946, $detail['sales_price']);
        $this->assertEquals(1, $detail['amount']);
        $this->assertEquals(54, $detail['use_point']);
        $this->assertEquals(\App\Enums\Ymdy\Member\PvDiv::Proper, $detail['pb_div']);
        $this->assertEquals(\App\Enums\Ymdy\Member\CrosspointPvDiv::Proper, $detail['crosspoint_pb_div']);
        $this->assertEquals(\App\Enums\OrderDetail\TaxRateId::Rate10, $detail['tax_rate_id']);
        $this->assertEquals(\App\Enums\Ymdy\Member\TaxType::Included, $detail['tax_type']);
        $this->assertEquals(449, $detail['tax']);
        $this->assertEquals(4497, $detail['tax_excluded_sale_price']);
        $this->assertEquals(0, count($detail['discounts']));

        foreach ($order->orderDetails as $orderDetail) {
            foreach ($orderDetail->orderDetailUnits as $unit) {
                if ((int) $unit->id === 4) {
                    $this->assertTrue((int) $unit->tax === (int) $detail['tax']);
                }
            }
        }

        // 受注全体
        $this->assertEquals('888', $sentPayload['code'], '商品コードが一致する');
        $this->assertEquals('2020-10-10 00:00:00', Carbon::parse($sentPayload['delivery_date'])->format('Y-m-d H:i:s'), '発送日が一致する');
        $this->assertEquals(\App\Enums\Order\PaymentType::Bank, $sentPayload['payment']['id'], '支払い方法が一致する');
        $this->assertEquals(\App\Enums\Order\PaymentType::Bank()->description, $sentPayload['payment']['name'], '支払い方法名称が一致する');
        $this->assertEquals(9612, $sentPayload['total_price'], '合計金額が一致する');
        $this->assertEquals(0, $sentPayload['delivery_fee'], '配送料金が一致する');
        $this->assertEquals('クーポン送料割引', $sentPayload['delivery_fee_memo'], '配送料金メモが一致する');
        $this->assertEquals(500, $sentPayload['payment_fee'], '手数料が一致する');
        $this->assertEquals(871, $sentPayload['total_tax'], '合計消費税が一致する');
        $this->assertEquals(8741, $sentPayload['tax_excluded_total_price'], '税抜合計金額が一致する');
        $this->assertEquals(4, $sentPayload['detail_num'], '詳細件数が一致する');
        $this->assertTrue((int) $order->tax === (int) $sentPayload['total_tax'], 'orderと合計消費税が一致する');
        foreach ([
            [\App\Enums\OrderAddress\Type::Member, 'member_address', '会員住所姓'],
            [\App\Enums\OrderAddress\Type::Delivery, 'delivery_address', '配送先姓'],
            [\App\Enums\OrderAddress\Type::Bill, 'bill_address', '請求先姓'],
        ] as $row) {
            $this->assertEquals($sentPayload[$row[1]]['type'], $row[0], '住所タイプが一致する');
            $this->assertEquals($sentPayload[$row[1]]['lname'], $row[2], '姓が一致する');
            $this->assertTrue(isset($sentPayload[$row[1]]['fname']), '名がセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['lkana']), '姓(かな)がセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['fkana']), '名(かな)がセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['tel']), '電話番号がセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['pref_id']), '都道府県がセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['zip']), '郵便番号がセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['city']), 'cityがセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['town']), 'townがセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['address']), 'addressがセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['building']), 'buildingがセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['email']), 'メールアドレスがセットされている');
        }
    }

    /**
     * @return void
     */
    public function testUpdateMemberPurchase()
    {
        $purchaseAdapter = resolve(PurchaseAdapter::class);
        $http = resolve(MemberHttpCommunication::class);
        $adapter = new MemberPurchase(resolve(OrderPortion::class), resolve(OrderRepository::class), $http, resolve(PurchaseHttpCommunication::class));
        $order = \App\Models\Order::find(1);
        $ecBill = $purchaseAdapter->makeEcBill($order);
        $adapter->updateMemberPurchaseAndUpdateTax($order, $ecBill);

        $sentPayload = $http->lastRequestedParams[2];

        // JAN: 101
        $detail = Arr::find($sentPayload['details'], function ($element) {
            return $element['item_jan_code'] === '101';
        });
        $this->assertTrue($detail !== false, 'jan101が存在する');
        $this->assertEquals(1000, $detail['retail_price'], '上代が一致する');
        $this->assertEquals(1602, $detail['sales_price'], '販売金額が一致する');
        $this->assertEquals(2, $detail['amount'], '数量が一致する');
        $this->assertEquals(18, $detail['use_point'], 'ポイント按分が一致する');
        $this->assertEquals(\App\Enums\Ymdy\Member\PvDiv::Bargain, $detail['pb_div'], 'P/B区分が正しい');
        $this->assertEquals(\App\Enums\Ymdy\Member\CrosspointPvDiv::Bargain, $detail['crosspoint_pb_div'], 'クロスポイントP/B区分が正しい');
        $this->assertEquals(\App\Enums\OrderDetail\TaxRateId::Rate10, $detail['tax_rate_id'], '消費税IDが正しい');
        $this->assertEquals(\App\Enums\Ymdy\Member\TaxType::Included, $detail['tax_type'], '消費税種別が正しい');
        $this->assertEquals(145, $detail['tax'], '消費税が正しい');
        $this->assertEquals(1457, $detail['tax_excluded_sale_price'], '税抜販売金額が正しい');
        $this->assertEquals(2, count($detail['discounts']), '割引の件数が正しい');
        $this->assertTrue(isset($detail['item_published_date']), '商品公開日が存在する');

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::Normal;
        });
        $this->assertTrue($discount !== false, '通常割引の割引が存在する');
        $this->assertEquals(200, $discount['price'], '通常割引の割引金額が正しい');

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::Coupon;
        });
        $this->assertTrue($discount !== false, 'クーポン割引の割引が存在する');
        $this->assertEquals(180, $discount['price'], 'クーポン割引の割引金額が正しい');

        // JAN: 102
        $detail = Arr::find($sentPayload['details'], function ($element) {
            return $element['item_jan_code'] === '102';
        });
        $this->assertTrue($detail !== false);
        $this->assertEquals(2000, $detail['retail_price']);
        $this->assertEquals(1282, $detail['sales_price']);
        $this->assertEquals(1, $detail['amount']);
        $this->assertEquals(14, $detail['use_point']);
        $this->assertEquals(\App\Enums\Ymdy\Member\PvDiv::Bargain, $detail['pb_div']);
        $this->assertEquals(\App\Enums\Ymdy\Member\CrosspointPvDiv::Bargain, $detail['crosspoint_pb_div']);
        $this->assertEquals(\App\Enums\OrderDetail\TaxRateId::Rate10, $detail['tax_rate_id']);
        $this->assertEquals(\App\Enums\Ymdy\Member\TaxType::Included, $detail['tax_type']);
        $this->assertEquals(116, $detail['tax']);
        $this->assertEquals(1166, $detail['tax_excluded_sale_price']);
        $this->assertEquals(3, count($detail['discounts']));
        $this->assertEquals(1, $detail['color']['id'], '色が一致する');
        $this->assertEquals('ホワイト', $detail['color']['display_name'], '色が一致する');
        $this->assertEquals(1, $detail['size']['id'], 'サイズが一致する');
        $this->assertEquals('フリー', $detail['size']['name'], 'サイズが一致する');

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::Member;
        });
        $this->assertTrue($discount !== false);
        $this->assertEquals(400, $discount['price']);

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::EventBundle;
        });
        $this->assertTrue($discount !== false);
        $this->assertEquals(160, $discount['price']);

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::Coupon;
        });
        $this->assertTrue($discount !== false);
        $this->assertEquals(144, $discount['price']);

        // JAN: 103
        $detail = Arr::find($sentPayload['details'], function ($element) {
            return $element['item_jan_code'] === '103';
        });
        $this->assertTrue($detail !== false);
        $this->assertEquals(2000, $detail['retail_price']);
        $this->assertEquals(1282, $detail['sales_price']);
        $this->assertEquals(1, $detail['amount']);
        $this->assertEquals(14, $detail['use_point']);
        $this->assertEquals(\App\Enums\Ymdy\Member\PvDiv::Bargain, $detail['pb_div']);
        $this->assertEquals(\App\Enums\Ymdy\Member\CrosspointPvDiv::Bargain, $detail['crosspoint_pb_div']);
        $this->assertEquals(\App\Enums\OrderDetail\TaxRateId::Rate10, $detail['tax_rate_id']);
        $this->assertEquals(\App\Enums\Ymdy\Member\TaxType::Included, $detail['tax_type']);
        $this->assertEquals(116, $detail['tax']);
        $this->assertEquals(1166, $detail['tax_excluded_sale_price']);
        $this->assertEquals(3, count($detail['discounts']));

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::Member;
        });
        $this->assertTrue($discount !== false);
        $this->assertEquals(400, $discount['price']);

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::EventBundle;
        });
        $this->assertTrue($discount !== false);
        $this->assertEquals(160, $discount['price']);

        $discount = Arr::find($detail['discounts'], function ($element) {
            return $element['type'] === \App\Enums\Ymdy\Member\Purchase\DiscountType::Coupon;
        });
        $this->assertTrue($discount !== false);
        $this->assertEquals(144, $discount['price']);

        // JAN: 104
        $detail = Arr::find($sentPayload['details'], function ($element) {
            return $element['item_jan_code'] === '104';
        });
        $this->assertTrue($detail !== false);
        $this->assertEquals(5000, $detail['retail_price']);
        $this->assertEquals(4946, $detail['sales_price']);
        $this->assertEquals(1, $detail['amount']);
        $this->assertEquals(54, $detail['use_point']);
        $this->assertEquals(\App\Enums\Ymdy\Member\PvDiv::Proper, $detail['pb_div']);
        $this->assertEquals(\App\Enums\Ymdy\Member\CrosspointPvDiv::Proper, $detail['crosspoint_pb_div']);
        $this->assertEquals(\App\Enums\OrderDetail\TaxRateId::Rate10, $detail['tax_rate_id']);
        $this->assertEquals(\App\Enums\Ymdy\Member\TaxType::Included, $detail['tax_type']);
        $this->assertEquals(449, $detail['tax']);
        $this->assertEquals(4497, $detail['tax_excluded_sale_price']);
        $this->assertEquals(0, count($detail['discounts']));

        // 受注全体
        $this->assertEquals('888', $sentPayload['code'], '商品コードが一致する');
        $this->assertEquals(\App\Enums\Order\PaymentType::Bank, $sentPayload['payment']['id'], '支払い方法が一致する');
        $this->assertEquals(\App\Enums\Order\PaymentType::Bank()->description, $sentPayload['payment']['name'], '支払い方法名称が一致する');
        $this->assertEquals('2020-10-10 00:00:00', Carbon::parse($sentPayload['delivery_date'])->format('Y-m-d H:i:s'), '発送日が一致する');
        $this->assertEquals(9612, $sentPayload['total_price'], '合計金額が一致する');
        $this->assertEquals(0, $sentPayload['delivery_fee'], '配送料金が一致する');
        $this->assertEquals('クーポン送料割引', $sentPayload['delivery_fee_memo'], '配送料金メモが一致する');
        $this->assertEquals(500, $sentPayload['payment_fee'], '手数料が一致する');
        $this->assertEquals(871, $sentPayload['total_tax'], '合計消費税が一致する');
        $this->assertEquals(8741, $sentPayload['tax_excluded_total_price'], '税抜合計金額が一致する');
        $this->assertEquals(4, $sentPayload['detail_num'], '詳細件数が一致する');
        foreach ([
            [\App\Enums\OrderAddress\Type::Member, 'member_address', '会員住所姓'],
            [\App\Enums\OrderAddress\Type::Delivery, 'delivery_address', '配送先姓'],
            [\App\Enums\OrderAddress\Type::Bill, 'bill_address', '請求先姓'],
        ] as $row) {
            $this->assertEquals($sentPayload[$row[1]]['type'], $row[0], '住所タイプが一致する');
            $this->assertEquals($sentPayload[$row[1]]['lname'], $row[2], '姓が一致する');
            $this->assertTrue(isset($sentPayload[$row[1]]['fname']), '名がセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['lkana']), '姓(かな)がセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['fkana']), '名(かな)がセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['tel']), '電話番号がセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['pref_id']), '都道府県がセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['zip']), '郵便番号がセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['city']), 'cityがセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['town']), 'townがセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['address']), 'addressがセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['building']), 'buildingがセットされている');
            $this->assertTrue(isset($sentPayload[$row[1]]['email']), 'メールアドレスがセットされている');
        }
    }
}
