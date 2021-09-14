<?php

namespace App\Utils;

use Carbon\Carbon;

class Cast
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public static function booleanLike($value)
    {
        $value = strtolower((string) $value);

        if ($value === 'true' || $value === 'false') {
            return (bool) ($value === 'true');
        }

        return (bool) ((int) $value);
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public static function string($value)
    {
        return (string) $value;
    }

    /**
     * @param mixed $value
     *
     * @return int
     */
    public static function int($value)
    {
        return (int) $value;
    }

    /**
     * @param mixed $value
     *
     * @return float
     */
    public static function float($value)
    {
        return (float) $value;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public static function bool($value)
    {
        return static::booleanLike($value);
    }

    /**
     * @param mixed $value
     *
     * @return \Carbon\Carbon
     */
    public static function date($value)
    {
        if (is_numeric($value)) {
            return Carbon::createFromTimestamp($value);
        }

        return Carbon::parse($value);
    }
}
