<?php

use App\Exceptions\ErrorUtil;

if (!function_exists('error_format')) {
    /**
     * エラーメッセージを形成する
     *
     * @param string $message
     * @param array|string $params
     * @param array $replace
     *
     * @return string
     */
    function error_format(string $message, $params = [], array $replace = []): string
    {
        return ErrorUtil::formatMessage($message, $params, $replace);
    }
}

if (!function_exists('ms2us')) {
    /**
     * ミリ秒からマイクロ秒に変換する
     *
     * @param int $usec
     *
     * @return int
     */
    function ms2us(int $msec): int
    {
        return $msec * 1000;
    }
}

if (!function_exists('form_response_array')) {
    /**
     * Undocumented function
     *
     * @param array $data
     *
     * @return array
     */
    function form_response_array(array $data)
    {
        if (!isset($data['data'])) {
            $data = ['data' => $data];
        }

        return $data;
    }
}

if (!function_exists('replace_hyphen')) {
    function replace_hyphen(string $value)
    {
        $string = mb_convert_kana($value, 'kvrna', 'UTF-8');
        $result = preg_replace('/[\x{30FC}\x{2010}-\x{2015}\x{2212}\x{FF70}-]/u', '-', $string);
        if (is_null($result)) {
            return $string;
        }

        return $result;
    }
}

if (!function_exists('translate')) {
    function translate(array $data, array $translator = [])
    {
        $translated = [];

        foreach ($data as $key => $value) {
            if (isset($translator[$key])) {
                $translated[$translator[$key]] = $value;
            } else {
                $translated[$key] = $value;
            }
        }

        return $translated;
    }
}
