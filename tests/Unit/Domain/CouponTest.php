<?php

namespace Tests\Unit\Domain;

use App\Domain\Coupon;
use App\HttpCommunication\Ymdy\Mock\Member as MemberHttpCommunication;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CouponTest extends TestCase
{
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
        \App\Models\Order::truncate();
        \App\Models\OrderDetail::truncate();
        \App\Models\OrderDetailUnit::truncate();
        \App\Models\OrderDiscount::truncate();
        \App\Models\OrderUsedCoupon::truncate();
        \App\Models\OrderLog::truncate();
        \App\Models\OrderDetailLog::truncate();
        \App\Models\OrderDetailUnitLog::truncate();
        \App\Models\OrderDiscountLog::truncate();
        \App\Models\OrderUsedCouponLog::truncate();
    }

    public function testUpdateItemCouponOrderDiscount()
    {
        $this->prepareTestUpdateItemCouponOrderDiscount();

        $http = resolve(MemberHttpCommunication::class);
        $http->setDummyResponse('showCoupon', [
            'coupon' => [
                'id' => 1,
                'name' => 'クーポン1',
                'target_member_type' => '1',
                'member_group_id' => null,
                'member_data' => [],
                'password' => null,
                'target_shop_type' => '1',
                'shop_data' => [],
                'issuance_limit' => null,
                'usage_number_limit' => 1,
                'image_path' => null,
                'start_dt' => '2020-01-01 00:00:00',
                'end_dt' => '2025-01-01 00:00:00',
                'free_shipping_flag' => 0,
                'discount_item_flag' => 1,
                'discount_type' => '2',
                'discount_amount' => null,
                'discount_rate' => 0.1,
                'target_item_type' => '2',
                'item_data' => [2, 3],
                'usage_amount_term_flag' => 0,
                'usage_amount_minimum' => null,
                'usage_amount_maximum' => null,
                'is_combinable' => 0,
                'description' => 'クーポン1の説明',
                'approval_status' => '2',
                'created_at' => '2020-11-09 13:58:09',
                'updated_at' => '2020-11-11 17:39:47',
                'member_coupons_count' => 0,
            ],
        ]);
        $service = new Coupon($http, resolve(\App\Repositories\OrderDiscountRepository::class), resolve(\App\Repositories\OrderUsedCouponRepository::class));

        // (1) 初期状態
        $order = \App\Models\Order::find(1);
        $this->assertEquals(0, $order->orderUsedCoupons->sum('item_applied_price'), '割引金額');

        // (2) クーポン追加
        factory(\App\Models\OrderDetail::class)->create(['retail_price' => 1000, 'item_detail_id' => 2, 'order_id' => 1]);
        factory(\App\Models\OrderDetailUnit::class)->create(['amount' => 2, 'order_detail_id' => 2, 'item_detail_identification_id' => 2]);
        \App\Models\OrderUsedCoupon::create(['order_id' => 1, 'coupon_id' => 1, 'target_order_detail_ids' => [2]]);
        \App\Models\OrderDiscount::create([
            'orderable_id' => 1,
            'orderable_type' => \App\Models\Order::class,
            'unit_applied_price' => null,
            'applied_price' => 200,
            'type' => \App\Enums\OrderDiscount\Type::CouponItem,
            'method' => \App\Enums\OrderDiscount\Method::Percentile,
            'discount_rate' => 0.1,
            'discountable_type' => \App\Models\OrderUsedCoupon::class,
            'discountable_id' => 1,
        ]);
        $service->updateOrderRelatedCouponState($order);

        $this->assertEquals(200, $order->orderUsedCoupons->sum('item_applied_price'), '割引金額');

        $targetOrderDetailIds = $order->orderUsedCoupons->first()->target_order_detail_ids;
        $this->assertEquals(1, count($targetOrderDetailIds));
        $this->assertEquals(2, $targetOrderDetailIds[0]);

        // (3) 受注商品追加
        factory(\App\Models\OrderDetail::class)->create(['retail_price' => 1000, 'item_detail_id' => 3, 'order_id' => 1]);
        factory(\App\Models\OrderDetailUnit::class)->create(['amount' => 1, 'order_detail_id' => 3, 'item_detail_identification_id' => 3]);
        $service->updateOrderRelatedCouponState($order);

        $this->assertEquals(300, $order->orderUsedCoupons->sum('item_applied_price'), '割引金額');

        $targetOrderDetailIds = $order->orderUsedCoupons->first()->target_order_detail_ids;
        $this->assertEquals(2, count($targetOrderDetailIds));
        foreach ([2, 3] as $id) {
            $this->assertTrue(in_array($id, $targetOrderDetailIds));
        }

        // (4) 受注削除
        \App\Models\OrderDetailUnit::find(3)->fill(['amount' => 0])->save();

        $service->updateOrderRelatedCouponState($order);
        $orderUsedCoupon = $order->orderUsedCoupons->first();
        $this->assertEquals(200, $orderUsedCoupon->item_applied_price, '割引金額');
        $this->assertEquals(1, count($orderUsedCoupon->target_order_detail_ids));
        $this->assertEquals(2, $orderUsedCoupon->target_order_detail_ids[0]);

        // (5) 受注削除2
        \App\Models\OrderDetailUnit::find(2)->fill(['amount' => 0])->save();
        $service->updateOrderRelatedCouponState($order);
        $this->assertEquals(0, $order->orderUsedCoupons->count());
    }

    private function prepareTestUpdateItemCouponOrderDiscount()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        factory(\App\Models\Item::class)->create(['term_id' => 1, 'organization_id' => 1, 'division_id' => 1, 'department_id' => 1, 'brand_id' => 1]);
        factory(\App\Models\Item::class)->create(['term_id' => 1, 'organization_id' => 1, 'division_id' => 1, 'department_id' => 1, 'brand_id' => 1]);
        factory(\App\Models\Item::class)->create(['term_id' => 1, 'organization_id' => 1, 'division_id' => 1, 'department_id' => 1, 'brand_id' => 1]);

        factory(\App\Models\ItemDetail::class)->create(['item_id' => 1, 'color_id' => 1, 'size_id' => 1]);
        factory(\App\Models\ItemDetail::class)->create(['item_id' => 2, 'color_id' => 1, 'size_id' => 1]);
        factory(\App\Models\ItemDetail::class)->create(['item_id' => 3, 'color_id' => 1, 'size_id' => 1]);

        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 1]);
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 2]);
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 3]);

        factory(\App\Models\Order::class)->create();

        factory(\App\Models\OrderDetail::class)->create(['retail_price' => 1000, 'item_detail_id' => 1, 'order_id' => 1]);

        factory(\App\Models\OrderDetailUnit::class)->create(['amount' => 2, 'order_detail_id' => 1, 'item_detail_identification_id' => 1]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function testAddCoupon()
    {
        $this->prepareTestAddCoupon();

        $coupon = new \App\Entities\Ymdy\Member\Coupon([
            'id' => 1,
            'name' => 'クーポン1',
            'target_member_type' => '1',
            'member_group_id' => null,
            'member_data' => [],
            'password' => null,
            'target_shop_type' => '1',
            'shop_data' => [],
            'issuance_limit' => null,
            'usage_number_limit' => 1,
            'image_path' => null,
            'start_dt' => '2020-01-01 00:00:00',
            'end_dt' => '2025-01-01 00:00:00',
            'free_shipping_flag' => 0,
            'discount_item_flag' => 1,
            'discount_type' => '2',
            'discount_amount' => null,
            'discount_rate' => 0.1,
            'target_item_type' => '2',
            'item_data' => [2, 3],
            'usage_amount_term_flag' => 0,
            'usage_amount_minimum' => null,
            'usage_amount_maximum' => null,
            'is_combinable' => 0,
            'description' => 'クーポン1の説明',
            'approval_status' => '2',
            'created_at' => '2020-11-09 13:58:09',
            'updated_at' => '2020-11-11 17:39:47',
            'member_coupons_count' => 0,
        ]);

        $service = new Coupon(resolve(MemberHttpCommunication::class), resolve(\App\Repositories\OrderDiscountRepository::class), resolve(\App\Repositories\OrderUsedCouponRepository::class));

        $order = \App\Models\Order::with('orderUsedCoupons')->find(1);

        $this->assertEquals(0, $order->orderUsedCoupons->count());

        $service->addCoupon($coupon, $order);

        $order->load('orderUsedCoupons');

        $this->assertEquals(1, $order->orderUsedCoupons->count());

        $orderUsedCoupon = $order->orderUsedCoupons->first();
        $this->assertEquals(300, $orderUsedCoupon->item_applied_price, '割引金額');
        $this->assertEquals(2, count($orderUsedCoupon->target_order_detail_ids));
        foreach ([2, 3] as $id) {
            $this->assertTrue(in_array($id, $orderUsedCoupon->target_order_detail_ids));
        }
    }

    private function prepareTestAddCoupon()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        factory(\App\Models\Item::class)->create(['term_id' => 1, 'organization_id' => 1, 'division_id' => 1, 'department_id' => 1, 'brand_id' => 1]);
        factory(\App\Models\Item::class)->create(['term_id' => 1, 'organization_id' => 1, 'division_id' => 1, 'department_id' => 1, 'brand_id' => 1]);
        factory(\App\Models\Item::class)->create(['term_id' => 1, 'organization_id' => 1, 'division_id' => 1, 'department_id' => 1, 'brand_id' => 1]);

        factory(\App\Models\ItemDetail::class)->create(['item_id' => 1, 'color_id' => 1, 'size_id' => 1]);
        factory(\App\Models\ItemDetail::class)->create(['item_id' => 2, 'color_id' => 1, 'size_id' => 1]);
        factory(\App\Models\ItemDetail::class)->create(['item_id' => 3, 'color_id' => 1, 'size_id' => 1]);

        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 1]);
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 2]);
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 3]);

        factory(\App\Models\Order::class)->create();

        factory(\App\Models\OrderDetail::class)->create(['retail_price' => 1000, 'item_detail_id' => 1, 'order_id' => 1]);
        factory(\App\Models\OrderDetail::class)->create(['retail_price' => 1000, 'item_detail_id' => 2, 'order_id' => 1]);
        factory(\App\Models\OrderDetail::class)->create(['retail_price' => 1000, 'item_detail_id' => 3, 'order_id' => 1]);

        factory(\App\Models\OrderDetailUnit::class)->create(['amount' => 2, 'order_detail_id' => 1, 'item_detail_identification_id' => 1]);
        factory(\App\Models\OrderDetailUnit::class)->create(['amount' => 2, 'order_detail_id' => 2, 'item_detail_identification_id' => 2]);
        factory(\App\Models\OrderDetailUnit::class)->create(['amount' => 1, 'order_detail_id' => 3, 'item_detail_identification_id' => 3]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
