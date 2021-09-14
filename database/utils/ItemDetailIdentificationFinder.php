<?php

namespace Database\Utils;

class ItemDetailIdentificationFinder
{
    private static $cache = [];

    public static function findSkuNumber($faker, $itemDetail = null)
    {
        echo 'Finding item_detail_identifications.jan_code ';

        while (true) {
            echo '.';

            if (is_null($itemDetail)) {
                $itemDetail = $faker->randomElement(\App\Models\ItemDetail::all());
            }
            $janCode = $itemDetail->sku_number . str_pad($faker->numberBetween(0, 99), 2, '0', STR_PAD_LEFT);

            if (empty(static::$cache[$janCode])) {
                echo 'Found! -> ' . $janCode . PHP_EOL;

                static::$cache[$janCode] = $janCode;

                return [
                    $itemDetail,
                    $janCode,
                ];
            }
        }
    }
}
