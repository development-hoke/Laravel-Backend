<?php

namespace App\HttpCommunication\Exceptions;

use App\HttpCommunication\Response\ResponseInterface as Response;

class StaffStartHttpException extends HttpException
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
        $this->message = __('error.http_client', ['response' => (string) $response]);
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
