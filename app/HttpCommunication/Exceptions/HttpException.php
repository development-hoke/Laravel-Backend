<?php

namespace App\HttpCommunication\Exceptions;

use App\HttpCommunication\Response\Concrete\Response as ConcreteResponse;
use App\HttpCommunication\Response\ResponseInterface as Response;
use GuzzleHttp\Exception\BadResponseException;

class HttpException extends Exception
{
    /**
     * @var BadResponseException
     */
    protected $exception;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param BadResponseException $exception
     * @param Response|null $response
     */
    public function __construct(BadResponseException $exception, ?Response $response = null)
    {
        parent::__construct($exception->getMessage(), $exception->getCode(), $exception);
        $this->response = $response ?? new ConcreteResponse($exception->getResponse());
        $this->exception = $exception;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return int
     */
    public function getResponseStatusCode()
    {
        return $this->getResponse()->getStatusCode();
    }

    /**
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->getResponse()->getHeaders();
    }

    /**
     * @return mixed
     */
    public function getResponseBody()
    {
        return $this->getResponse()->getBody();
    }
}
