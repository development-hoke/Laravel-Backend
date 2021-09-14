<?php

namespace App\Utils;

class Http
{
    const CONTENT_TYPE = 'json';

    /**
     * @param string $rawResponse
     * @param string|null $contentType
     *
     * @return void
     */
    public static function parseResponse(string $rawResponse, ?string $contentType = null)
    {
        [$headers, $body] = explode("\r\n\r\n", $rawResponse);

        $headers = static::parseHeaders($headers);

        $body = static::parseBody($body, $contentType);

        return ['headers' => $headers, 'body' => $body];
    }

    /**
     * @param string $rawHeaders
     *
     * @return string[][]
     */
    public static function parseHeaders(string $rawHeaders)
    {
        $rawHeaders = explode("\r\n", $rawHeaders);

        $headers = [];

        foreach ($rawHeaders as $i => $header) {
            $header = array_map('trim', explode(':', $header));

            if (count($header) > 1) {
                [$key, $value] = $header;
            } else {
                $key = $i;
                $value = $header[0];
            }

            if (!isset($headers[$key])) {
                $headers[$key] = [];
            }

            $headers[$key][] = $value;
        }

        return $headers;
    }

    /**
     * @param string $rawBody
     * @param string|null $contentType
     *
     * @return mixed
     */
    public static function parseBody(string $rawBody, ?string $contentType = null)
    {
        $contentType = $contentType ?? static::CONTENT_TYPE;

        switch ($contentType) {
            case static::CONTENT_TYPE:
            default:
                return json_decode($rawBody, true);
        }
    }
}
