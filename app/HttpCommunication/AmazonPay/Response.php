<?php

namespace App\HttpCommunication\AmazonPay;

use AmazonPay\ResponseInterface as AmazonPayResponse;
use App\HttpCommunication\Response\ResponseInterface;

class Response implements ResponseInterface
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var array
     */
    private $body;

    /**
     * @param AmazonPayResponse $response
     */
    public function __construct(AmazonPayResponse $response)
    {
        $responseData = $response->toArray();

        $this->statusCode = (int) $responseData['ResponseStatus'];

        $this->body = $responseData;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return null
     */
    public function getHeaders()
    {
    }

    /**
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }
}
