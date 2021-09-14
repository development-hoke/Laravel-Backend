<?php

namespace App\Enums\Common;

use App\Enums\BaseEnum;

/**
 * @method static static Medoc()
 * @method static static Lasud()
 * @method static static Vin()
 * @method static static Aga()
 * @method static static Fennel()
 * @method static static Radiate()
 * @method static static Clove()
 */
final class StoreBrand extends BaseEnum
{
    const Medoc = 1;
    const Lasud = 2;
    const Vin = 3;
    const Aga = 4;
    const Fennel = 5;
    const Radiate = 6;
    const Clove = 7;

    public static function lowerKeys()
    {
        return collect(self::getKeys())->map(function ($item, $key) {
            return strtolower($item);
        });
    }
}
