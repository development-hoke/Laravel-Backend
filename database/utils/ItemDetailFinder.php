<?php

namespace Database\Utils;

class ItemDetailFinder
{
    private static $cache = [];

    public static function findSkuNumber($faker)
    {
        echo 'Finding item_details.sku_number ';

        while (true) {
            echo '.';

            $item = $faker->randomElement(\App\Models\Item::all());
            $colorId = $faker->randomElement(\App\Models\Color::all()->pluck('id'));
            $sizeId = $faker->randomElement(\App\Models\Size::all()->pluck('id'));
            $skuNumber = $item->product_number . str_pad($colorId, 2, '0', STR_PAD_LEFT) . str_pad($sizeId, 1, '0', STR_PAD_LEFT);

            if (empty(static::$cache[$skuNumber])) {
                echo 'Found! -> ' . $skuNumber . PHP_EOL;

                static::$cache[$skuNumber] = $skuNumber;

                return [
                    $item,
                    $colorId,
                    $sizeId,
                    $skuNumber,
                ];
            }
        }
    }
}
