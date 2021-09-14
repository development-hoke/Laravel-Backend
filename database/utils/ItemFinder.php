<?php

namespace Database\Utils;

class ItemFinder
{
    private static $cache = [];

    public static function findProductNumber($faker)
    {
        // echo 'Finding items.product_number ';

        while (true) {
            // echo '.';

            $divisionId = $faker->randomElement(\App\Models\Division::all()->pluck('id'));
            $departmentId = $faker->randomElement(\App\Models\Department::all()->pluck('id'));
            $shortProductNumber = str_pad($faker->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);
            $productNumber = str_pad($divisionId, 2, '0', STR_PAD_LEFT) . str_pad($departmentId, 2, '0', STR_PAD_LEFT) . $shortProductNumber;

            if (empty(static::$cache[$productNumber])) {
                // echo 'Found! -> ' . $productNumber . PHP_EOL;

                return [
                    $divisionId,
                    $departmentId,
                    $shortProductNumber,
                    $productNumber,
                ];
            }
        }
    }
}
