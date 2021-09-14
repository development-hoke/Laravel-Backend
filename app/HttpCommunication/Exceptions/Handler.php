<?php

namespace App\HttpCommunication\Exceptions;

use App\HttpCommunication\Response\Concrete\Response as HttpCommunicationResponse;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Response;

class Handler
{
    const ERROR_CODE_INVALID_SYSTEM = 'InvalidSystem';

    public static function handle(RequestException $e)
    {
        if (!$e->hasResponse()) {
            throw $e;
        }

        $response = new HttpCommunicationResponse($e->getResponse());

        $statusCode = $response->getStatusCode();

        $body = $response->getBody();

        switch ($body['error']['code'] ?? '') {
            case self::ERROR_CODE_INVALID_SYSTEM:
                throw new InvalidSystemException($e);
            default:
                break;
        }

        switch ($statusCode) {
            case Response::HTTP_UNAUTHORIZED:
            case Response::HTTP_FORBIDDEN:
                throw new AuthHttpException($e);
            case Response::HTTP_UNPROCESSABLE_ENTITY:
                throw new UnprocessableEntityHttpException($e);
            default:
                throw new HttpException($e);
        }
    }
}
