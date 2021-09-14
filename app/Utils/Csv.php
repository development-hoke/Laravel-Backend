<?php

namespace App\Utils;

class Csv
{
    const EXPRESSION_TRUE = 'TRUE';
    const EXPRESSION_FALSE = 'FALSE';

    /**
     * @return array
     */
    public static function getBooleanExpressions()
    {
        return [static::EXPRESSION_FALSE, static::EXPRESSION_FALSE];
    }

    /**
     * CSVで使用するフォーマットに変換
     *
     * @param string $datetime
     *
     * @return string
     */
    public static function formatDatetime(string $datetime)
    {
        if (($timestamp = strtotime($datetime)) === false) {
            return null;
        }

        return date('Y/m/d H:i', $timestamp);
    }

    /**
     * CSVで使用するフォーマットに変換
     *
     * @param string $datetime
     *
     * @return string
     */
    public static function formatDate(string $datetime)
    {
        if (($timestamp = strtotime($datetime)) === false) {
            return null;
        }

        return date('Y/m/d', $timestamp);
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public static function fomatBoolean($value)
    {
        return (bool) $value ? static::EXPRESSION_TRUE : static::EXPRESSION_FALSE;
    }
}
