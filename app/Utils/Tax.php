<?php

namespace App\Utils;

use Carbon\Carbon;

class Tax
{
    /**
     * 元金の割合
     */
    private const ORIGIN = 1;

    /**
     * @var int
     */
    private static $defaultTaxRateId;

    /**
     * 元金の割合を含めた税率 (2020年現在 1.1)
     *
     * @param int|null $taxRateId
     *
     * @return float
     */
    public static function getRateWithOrigin(?int $taxRateId = null)
    {
        return self::ORIGIN + self::getRate($taxRateId);
    }

    /**
     * @return int
     */
    public static function getDefaultTaxId()
    {
        if (isset(static::$defaultTaxRateId)) {
            return static::$defaultTaxRateId;
        }

        $defaultTaxRateIds = config('constants.default_tax_rate_id');
        $today = Carbon::today();

        $key = collect($defaultTaxRateIds)->keys()->filter(function ($key) use ($today) {
            return Carbon::parse($key)->lte($today);
        })->sortKeysDesc()->first();

        static::$defaultTaxRateId = $defaultTaxRateIds[$key];

        return static::$defaultTaxRateId;
    }

    /**
     * 消費税率取得
     *
     * @param int|null $taxRateId
     *
     * @return float
     */
    public static function getRate(?int $taxRateId = null)
    {
        $taxRateId = $taxRateId ?? static::getDefaultTaxId();

        $taxRates = config('constants.tax_rates');

        return $taxRates[$taxRateId];
    }

    /**
     * 消費税 = 商品金額(税込み) - ceil(商品金額(税込み) / 1.1)
     *
     * @param int $price
     * @param int|null $taxRateId
     *
     * @return int
     */
    public static function calcTax(int $price, ?int $taxRateId = null)
    {
        return $price - static::calcPriceExcludeTax($price, $taxRateId);
    }

    /**
     * 税抜き価格
     *
     * @param int $price
     * @param int|null $taxRateId
     *
     * @return int
     */
    public static function calcPriceExcludeTax(int $price, ?int $taxRateId = null)
    {
        $rate = static::getRateWithOrigin($taxRateId);

        return (int) ceil($price / $rate);
    }
}
