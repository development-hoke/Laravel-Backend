<?php

namespace App\Utils;

class Geometry
{
    const LONGITUDE_SIZE = 180;
    const LATITUDE_SIZE = 90;

    /**
     * @return array
     */
    public static function getLongitudeRange()
    {
        return [-static::LONGITUDE_SIZE, static::LONGITUDE_SIZE];
    }

    /**
     * @return array
     */
    public static function getLatitudeRange()
    {
        return [-static::LATITUDE_SIZE, static::LATITUDE_SIZE];
    }
}
