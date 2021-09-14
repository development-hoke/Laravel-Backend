<?php

namespace App\Utils;

class Xml
{
    /**
     * XMLを配列に変換する
     *
     * @param string $source
     *
     * @return array
     */
    public static function toArray(string $source)
    {
        $xml = simplexml_load_string(trim($source));

        $jonn = json_encode($xml);

        return json_decode($jonn, true);
    }
}
