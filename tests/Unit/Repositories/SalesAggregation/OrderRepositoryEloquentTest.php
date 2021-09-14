<?php

namespace Tests\Unit\Repositories\SalesAggregation;

use App\Criteria\SalesAggregation\AdminOrderCriteria;
use App\Enums\OrderAggregation\By as ByEnum;
use App\Enums\OrderAggregation\Group1 as Group1Enum;
use App\Enums\OrderAggregation\Group2 as Group2Enum;
use App\Enums\OrderAggregation\Unit as UnitEnum;
use App\Repositories\SalesAggregation\ItemRepositoryEloquent;
use App\Repositories\SalesAggregation\OrderRepositoryEloquent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD)
 */
class OrderRepositoryEloquentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->truncateDatabase();

        Artisan::call('db:seed', ['--class' => 'ColorsTableSeeder']);
        Artisan::call('db:seed', ['--class' => 'SizesTableSeeder']);

        factory(\App\Models\Term::class, 4)->create();
        factory(\App\Models\Brand::class, 24)->create();

        factory(\App\Models\Organization::class)->create(['id' => 1]);
        factory(\App\Models\Organization::class)->create(['id' => 2]);
        factory(\App\Models\DepartmentGroup::class)->create(['id' => 1]);
        factory(\App\Models\Department::class)->create(['id' => 1, 'department_group_id' => 1]);
        factory(\App\Models\Department::class)->create(['id' => 2, 'department_group_id' => 1]);
        factory(\App\Models\Division::class)->create(['id' => 1]);
        factory(\App\Models\Division::class)->create(['id' => 2]);

        foreach ([
            1 => ['parent_id' => null, 'root_id' => 1, 'level' => 1, 'name' => 'cat 1', 'sort' => 1, '_lft' => 1, '_rgt' => 1],
            11 => ['parent_id' => 1, 'root_id' => 1, 'level' => 2, 'name' => 'cat 11', 'sort' => 1, '_lft' => 1, '_rgt' => 1],
            2 => ['parent_id' => null, 'root_id' => 2, 'level' => 1, 'name' => 'cat 1', 'sort' => 1, '_lft' => 1, '_rgt' => 1],
            21 => ['parent_id' => 2, 'root_id' => 2, 'level' => 1, 'name' => 'cat 1', 'sort' => 1, '_lft' => 1, '_rgt' => 1],
        ] as $id => $data) {
            $cat = new \App\Models\OnlineCategory();
            $cat->fill($data);
            $cat->id = $id;
            $cat->save();
        }

        factory(\App\Models\Item::class)->create(['organization_id' => 1, 'division_id' => 1, 'main_store_brand' => 1, 'department_id' => 1, 'product_number' => '0000-0001']); // 1
        factory(\App\Models\Item::class)->create(['organization_id' => 2, 'division_id' => 2, 'main_store_brand' => 2, 'department_id' => 1, 'product_number' => '0000-0002']); // 2
        factory(\App\Models\Item::class)->create(['organization_id' => 1, 'division_id' => 1, 'main_store_brand' => 1, 'department_id' => 2, 'product_number' => '0000-0003']); // 3
        factory(\App\Models\Item::class)->create(['organization_id' => 2, 'division_id' => 2, 'main_store_brand' => 2, 'department_id' => 2, 'product_number' => '0000-0004']); // 4

        \App\Models\ItemOnlineCategory::create(['item_id' => 1, 'online_category_id' => 11]); // 1
        \App\Models\ItemOnlineCategory::create(['item_id' => 2, 'online_category_id' => 11]); // 2
        \App\Models\ItemOnlineCategory::create(['item_id' => 3, 'online_category_id' => 21]); // 3
        \App\Models\ItemOnlineCategory::create(['item_id' => 4, 'online_category_id' => 21]); // 4

        factory(\App\Models\ItemDetail::class)->create(['item_id' => 1, 'sku_number' => '01-01-2639-210']); // 1
        factory(\App\Models\ItemDetail::class)->create(['item_id' => 1, 'sku_number' => '01-01-2639-211']); // 2
        factory(\App\Models\ItemDetail::class)->create(['item_id' => 2, 'sku_number' => '01-01-2639-212']); // 3
        factory(\App\Models\ItemDetail::class)->create(['item_id' => 2, 'sku_number' => '01-01-2639-213']); // 4
        factory(\App\Models\ItemDetail::class)->create(['item_id' => 3, 'sku_number' => '01-01-2639-214']); // 5
        factory(\App\Models\ItemDetail::class)->create(['item_id' => 3, 'sku_number' => '01-01-2639-215']); // 6
        factory(\App\Models\ItemDetail::class)->create(['item_id' => 4, 'sku_number' => '01-01-2639-216']); // 7
        factory(\App\Models\ItemDetail::class)->create(['item_id' => 4, 'sku_number' => '01-01-2639-217']); // 8

        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 1, 'jan_code' => '01-01-2639-217-58']); // 1
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 1, 'jan_code' => '01-01-2639-217-59']); // 2
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 2, 'jan_code' => '01-01-2639-217-60']); // 3
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 2, 'jan_code' => '01-01-2639-217-61']); // 4
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 3, 'jan_code' => '01-01-2639-217-62']); // 5
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 3, 'jan_code' => '01-01-2639-217-63']); // 6
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 4, 'jan_code' => '01-01-2639-217-64']); // 7
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 4, 'jan_code' => '01-01-2639-217-65']); // 8
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 5, 'jan_code' => '01-01-2639-217-66']); // 9
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 5, 'jan_code' => '01-01-2639-217-67']); // 10
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 6, 'jan_code' => '01-01-2639-217-68']); // 11
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 6, 'jan_code' => '01-01-2639-217-69']); // 12
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 7, 'jan_code' => '01-01-2639-217-70']); // 13
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 7, 'jan_code' => '01-01-2639-217-71']); // 14
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 8, 'jan_code' => '01-01-2639-217-72']); // 15
        factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => 8, 'jan_code' => '01-01-2639-217-73']); // 16

        // orders
        factory(\App\Models\Order::class)->create(['order_date' => Carbon::create(2020, 1, 6), 'deliveryed_date' => Carbon::create(2020, 2, 3)]); // 1
        factory(\App\Models\Order::class)->create(['order_date' => Carbon::create(2020, 1, 6), 'deliveryed_date' => Carbon::create(2020, 2, 3)]); // 2
        factory(\App\Models\Order::class)->create(['order_date' => Carbon::create(2020, 1, 6), 'deliveryed_date' => Carbon::create(2020, 2, 3)]); // 3
        factory(\App\Models\Order::class)->create(['order_date' => Carbon::create(2020, 1, 6), 'deliveryed_date' => Carbon::create(2020, 2, 3)]); // 4
        factory(\App\Models\Order::class)->create(['order_date' => Carbon::create(2020, 1, 6), 'deliveryed_date' => Carbon::create(2020, 2, 3)]); // 5
        factory(\App\Models\Order::class)->create(['order_date' => Carbon::create(2020, 1, 6), 'deliveryed_date' => Carbon::create(2020, 2, 3)]); // 6
        factory(\App\Models\Order::class)->create(['order_date' => Carbon::create(2020, 1, 6), 'deliveryed_date' => Carbon::create(2020, 2, 3)]); // 7
        factory(\App\Models\Order::class)->create(['order_date' => Carbon::create(2020, 1, 6), 'deliveryed_date' => Carbon::create(2020, 2, 3)]); // 8

        factory(\App\Models\Order::class)->create(['order_date' => Carbon::create(2020, 1, 12), 'deliveryed_date' => Carbon::create(2020, 2, 9)]); // 9
        factory(\App\Models\Order::class)->create(['order_date' => Carbon::create(2020, 1, 12), 'deliveryed_date' => Carbon::create(2020, 2, 9)]); // 10
        factory(\App\Models\Order::class)->create(['order_date' => Carbon::create(2020, 1, 12), 'deliveryed_date' => Carbon::create(2020, 2, 9)]); // 11
        factory(\App\Models\Order::class)->create(['order_date' => Carbon::create(2020, 1, 12), 'deliveryed_date' => Carbon::create(2020, 2, 9)]); // 12
        factory(\App\Models\Order::class)->create(['order_date' => Carbon::create(2020, 1, 16), 'deliveryed_date' => Carbon::create(2020, 2, 17)]); // 13
        factory(\App\Models\Order::class)->create(['order_date' => Carbon::create(2020, 1, 16), 'deliveryed_date' => Carbon::create(2020, 2, 17)]); // 14
        factory(\App\Models\Order::class)->create(['order_date' => Carbon::create(2020, 1, 16), 'deliveryed_date' => Carbon::create(2020, 2, 17)]); // 15
        factory(\App\Models\Order::class)->create(['order_date' => Carbon::create(2020, 1, 16), 'deliveryed_date' => Carbon::create(2020, 2, 17)]); // 16

        // order_details
        \App\Models\OrderDetail::create(['order_id' => 1, 'sale_type' => \App\Enums\Order\SaleType::Sale, 'item_detail_id' => 1, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 1
        \App\Models\OrderDetail::create(['order_id' => 2, 'sale_type' => \App\Enums\Order\SaleType::Sale, 'item_detail_id' => 3, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 2
        \App\Models\OrderDetail::create(['order_id' => 2, 'sale_type' => \App\Enums\Order\SaleType::Sale, 'item_detail_id' => 4, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 3

        \App\Models\OrderDetail::create(['order_id' => 3, 'sale_type' => \App\Enums\Order\SaleType::Sale, 'item_detail_id' => 5, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 4
        \App\Models\OrderDetail::create(['order_id' => 4, 'sale_type' => \App\Enums\Order\SaleType::Sale, 'item_detail_id' => 7, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 5
        \App\Models\OrderDetail::create(['order_id' => 4, 'sale_type' => \App\Enums\Order\SaleType::Sale, 'item_detail_id' => 8, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 6

        \App\Models\OrderDetail::create(['order_id' => 5, 'sale_type' => \App\Enums\Order\SaleType::Employee, 'item_detail_id' => 2, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 7
        \App\Models\OrderDetail::create(['order_id' => 6, 'sale_type' => \App\Enums\Order\SaleType::Employee, 'item_detail_id' => 3, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 8
        \App\Models\OrderDetail::create(['order_id' => 6, 'sale_type' => \App\Enums\Order\SaleType::Employee, 'item_detail_id' => 4, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 9

        \App\Models\OrderDetail::create(['order_id' => 7, 'sale_type' => \App\Enums\Order\SaleType::Employee, 'item_detail_id' => 6, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 10
        \App\Models\OrderDetail::create(['order_id' => 8, 'sale_type' => \App\Enums\Order\SaleType::Employee, 'item_detail_id' => 7, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 11
        \App\Models\OrderDetail::create(['order_id' => 8, 'sale_type' => \App\Enums\Order\SaleType::Employee, 'item_detail_id' => 8, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 12

        \App\Models\OrderDetail::create(['order_id' => 9, 'sale_type' => \App\Enums\Order\SaleType::Sale, 'item_detail_id' => 1, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 13
        \App\Models\OrderDetail::create(['order_id' => 11, 'sale_type' => \App\Enums\Order\SaleType::Sale, 'item_detail_id' => 7, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 14
        \App\Models\OrderDetail::create(['order_id' => 12, 'sale_type' => \App\Enums\Order\SaleType::Sale, 'item_detail_id' => 8, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 15

        \App\Models\OrderDetail::create(['order_id' => 13, 'sale_type' => \App\Enums\Order\SaleType::Sale, 'item_detail_id' => 2, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 16
        \App\Models\OrderDetail::create(['order_id' => 15, 'sale_type' => \App\Enums\Order\SaleType::Sale, 'item_detail_id' => 7, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 17
        \App\Models\OrderDetail::create(['order_id' => 16, 'sale_type' => \App\Enums\Order\SaleType::Sale, 'item_detail_id' => 8, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 18

        \App\Models\OrderDetail::create(['order_id' => 10, 'sale_type' => \App\Enums\Order\SaleType::Employee, 'item_detail_id' => 1, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 19
        \App\Models\OrderDetail::create(['order_id' => 11, 'sale_type' => \App\Enums\Order\SaleType::Employee, 'item_detail_id' => 7, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 20
        \App\Models\OrderDetail::create(['order_id' => 12, 'sale_type' => \App\Enums\Order\SaleType::Employee, 'item_detail_id' => 8, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 21

        \App\Models\OrderDetail::create(['order_id' => 14, 'sale_type' => \App\Enums\Order\SaleType::Employee, 'item_detail_id' => 2, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 22
        \App\Models\OrderDetail::create(['order_id' => 15, 'sale_type' => \App\Enums\Order\SaleType::Employee, 'item_detail_id' => 7, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 23
        \App\Models\OrderDetail::create(['order_id' => 16, 'sale_type' => \App\Enums\Order\SaleType::Employee, 'item_detail_id' => 8, 'retail_price' => 1000, 'tax' => 100, 'tax_rate_id' => 1]); // 24

        // order_discounts
        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 1, 'applied_price' => 400, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::Normal, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 1
        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 2, 'applied_price' => 200, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::Member, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 2
        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 3, 'applied_price' => 200, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::Staff, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 3

        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 4, 'applied_price' => 400, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::EventSale, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 4
        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 5, 'applied_price' => 200, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::EventBundle, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 5
        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 6, 'applied_price' => 200, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::Normal, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 6

        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 7, 'applied_price' => 400, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::Member, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 7
        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 8, 'applied_price' => 200, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::Staff, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 8
        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 9, 'applied_price' => 200, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::EventSale, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 9

        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 10, 'applied_price' => 400, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::EventBundle, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 10
        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 11, 'applied_price' => 200, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::Normal, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 11
        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 12, 'applied_price' => 200, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::Member, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 12

        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 13, 'applied_price' => 400, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::Staff, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 13
        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 14, 'applied_price' => 200, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::EventSale, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 14
        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 15, 'applied_price' => 200, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::EventBundle, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 15

        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 16, 'applied_price' => 400, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::Normal, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 16
        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 17, 'applied_price' => 200, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::Member, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 17
        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 18, 'applied_price' => 200, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::Staff, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 18

        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 19, 'applied_price' => 400, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::EventSale, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 19
        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 20, 'applied_price' => 200, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::EventBundle, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 20
        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 21, 'applied_price' => 200, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::Normal, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 21

        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 22, 'applied_price' => 400, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::Member, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 22
        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 23, 'applied_price' => 200, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::Staff, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 23
        \App\Models\OrderDiscount::create(['orderable_type' => \App\Models\OrderDetail::class, 'orderable_id' => 24, 'applied_price' => 200, 'unit_applied_price' => 100, 'discount_rate' => 0.1, 'type' => \App\Enums\OrderDiscount\Type::EventSale, 'method' => \App\Enums\OrderDiscount\Method::Percentile]); // 24

        // order_detail_units
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 1, 'item_detail_identification_id' => 1, 'amount' => 2]); // 1, 1
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 1, 'item_detail_identification_id' => 2, 'amount' => 2]); // 2, 1
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 2, 'item_detail_identification_id' => 5, 'amount' => 2]); // 3, 3
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 3, 'item_detail_identification_id' => 7, 'amount' => 2]); // 4, 4

        \App\Models\OrderDetailUnit::create(['order_detail_id' => 4, 'item_detail_identification_id' => 9, 'amount' => 2]); // 5, 5
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 4, 'item_detail_identification_id' => 10, 'amount' => 2]); // 6, 5
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 5, 'item_detail_identification_id' => 13, 'amount' => 2]); // 7, 7
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 6, 'item_detail_identification_id' => 15, 'amount' => 2]); // 8, 8

        \App\Models\OrderDetailUnit::create(['order_detail_id' => 7, 'item_detail_identification_id' => 3, 'amount' => 2]); // 9, 2
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 7, 'item_detail_identification_id' => 4, 'amount' => 2]); // 10, 2
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 8, 'item_detail_identification_id' => 6, 'amount' => 2]); // 11, 3
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 9, 'item_detail_identification_id' => 8, 'amount' => 2]); // 12, 4

        \App\Models\OrderDetailUnit::create(['order_detail_id' => 10, 'item_detail_identification_id' => 11, 'amount' => 2]); // 13, 6
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 10, 'item_detail_identification_id' => 12, 'amount' => 2]); // 14, 6
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 11, 'item_detail_identification_id' => 14, 'amount' => 2]); // 15, 7
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 12, 'item_detail_identification_id' => 16, 'amount' => 2]); // 16, 8

        \App\Models\OrderDetailUnit::create(['order_detail_id' => 13, 'item_detail_identification_id' => 1, 'amount' => 2]); // 17, 1
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 13, 'item_detail_identification_id' => 2, 'amount' => 2]); // 18, 1
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 14, 'item_detail_identification_id' => 14, 'amount' => 2]); // 19, 7
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 15, 'item_detail_identification_id' => 16, 'amount' => 2]); // 20, 8

        \App\Models\OrderDetailUnit::create(['order_detail_id' => 16, 'item_detail_identification_id' => 3, 'amount' => 2]); // 21, 2
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 16, 'item_detail_identification_id' => 4, 'amount' => 2]); // 22, 2
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 17, 'item_detail_identification_id' => 13, 'amount' => 2]); // 23, 7
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 18, 'item_detail_identification_id' => 15, 'amount' => 2]); // 24, 8

        \App\Models\OrderDetailUnit::create(['order_detail_id' => 19, 'item_detail_identification_id' => 1, 'amount' => 2]); // 25, 1
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 19, 'item_detail_identification_id' => 2, 'amount' => 2]); // 26, 1
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 20, 'item_detail_identification_id' => 14, 'amount' => 2]); // 27, 7
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 21, 'item_detail_identification_id' => 16, 'amount' => 2]); // 28, 8

        \App\Models\OrderDetailUnit::create(['order_detail_id' => 22, 'item_detail_identification_id' => 3, 'amount' => 2]); // 29, 2
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 22, 'item_detail_identification_id' => 4, 'amount' => 2]); // 30, 2
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 23, 'item_detail_identification_id' => 13, 'amount' => 2]); // 31, 7
        \App\Models\OrderDetailUnit::create(['order_detail_id' => 24, 'item_detail_identification_id' => 15, 'amount' => 2]); // 32, 8
    }

    public function tearDown(): void
    {
        $this->truncateDatabase();
        parent::tearDown();
    }

    private function truncateDatabase()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\Color::truncate();
        \App\Models\Size::truncate();
        \App\Models\Term::truncate();
        \App\Models\Brand::truncate();
        \App\Models\Organization::truncate();
        \App\Models\DepartmentGroup::truncate();
        \App\Models\Department::truncate();
        \App\Models\Division::truncate();
        \App\Models\OnlineCategory::truncate();
        \App\Models\Item::truncate();
        \App\Models\ItemOnlineCategory::truncate();
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
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * @return array
     */
    public function aggregateTestDataProvider()
    {
        $baseParams = [
            'date_from' => '2020-01-01 00:00:00',
            'date_to' => '2020-03-01 00:00:00',
        ];

        $cases = [
            '受注日 x 日次 x 組織 x 部門' => [
                ['by' => ByEnum::Ordered, 'unit' => UnitEnum::Daily, 'group1' => Group1Enum::Organization, 'organization_id' => [1, 2], 'group2' => Group2Enum::Department, 'department_id' => [1, 2]],
                [
                    '2020-01-06' => [1 => [1 => 3600, 2 => 3600], 2 => [1 => 3600, 2 => 3600]],
                    '2020-01-12' => [1 => [1 => 3600], 2 => [2 => 3600]],
                    '2020-01-16' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '受注日 x 日次 x 事業部 x 部門' => [
                ['by' => ByEnum::Ordered, 'unit' => UnitEnum::Daily, 'group1' => Group1Enum::Division, 'organization_id' => [1, 2], 'group2' => Group2Enum::Department, 'department_id' => [1, 2]],
                [
                    '2020-01-06' => [1 => [1 => 3600, 2 => 3600], 2 => [1 => 3600, 2 => 3600]],
                    '2020-01-12' => [1 => [1 => 3600], 2 => [2 => 3600]],
                    '2020-01-16' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '受注日 x 日次 x ストラブランド x 部門' => [
                ['by' => ByEnum::Ordered, 'unit' => UnitEnum::Daily, 'group1' => Group1Enum::MainStoreBrand, 'organization_id' => [1, 2], 'group2' => Group2Enum::Department, 'department_id' => [1, 2]],
                [
                    '2020-01-06' => [1 => [1 => 3600, 2 => 3600], 2 => [1 => 3600, 2 => 3600]],
                    '2020-01-12' => [1 => [1 => 3600], 2 => [2 => 3600]],
                    '2020-01-16' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '受注日 x 週次 x 組織 x 部門' => [
                ['by' => ByEnum::Ordered, 'unit' => UnitEnum::Weekly, 'group1' => Group1Enum::Organization, 'organization_id' => [1, 2], 'group2' => Group2Enum::Department, 'department_id' => [1, 2]],
                [
                    '2020-01-06' => [1 => [1 => 7200, 2 => 3600], 2 => [1 => 3600, 2 => 7200]],
                    '2020-01-13' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '受注日 x 週次 x 事業部 x 部門' => [
                ['by' => ByEnum::Ordered, 'unit' => UnitEnum::Weekly, 'group1' => Group1Enum::Division, 'organization_id' => [1, 2], 'group2' => Group2Enum::Department, 'department_id' => [1, 2]],
                [
                    '2020-01-06' => [1 => [1 => 7200, 2 => 3600], 2 => [1 => 3600, 2 => 7200]],
                    '2020-01-13' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '受注日 x 週次 x ストラブランド x 部門' => [
                ['by' => ByEnum::Ordered, 'unit' => UnitEnum::Weekly, 'group1' => Group1Enum::MainStoreBrand, 'organization_id' => [1, 2], 'group2' => Group2Enum::Department, 'department_id' => [1, 2]],
                [
                    '2020-01-06' => [1 => [1 => 7200, 2 => 3600], 2 => [1 => 3600, 2 => 7200]],
                    '2020-01-13' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '受注日 x 月次 x 組織 x 部門' => [
                ['by' => ByEnum::Ordered, 'unit' => UnitEnum::Monthly, 'group1' => Group1Enum::Organization, 'organization_id' => [1, 2], 'group2' => Group2Enum::Department, 'department_id' => [1, 2]],
                ['2020-01-01' => [1 => [1 => 10800, 2 => 3600], 2 => [1 => 3600, 2 => 10800]]],
            ],
            '受注日 x 月次 x 事業部 x 部門' => [
                ['by' => ByEnum::Ordered, 'unit' => UnitEnum::Monthly, 'group1' => Group1Enum::Division, 'organization_id' => [1, 2], 'group2' => Group2Enum::Department, 'department_id' => [1, 2]],
                ['2020-01-01' => [1 => [1 => 10800, 2 => 3600], 2 => [1 => 3600, 2 => 10800]]],
            ],
            '受注日 x 月次 x ストラブランド x 部門' => [
                ['by' => ByEnum::Ordered, 'unit' => UnitEnum::Monthly, 'group1' => Group1Enum::MainStoreBrand, 'organization_id' => [1, 2], 'group2' => Group2Enum::Department, 'department_id' => [1, 2]],
                ['2020-01-01' => [1 => [1 => 10800, 2 => 3600], 2 => [1 => 3600, 2 => 10800]]],
            ],
            '発送日 x 日次 x 組織 x 部門' => [
                ['by' => ByEnum::Delivered, 'unit' => UnitEnum::Daily, 'group1' => Group1Enum::Organization, 'organization_id' => [1, 2], 'group2' => Group2Enum::Department, 'department_id' => [1, 2]],
                [
                    '2020-02-03' => [1 => [1 => 3600, 2 => 3600], 2 => [1 => 3600, 2 => 3600]],
                    '2020-02-09' => [1 => [1 => 3600], 2 => [2 => 3600]],
                    '2020-02-17' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '発送日 x 日次 x 事業部 x 部門' => [
                ['by' => ByEnum::Delivered, 'unit' => UnitEnum::Daily, 'group1' => Group1Enum::Division, 'organization_id' => [1, 2], 'group2' => Group2Enum::Department, 'department_id' => [1, 2]],
                [
                    '2020-02-03' => [1 => [1 => 3600, 2 => 3600], 2 => [1 => 3600, 2 => 3600]],
                    '2020-02-09' => [1 => [1 => 3600], 2 => [2 => 3600]],
                    '2020-02-17' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '発送日 x 日次 x ストラブランド x 部門' => [
                ['by' => ByEnum::Delivered, 'unit' => UnitEnum::Daily, 'group1' => Group1Enum::MainStoreBrand, 'organization_id' => [1, 2], 'group2' => Group2Enum::Department, 'department_id' => [1, 2]],
                [
                    '2020-02-03' => [1 => [1 => 3600, 2 => 3600], 2 => [1 => 3600, 2 => 3600]],
                    '2020-02-09' => [1 => [1 => 3600], 2 => [2 => 3600]],
                    '2020-02-17' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '発送日 x 週次 x 組織 x 部門' => [
                ['by' => ByEnum::Delivered, 'unit' => UnitEnum::Weekly, 'group1' => Group1Enum::Organization, 'organization_id' => [1, 2], 'group2' => Group2Enum::Department, 'department_id' => [1, 2]],
                [
                    '2020-02-03' => [1 => [1 => 7200, 2 => 3600], 2 => [1 => 3600, 2 => 7200]],
                    '2020-02-17' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '発送日 x 週次 x 事業部 x 部門' => [
                ['by' => ByEnum::Delivered, 'unit' => UnitEnum::Weekly, 'group1' => Group1Enum::Division, 'organization_id' => [1, 2], 'group2' => Group2Enum::Department, 'department_id' => [1, 2]],
                [
                    '2020-02-03' => [1 => [1 => 7200, 2 => 3600], 2 => [1 => 3600, 2 => 7200]],
                    '2020-02-17' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '発送日 x 週次 x ストラブランド x 部門' => [
                ['by' => ByEnum::Delivered, 'unit' => UnitEnum::Weekly, 'group1' => Group1Enum::MainStoreBrand, 'organization_id' => [1, 2], 'group2' => Group2Enum::Department, 'department_id' => [1, 2]],
                [
                    '2020-02-03' => [1 => [1 => 7200, 2 => 3600], 2 => [1 => 3600, 2 => 7200]],
                    '2020-02-17' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '発送日 x 月次 x 組織 x 部門' => [
                ['by' => ByEnum::Delivered, 'unit' => UnitEnum::Monthly, 'group1' => Group1Enum::Organization, 'organization_id' => [1, 2], 'group2' => Group2Enum::Department, 'department_id' => [1, 2]],
                ['2020-02-01' => [1 => [1 => 10800, 2 => 3600], 2 => [1 => 3600, 2 => 10800]]],
            ],
            '発送日 x 月次 x 事業部 x 部門' => [
                ['by' => ByEnum::Delivered, 'unit' => UnitEnum::Monthly, 'group1' => Group1Enum::Division, 'organization_id' => [1, 2], 'group2' => Group2Enum::Department, 'department_id' => [1, 2]],
                ['2020-02-01' => [1 => [1 => 10800, 2 => 3600], 2 => [1 => 3600, 2 => 10800]]],
            ],
            '発送日 x 月次 x ストラブランド x 部門' => [
                ['by' => ByEnum::Delivered, 'unit' => UnitEnum::Monthly, 'group1' => Group1Enum::MainStoreBrand, 'organization_id' => [1, 2], 'group2' => Group2Enum::Department, 'department_id' => [1, 2]],
                ['2020-02-01' => [1 => [1 => 10800, 2 => 3600], 2 => [1 => 3600, 2 => 10800]]],
            ],
        ];

        foreach ($cases as $i => $param) {
            $cases[$i][0] = array_merge($baseParams, $param[0]);
        }

        return $cases;
    }

    /**
     * @return void
     *
     * @dataProvider aggregateTestDataProvider
     */
    public function testAggregate($params, $expected)
    {
        $repository = app()->make(OrderRepositoryEloquent::class);
        $repository->pushCriteria(new AdminOrderCriteria($params));
        $results = $repository->aggregate($params, 50);

        $actual = [];

        foreach ($results as $row) {
            if (!isset($actual[$row->date])) {
                $actual[$row->date] = [];
            }

            if (!isset($actual[$row->date][$row->group1])) {
                $actual[$row->date][$row->group1] = [];
            }

            if (!isset($actual[$row->date][$row->group1][$row->group2])) {
                $actual[$row->date][$row->group1][$row->group2] = [];
            }

            if (!isset($actual[$row->date][$row->group1][$row->group2][$row->sale_type])) {
                $actual[$row->date][$row->group1][$row->group2][$row->sale_type] = 0;
            }

            $actual[$row->date][$row->group1][$row->group2][$row->sale_type] += $row->total_price;
        }

        foreach ($actual as $date => $group1) {
            foreach ($group1 as $group1Id => $group2) {
                foreach ($group2 as $group2Id => $saleTypes) {
                    foreach ($saleTypes as $price) {
                        if (!isset($expected[$date])) {
                            var_dump('$expected', $expected);
                            var_dump('$actual', $actual);

                            return $this->fail("date {$date}がありませんでした。");
                        }

                        if (!isset($expected[$date][$group1Id])) {
                            var_dump('$expected', $expected);
                            var_dump('$actual', $actual);

                            return $this->fail("date {$date} group1Id {$group1Id}がありませんでした。");
                        }

                        if (!isset($expected[$date][$group1Id][$group2Id])) {
                            var_dump('$expected', $expected);
                            var_dump('$actual', $actual);

                            return $this->fail("date {$date} group1Id {$group1Id} group2Id {$group2Id}がありませんでした。");
                        }

                        $this->assertEquals($expected[$date][$group1Id][$group2Id], $price);
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    public function aggregateWithCategoryTestDataProvider()
    {
        $baseParams = [
            'date_from' => '2020-01-01 00:00:00',
            'date_to' => '2020-03-01 00:00:00',
        ];

        $cases = [
            '受注日 x 日次 x 組織 x オンライン分類' => [
                ['by' => ByEnum::Ordered, 'unit' => UnitEnum::Daily, 'group1' => Group1Enum::Organization, 'organization_id' => [1, 2], 'group2' => Group2Enum::OnlineCategory, 'online_category_id' => [11, 21]],
                [
                    '2020-01-06' => [1 => [11 => 3600, 21 => 3600], 2 => [11 => 3600, 21 => 3600]],
                    '2020-01-12' => [1 => [11 => 3600], 2 => [21 => 3600]],
                    '2020-01-16' => [1 => [11 => 3600], 2 => [21 => 3600]],
                ],
            ],
            '受注日 x 日次 x 事業部 x オンライン分類' => [
                ['by' => ByEnum::Ordered, 'unit' => UnitEnum::Daily, 'group1' => Group1Enum::Division, 'organization_id' => [1, 2], 'group2' => Group2Enum::OnlineCategory, 'online_category_id' => [1, 2]],
                [
                    '2020-01-06' => [1 => [1 => 3600, 2 => 3600], 2 => [1 => 3600, 2 => 3600]],
                    '2020-01-12' => [1 => [1 => 3600], 2 => [2 => 3600]],
                    '2020-01-16' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '受注日 x 日次 x ストラブランド x オンライン分類' => [
                ['by' => ByEnum::Ordered, 'unit' => UnitEnum::Daily, 'group1' => Group1Enum::MainStoreBrand, 'organization_id' => [1, 2], 'group2' => Group2Enum::OnlineCategory, 'online_category_id' => [1, 2]],
                [
                    '2020-01-06' => [1 => [1 => 3600, 2 => 3600], 2 => [1 => 3600, 2 => 3600]],
                    '2020-01-12' => [1 => [1 => 3600], 2 => [2 => 3600]],
                    '2020-01-16' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '受注日 x 週次 x 組織 x オンライン分類' => [
                ['by' => ByEnum::Ordered, 'unit' => UnitEnum::Weekly, 'group1' => Group1Enum::Organization, 'organization_id' => [1, 2], 'group2' => Group2Enum::OnlineCategory, 'online_category_id' => [1, 2]],
                [
                    '2020-01-06' => [1 => [1 => 7200, 2 => 3600], 2 => [1 => 3600, 2 => 7200]],
                    '2020-01-13' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '受注日 x 週次 x 事業部 x オンライン分類' => [
                ['by' => ByEnum::Ordered, 'unit' => UnitEnum::Weekly, 'group1' => Group1Enum::Division, 'organization_id' => [1, 2], 'group2' => Group2Enum::OnlineCategory, 'online_category_id' => [1, 2]],
                [
                    '2020-01-06' => [1 => [1 => 7200, 2 => 3600], 2 => [1 => 3600, 2 => 7200]],
                    '2020-01-13' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '受注日 x 週次 x ストラブランド x オンライン分類' => [
                ['by' => ByEnum::Ordered, 'unit' => UnitEnum::Weekly, 'group1' => Group1Enum::MainStoreBrand, 'organization_id' => [1, 2], 'group2' => Group2Enum::OnlineCategory, 'online_category_id' => [1, 2]],
                [
                    '2020-01-06' => [1 => [1 => 7200, 2 => 3600], 2 => [1 => 3600, 2 => 7200]],
                    '2020-01-13' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '受注日 x 月次 x 組織 x オンライン分類' => [
                ['by' => ByEnum::Ordered, 'unit' => UnitEnum::Monthly, 'group1' => Group1Enum::Organization, 'organization_id' => [1, 2], 'group2' => Group2Enum::OnlineCategory, 'online_category_id' => [1, 2]],
                ['2020-01-01' => [1 => [1 => 10800, 2 => 3600], 2 => [1 => 3600, 2 => 10800]]],
            ],
            '受注日 x 月次 x 事業部 x オンライン分類' => [
                ['by' => ByEnum::Ordered, 'unit' => UnitEnum::Monthly, 'group1' => Group1Enum::Division, 'organization_id' => [1, 2], 'group2' => Group2Enum::OnlineCategory, 'online_category_id' => [1, 2]],
                ['2020-01-01' => [1 => [1 => 10800, 2 => 3600], 2 => [1 => 3600, 2 => 10800]]],
            ],
            '受注日 x 月次 x ストラブランド x オンライン分類' => [
                ['by' => ByEnum::Ordered, 'unit' => UnitEnum::Monthly, 'group1' => Group1Enum::MainStoreBrand, 'organization_id' => [1, 2], 'group2' => Group2Enum::OnlineCategory, 'online_category_id' => [1, 2]],
                ['2020-01-01' => [1 => [1 => 10800, 2 => 3600], 2 => [1 => 3600, 2 => 10800]]],
            ],
            '発送日 x 日次 x 組織 x オンライン分類' => [
                ['by' => ByEnum::Delivered, 'unit' => UnitEnum::Daily, 'group1' => Group1Enum::Organization, 'organization_id' => [1, 2], 'group2' => Group2Enum::OnlineCategory, 'online_category_id' => [11, 21]],
                [
                    '2020-02-03' => [1 => [11 => 3600, 21 => 3600], 2 => [11 => 3600, 21 => 3600]],
                    '2020-02-09' => [1 => [11 => 3600], 2 => [21 => 3600]],
                    '2020-02-17' => [1 => [11 => 3600], 2 => [21 => 3600]],
                ],
            ],
            '発送日 x 日次 x 事業部 x オンライン分類' => [
                ['by' => ByEnum::Delivered, 'unit' => UnitEnum::Daily, 'group1' => Group1Enum::Division, 'organization_id' => [1, 2], 'group2' => Group2Enum::OnlineCategory, 'online_category_id' => [1, 2]],
                [
                    '2020-02-03' => [1 => [1 => 3600, 2 => 3600], 2 => [1 => 3600, 2 => 3600]],
                    '2020-02-09' => [1 => [1 => 3600], 2 => [2 => 3600]],
                    '2020-02-17' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '発送日 x 日次 x ストラブランド x オンライン分類' => [
                ['by' => ByEnum::Delivered, 'unit' => UnitEnum::Daily, 'group1' => Group1Enum::MainStoreBrand, 'organization_id' => [1, 2], 'group2' => Group2Enum::OnlineCategory, 'online_category_id' => [1, 2]],
                [
                    '2020-02-03' => [1 => [1 => 3600, 2 => 3600], 2 => [1 => 3600, 2 => 3600]],
                    '2020-02-09' => [1 => [1 => 3600], 2 => [2 => 3600]],
                    '2020-02-17' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '発送日 x 週次 x 組織 x オンライン分類' => [
                ['by' => ByEnum::Delivered, 'unit' => UnitEnum::Weekly, 'group1' => Group1Enum::Organization, 'organization_id' => [1, 2], 'group2' => Group2Enum::OnlineCategory, 'online_category_id' => [1, 2]],
                [
                    '2020-02-03' => [1 => [1 => 7200, 2 => 3600], 2 => [1 => 3600, 2 => 7200]],
                    '2020-02-17' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '発送日 x 週次 x 事業部 x オンライン分類' => [
                ['by' => ByEnum::Delivered, 'unit' => UnitEnum::Weekly, 'group1' => Group1Enum::Division, 'organization_id' => [1, 2], 'group2' => Group2Enum::OnlineCategory, 'online_category_id' => [1, 2]],
                [
                    '2020-02-03' => [1 => [1 => 7200, 2 => 3600], 2 => [1 => 3600, 2 => 7200]],
                    '2020-02-17' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],
            '発送日 x 週次 x ストラブランド x オンライン分類' => [
                ['by' => ByEnum::Delivered, 'unit' => UnitEnum::Weekly, 'group1' => Group1Enum::MainStoreBrand, 'organization_id' => [1, 2], 'group2' => Group2Enum::OnlineCategory, 'online_category_id' => [1, 2]],
                [
                    '2020-02-03' => [1 => [1 => 7200, 2 => 3600], 2 => [1 => 3600, 2 => 7200]],
                    '2020-02-17' => [1 => [1 => 3600], 2 => [2 => 3600]],
                ],
            ],

            '発送日 x 月次 x 組織 x オンライン分類' => [
                ['by' => ByEnum::Delivered, 'unit' => UnitEnum::Monthly, 'group1' => Group1Enum::Organization, 'organization_id' => [1, 2], 'group2' => Group2Enum::OnlineCategory, 'online_category_id' => [1, 2]],
                ['2020-02-01' => [1 => [1 => 10800, 2 => 3600], 2 => [1 => 3600, 2 => 10800]]],
            ],
            '発送日 x 月次 x 事業部 x オンライン分類' => [
                ['by' => ByEnum::Delivered, 'unit' => UnitEnum::Monthly, 'group1' => Group1Enum::Division, 'organization_id' => [1, 2], 'group2' => Group2Enum::OnlineCategory, 'online_category_id' => [1, 2]],
                ['2020-02-01' => [1 => [1 => 10800, 2 => 3600], 2 => [1 => 3600, 2 => 10800]]],
            ],
            '発送日 x 月次 x ストラブランド x オンライン分類' => [
                ['by' => ByEnum::Delivered, 'unit' => UnitEnum::Monthly, 'group1' => Group1Enum::MainStoreBrand, 'organization_id' => [1, 2], 'group2' => Group2Enum::OnlineCategory, 'online_category_id' => [1, 2]],
                ['2020-02-01' => [1 => [1 => 10800, 2 => 3600], 2 => [1 => 3600, 2 => 10800]]],
            ],
        ];

        foreach ($cases as $i => $param) {
            $cases[$i][0] = array_merge($baseParams, $param[0]);
        }

        return $cases;
    }

    /**
     * @return void
     *
     * @dataProvider aggregateWithCategoryTestDataProvider
     */
    public function testAggregateWithCategory($params, $expected)
    {
        $repository = app()->make(ItemRepositoryEloquent::class);
        $itemGroups = $repository->getItemCategoryGroups($params['online_category_id']);
        $params['item_category_group'] = $itemGroups;

        $repository = app()->make(OrderRepositoryEloquent::class);
        $repository->pushCriteria(new AdminOrderCriteria($params));
        $results = $repository->aggregateWithCategory($params, 50);

        $this->assertGreaterThan(1, $results->count(), '1件以上取得できる。');

        $actual = [];

        foreach ($results as $row) {
            if (!isset($actual[$row->date])) {
                $actual[$row->date] = [];
            }

            if (!isset($actual[$row->date][$row->group1])) {
                $actual[$row->date][$row->group1] = [];
            }

            if (!isset($actual[$row->date][$row->group1][$row->group2])) {
                $actual[$row->date][$row->group1][$row->group2] = [];
            }

            if (!isset($actual[$row->date][$row->group1][$row->group2][$row->sale_type])) {
                $actual[$row->date][$row->group1][$row->group2][$row->sale_type] = 0;
            }

            $actual[$row->date][$row->group1][$row->group2][$row->sale_type] += $row->total_price;
        }

        foreach ($actual as $date => $group1) {
            foreach ($group1 as $group1Id => $group2) {
                foreach ($group2 as $group2Id => $saleTypes) {
                    foreach ($saleTypes as $price) {
                        if (!isset($expected[$date])) {
                            var_dump('$expected', $expected);
                            var_dump('$actual', $actual);

                            return $this->fail("date {$date}がありませんでした。");
                        }

                        if (!isset($expected[$date][$group1Id])) {
                            var_dump('$expected', $expected);
                            var_dump('$actual', $actual);

                            return $this->fail("date {$date} group1Id {$group1Id}がありませんでした。");
                        }

                        if (!isset($expected[$date][$group1Id][$group2Id])) {
                            var_dump('$expected', $expected);
                            var_dump('$actual', $actual);

                            return $this->fail("date {$date} group1Id {$group1Id} group2Id {$group2Id}がありませんでした。");
                        }

                        $this->assertEquals($expected[$date][$group1Id][$group2Id], $price);
                    }
                }
            }
        }
    }
}
