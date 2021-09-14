<?php

namespace App\HttpCommunication\AmazonPay;

class GetUserInfoResponse extends Response
{
    /**
     * @var array
     */
    private $body;

    /**
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->body = $response;
    }

    /**
     * @return null
     */
    public function getStatusCode()
    {
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
