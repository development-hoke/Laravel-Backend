<?php

namespace App\Domain\Utils;

class ItemSort
{
    const MAX_SETTING_COUNT = 30;

    public static function isAcceptableCount($count)
    {
        return $count <= static::MAX_SETTING_COUNT;
    }
}
