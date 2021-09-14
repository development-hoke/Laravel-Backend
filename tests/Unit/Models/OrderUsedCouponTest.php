<?php

namespace Tests\Unit\Domain;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CouponTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        factory(\App\Models\Order::class)->create();
        \App\Models\OrderUsedCoupon::create([
            'order_id' => 1,
            'coupon_id' => 1,
            'target_order_detail_ids' => [1, 2, 3],
        ]);
        \App\Models\OrderDiscount::create([
            'orderable_id' => 1,
            'orderable_type' => \App\Models\Order::class,
            'applied_price' => 5000,
            'type' => \App\Enums\OrderDiscount\Type::CouponItem,
            'method' => \App\Enums\OrderDiscount\Method::Fixed,
            'discount_price' => 5000,
            'discountable_type' => \App\Models\OrderUsedCoupon::class,
            'discountable_id' => 1,
        ]);
        \App\Models\OrderDiscount::create([
            'orderable_id' => 1,
            'orderable_type' => \App\Models\Order::class,
            'applied_price' => 1080,
            'type' => \App\Enums\OrderDiscount\Type::CouponDeliveryFee,
            'method' => \App\Enums\OrderDiscount\Method::Fixed,
            'discount_price' => 1080,
            'discountable_type' => \App\Models\OrderUsedCoupon::class,
            'discountable_id' => 1,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function tearDown(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\Order::truncate();
        \App\Models\OrderLog::truncate();
        \App\Models\OrderDiscount::truncate();
        \App\Models\OrderDiscountLog::truncate();
        \App\Models\OrderUsedCoupon::truncate();
        \App\Models\OrderUsedCouponLog::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        parent::tearDown();
    }

    public function testGetItemAppliedPriceAttribute()
    {
        $orderUsedCoupon = \App\Models\OrderUsedCoupon::find(1);
        $this->assertEquals(5000, $orderUsedCoupon->getItemAppliedPriceAttribute());
    }

    public function testGetDeliveryFeeAppliedPriceAttribute()
    {
        $orderUsedCoupon = \App\Models\OrderUsedCoupon::find(1);
        $this->assertEquals(1080, $orderUsedCoupon->getDeliveryFeeAppliedPriceAttribute());
    }
}
