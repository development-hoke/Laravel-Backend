<?php

namespace App\HttpCommunication\FRegi;

use App\HttpCommunication\Exceptions\FRegiResponseException;
use App\HttpCommunication\Exceptions\HttpException;
use App\HttpCommunication\HttpCommunicationService as BaseHttpCommunicationService;
use App\HttpCommunication\Response\Concrete\FRegiResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class HttpCommunicationService extends BaseHttpCommunicationService
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $originalResponse
     *
     * @return void
     */
    protected function validateResponse(RequestInterface $request, ResponseInterface $originalResponse)
    {
        $response = $this->newResponse($originalResponse);

        if ($response->hasErrorCode()) {
            throw new FRegiResponseException($response);
        }
    }

    /**
     * @param ResponseInterface $originalResponse
     *
     * @return FRegiResponse
     */
    protected function newResponse(ResponseInterface $originalResponse)
    {
        return new FRegiResponse($originalResponse);
    }

    /**
     * @param \GuzzleHttp\Exception\RequestException $exception
     *
     * @return void
     */
    protected function handleRequestError(\GuzzleHttp\Exception\RequestException $exception)
    {
        throw new HttpException($exception);
    }
}
