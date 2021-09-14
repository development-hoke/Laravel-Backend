<?php

namespace App\HttpCommunication\Shohin;

use App\HttpCommunication\Exceptions\HttpException;
use App\HttpCommunication\Exceptions\ShohinResponseException;
use App\HttpCommunication\HttpCommunicationService as BaseHttpCommunicationService;
use GuzzleHttp\Exception\BadResponseException;

abstract class HttpCommunicationService extends BaseHttpCommunicationService
{
    const STATUS_CODE_SUCCESS = 1;

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return void
     */
    protected function validateResponse(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response
    ) {
        $body = json_decode((string) $response->getBody(), true);

        if (!isset($body['status']['code'])) {
            throw new ShohinResponseException($response, __('error.failed_to_parse_shohin_response'));
        }

        if ((int) $body['status']['code'] !== self::STATUS_CODE_SUCCESS) {
            if (!isset($body['message'])) {
                throw new ShohinResponseException($response, __('error.failed_to_extract_http_error'));
            }

            $exception = new BadResponseException($body['message'], $request, $response);

            throw new HttpException($exception);
        }
    }
}
