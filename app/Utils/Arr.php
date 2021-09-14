<?php

namespace App\Utils;

use Illuminate\Support\Arr as BaseArr;

class Arr extends BaseArr
{
    /**
     * indexも引数に渡せるようしたメソッド
     *
     * @param array $array
     * @param \Closure $closure
     *
     * @return array
     */
    public static function map($array, \Closure $closure)
    {
        $mapped = [];

        foreach ($array as $i => $element) {
            $mapped[$i] = $closure($element, $i);
        }

        return $mapped;
    }

    /**
     * @param array $array
     * @param \Closure $closure
     * @param mixed $carry
     *
     * @return array
     */
    public static function reduce($array, \Closure $closure, $carry)
    {
        foreach ($array as $i => $element) {
            $carry = $closure($carry, $element, $i, $array);
        }

        return $carry;
    }

    /**
     * @param array $array
     * @param \Closure|mixed $condition
     * @param bool $strict
     *
     * @return mixed
     */
    public static function findKey($array, $condition, $strict = false)
    {
        if (!is_callable($condition)) {
            $condition = function ($element, $key) use ($condition, $strict) {
                if ($strict) {
                    return $condition === $element;
                }

                return $condition == $element;
            };
        }

        foreach ($array as $key => $element) {
            if ($condition($element, $key)) {
                return $key;
            }
        }

        return false;
    }

    /**
     * @param array $array
     * @param \Closure|mixed $condition
     * @param bool $strict
     *
     * @return mixed
     */
    public static function find($array, $condition, $strict = false)
    {
        $key = static::findKey($array, $condition, $strict);

        return $key === false || !array_key_exists($key, $array)
            ? false
            : $array[$key];
    }

    /**
     * @param array $array
     * @param mixed $key
     *
     * @return array
     */
    public static function dict($array, $key = 'id')
    {
        $dict = [];

        foreach ($array as $element) {
            $value = is_array($element)
                ? $element[$key]
                : (is_object($element) ? $element->{$key} : $element);
            $dict[$value] = $element;
        }

        return $dict;
    }

    /**
     * @param array $array
     * @param mixed $key
     *
     * @return array
     */
    public static function group($array, $key)
    {
        $dict = [];

        foreach ($array as $element) {
            $value = is_array($element) ? $element[$key] : $element->{$key};

            if (!isset($dict[$value])) {
                $dict[$value] = [];
            }

            $dict[$value][] = $element;
        }

        return $dict;
    }

    /**
     * ユニークな配列を返却する。
     * NOTE: 値の判定を連想配列の添字の有無で行うためstrict comparisonsではない
     *
     * @param array $array
     *
     * @return array
     */
    public static function uniq($array)
    {
        return array_values(static::dict($array));
    }

    /**
     * 配列のマージ。数値型のキーもそのまま保持する。
     *
     * @param array ...$arrays
     *
     * @return array
     */
    public static function merge(...$arrays)
    {
        $target = array_shift($arrays);

        foreach ($arrays as $sorce) {
            foreach ($sorce as $key => $value) {
                $target[$key] = $value;
            }
        }

        return $target;
    }
}
