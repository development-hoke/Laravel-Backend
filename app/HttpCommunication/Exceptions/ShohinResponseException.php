<?php

namespace App\HttpCommunication\Exceptions;

use Psr\Http\Message\ResponseInterface as Response;

class ShohinResponseException extends Exception
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @param BadResponseException $exception
     * @param Response|null $response
     */
    public function __construct(Response $response, $message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
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
