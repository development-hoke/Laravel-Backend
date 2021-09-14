<?php

// phpcs:ignoreFile

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Item;
use Database\Utils\ItemFinder;
use Faker\Generator as Faker;

$factory->define(Item::class, function (Faker $faker) {
    [
        $divisionId,
        $departmentId,
        $shortProductNumber,
        $productNumber,
    ] = ItemFinder::findProductNumber($faker);

    $mekerProductNumber = $faker->unique()->ean8;
    $mekerProductNumber = substr($mekerProductNumber, 0, 4) . substr($mekerProductNumber, 4, 4);

    $priceChangeRate = $faker->numberBetween(10, 90);
    $discountRate = rand(0, 1) ? $faker->numberBetween(0.0, $priceChangeRate) : 0.0;
    $memberDiscountRate = $faker->numberBetween($discountRate, $priceChangeRate);

    return [
        'term_id' => $faker->randomElement(\App\Models\Term::all()->pluck('id')),
        'season_id' => $faker->numberBetween(1, 4),
        'organization_id' => $faker->randomElement(\App\Models\Organization::all()->pluck('id')),
        'division_id' => $divisionId,
        'department_id' => $departmentId,
        'short_product_number' => $shortProductNumber,
        'product_number' => $productNumber,
        'maker_product_number' => $mekerProductNumber,
        'fashion_speed' => $faker->randomElement(\App\Enums\Item\FashionSpeed::getValues()),
        'name' => $faker->word,
        'retail_price' => $faker->numberBetween(1000, 20000),
        'retail_tax' => $faker->numberBetween(1000, 20000),
        'tax_rate' => 0.1,
        'price_change_period' => $faker->dateTimeBetween('-1 years', '0years')->format('Y-m-d H:i:s'),
        'price_change_rate' => $priceChangeRate / 100,
        'main_store_brand' => $faker->randomElement(\App\Enums\Common\StoreBrand::getValues()),
        'brand_id' => $faker->randomElement(\App\Models\Brand::all()->pluck('id')),
        'display_name' => $faker->word,
        'discount_rate' => $discountRate / 100,
        'discount_rate_updated_at' => $faker->dateTimeBetween('-6 month', '0years')->format('Y-m-d H:i:s'),
        'is_member_discount' => $faker->numberBetween(0, 1),
        'member_discount_rate' => $memberDiscountRate / 100,
        'member_discount_rate_updated_at' => $faker->dateTimeBetween('-6 month', '0years')->format('Y-m-d H:i:s'),
        'point_rate' => $faker->numberBetween(10, 90) / 100,
        'sales_period_from' => $faker->dateTimeBetween('-1 years', '0years')->format('Y-m-d H:i:s'),
        'sales_period_to' => $faker->dateTimeBetween('0years', '+1 years')->format('Y-m-d H:i:s'),
        'description' => $faker->sentence(10),
        'note_staff_ok' => $faker->sentence(10),
        'size_caution' => $faker->sentence(10),
        'material_caution' => $faker->sentence(10),
        'status' => $faker->numberBetween(0, 1),
        'sales_status' => $faker->randomElement(\App\Enums\Item\SalesStatus::getValues()),
        'returnable' => $faker->numberBetween(0, 1),
        'is_manually_setting_recommendation' => $faker->numberBetween(0, 1),
        'back_orderble' => $faker->numberBetween(0, 1),
    ];
});

$factory->state(Item::class, 'published', function (Faker $faker) {
    return [
        'status' => 1,
    ];
});

$factory->state(Item::class, 'unpublished', function (Faker $faker) {
    return [
        'status' => 0,
    ];
});

$factory->afterCreatingState(Item::class, 'EC在庫1つ', function (Item $item, Faker $faker) {
    $itemDetail = factory(\App\Models\ItemDetail::class)->create([
        'item_id' => $item->id,
        'stock' => 1,
        'status' => 1,
    ]);
    $itemDetailIdentification = factory(\App\Models\ItemDetailIdentification::class)->create([
        'item_detail_id' => $itemDetail->id,
        'ec_stock' => 1,
        'reservable_stock' => 0,
    ]);
});

$factory->afterCreatingState(Item::class, '予約在庫1つ', function (Item $item, Faker $faker) {
    $itemDetail = factory(\App\Models\ItemDetail::class)->create([
        'item_id' => $item->id,
        'stock' => 1,
        'status' => 1,
    ]);
    $itemDetailIdentification = factory(\App\Models\ItemDetailIdentification::class)->create([
        'item_detail_id' => $itemDetail->id,
        'ec_stock' => 0,
        'reservable_stock' => 1,
    ]);
    factory(\App\Models\ItemReserve::class)->create([
        'item_id' => $item->id,
        'is_enable' => 1,
    ]);
});
