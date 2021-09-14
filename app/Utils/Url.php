<?php

namespace App\Utils;

class Url
{
    /**
     * @return string
     */
    public static function getFrontUrl()
    {
        return config('app.front_url');
    }

    /**
     * @param string $path
     * @param array $params
     *
     * @return string
     */
    public static function generatePath(string $path, array $params = [])
    {
        foreach ($params as $from => $to) {
            $path = str_replace(':' . $from, $to, $path);
        }

        $path = '/' . trim($path, '/');

        return $path;
    }

    /**
     * @param string $path
     * @param array $params
     *
     * @return string
     */
    public static function resolveFrontUrl(string $path, array $params = [], array $query = [])
    {
        $url = static::resolveUrl('front', $path, $params);

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    /**
     * @param string $app
     * @param string $path
     * @param array $params
     *
     * @return string
     */
    public static function resolveUrl(string $app, string $path, array $params = [])
    {
        $path = config('url_client.'.$app.'.'.$path, $path);

        $path = static::generatePath($path, $params);

        $host = static::getFrontUrl();

        return $host . $path;
    }

    /**
     * parse_urlのプロキシ
     *
     * @see https://www.php.net/manual/en/function.parse-url.php
     *
     * @param string $url
     *
     * @return array
     */
    public static function parseUrl($url)
    {
        return parse_url($url);
    }

    /**
     * URLからパスを抽出する
     *
     * @param string $url
     *
     * @return string
     */
    public static function extractPath($url)
    {
        $info = static::parseUrl($url);

        return $info['path'] ?? null;
    }
}
