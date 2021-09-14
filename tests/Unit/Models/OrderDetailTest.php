<?php

namespace Tests\Unit\Models;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrderDetailTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->truncateTables();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        factory(\App\Models\Item::class)->create(['term_id' => 1, 'organization_id' => 1, 'division_id' => 1, 'department_id' => 1, 'brand_id' => 1]);
        factory(\App\Models\ItemDetail::class)->create(['item_id' => 1, 'color_id' => 1, 'size_id' => 1]);
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 1]);
        factory(\App\Models\Order::class)->create();
        factory(\App\Models\OrderDetail::class)->create(['retail_price' => 1000, 'item_detail_id' => 1]);
        factory(\App\Models\OrderDetailUnit::class)->create(['amount' => 2, 'order_detail_id' => 1, 'item_detail_identification_id' => 1]);
        factory(\App\Models\Event::class)->create();
        factory(\App\Models\OrderDiscount::class)->create([
            'orderable_id' => 1,
            'orderable_type' => \App\Models\OrderDetail::class,
            'type' => \App\Enums\OrderDiscount\Type::Normal,
            'method' => \App\Enums\OrderDiscount\Method::Percentile,
            'discountable_type' => null,
            'discountable_id' => null,
            'discount_rate' => 0.1,
        ]);
        factory(\App\Models\OrderDiscount::class)->create([
            'orderable_id' => 1,
            'orderable_type' => \App\Models\OrderDetail::class,
            'type' => \App\Enums\OrderDiscount\Type::EventBundle,
            'method' => \App\Enums\OrderDiscount\Method::Percentile,
            'discountable_type' => \App\Models\Event::class,
            'discountable_id' => 1,
            'discount_rate' => 0.2,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function tearDown(): void
    {
        $this->truncateTables();
        parent::tearDown();
    }

    private function truncateTables()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\Item::truncate();
        \App\Models\ItemDetail::truncate();
        \App\Models\ItemDetailIdentification::truncate();
        \App\Models\Order::truncate();
        \App\Models\OrderDetail::truncate();
        \App\Models\OrderDetailUnit::truncate();
        \App\Models\OrderDiscount::truncate();
        \App\Models\OrderLog::truncate();
        \App\Models\OrderDetailLog::truncate();
        \App\Models\OrderDetailUnitLog::truncate();
        \App\Models\OrderDiscountLog::truncate();
        \App\Models\Event::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function testGetAmountAttribute()
    {
        $orderDetail = \App\Models\OrderDetail::find(1);
        $this->assertEquals(2, $orderDetail->getAmountAttribute(), '数量が一致する');
    }

    public function testGetDisplayedDiscountRateAttribute()
    {
        $orderDetail = \App\Models\OrderDetail::with('displayedDiscount')->find(1);
        $this->assertEquals(0.1, $orderDetail->getDisplayedDiscountRateAttribute(), '割引率が一致する');
    }

    public function testGetBundleDiscountRateAttribute()
    {
        $orderDetail = \App\Models\OrderDetail::with('bundleSaleDiscount')->find(1);
        $this->assertEquals(0.2, $orderDetail->getBundleDiscountRateAttribute(), 'バンドル販売割引率が一致する');
    }

    public function testGetDisplayedSalePriceAttribute()
    {
        $orderDetail = \App\Models\OrderDetail::with('displayedDiscount')->find(1);
        $this->assertEquals(900, $orderDetail->getDisplayedSalePriceAttribute(), '割引価格が一致する');
    }

    public function testGetPriceBeforeOrderAttribute()
    {
        $orderDetail = \App\Models\OrderDetail::with(['displayedDiscount', 'bundleSaleDiscount'])->find(1);
        $this->assertEquals(720, $orderDetail->getPriceBeforeOrderAttribute(), '販売価格が一致する');
    }

    public function testGetTotalPriceBeforeOrderAttribute()
    {
        $orderDetail = \App\Models\OrderDetail::with(['displayedDiscount', 'bundleSaleDiscount'])->find(1);
        $this->assertEquals(1440, $orderDetail->getTotalPriceBeforeOrderAttribute(), '合計販売価格が一致する');
    }
}
