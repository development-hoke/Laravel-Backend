<?php

namespace App\Utils;

class Color
{
    // https://www.w3.org/TR/AERT/#color-contrast
    const BRIGHTNESS_COEFFICIENT_R = 0.299;
    const BRIGHTNESS_COEFFICIENT_G = 0.587;
    const BRIGHTNESS_COEFFICIENT_B = 0.114;

    /**
     * 16進数をRGBの部分に分割して配列で返す
     *
     * @param string $hexNumber
     * @param bool $toDec (Default: true)
     *
     * @return array
     */
    public static function splitHex(string $hexNumber, bool $toDec = true)
    {
        $hex = ltrim($hexNumber, '#');
        $isAbbreviation = strlen($hex) === 3;
        $partLength = $isAbbreviation ? 1 : 2;

        $rgb = [];

        foreach (range(0, 3) as $i) {
            $part = substr($hex, $i * $partLength, $partLength);

            if ($isAbbreviation) {
                $part .= $part;
            }

            $rgb[] = $toDec ? hexdec($part) : $part;
        }

        return $rgb;
    }

    /**
     * HEXから輝度に変換する
     *
     * @param string $hexNumber
     *
     * @return float
     */
    public static function hex2brightness(string $hexNumber)
    {
        [$r, $g, $b] = static::splitHex($hexNumber);

        $brightness = array_sum([
            $r * static::BRIGHTNESS_COEFFICIENT_R,
            $g * static::BRIGHTNESS_COEFFICIENT_G,
            $b * static::BRIGHTNESS_COEFFICIENT_B,
        ]);

        return $brightness / 255;
    }
}
