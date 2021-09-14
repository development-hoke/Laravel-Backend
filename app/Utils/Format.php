<?php

namespace App\Utils;

class Format
{
    /**
     * %表示から数値に変換
     *
     * @param string $pecent
     *
     * @return float
     */
    public static function percentile2number(string $pecent)
    {
        return ((float) str_replace('%', '', $pecent)) / 100;
    }

    /**
     * @param float|int $value
     *
     * @return string
     */
    public static function percentile($value)
    {
        return round($value * 100, 0) . '%';
    }

    /**
     * @param int $value
     *
     * @return string
     */
    public static function yen($value)
    {
        return number_format($value) . '円';
    }
}
