<?php

namespace App\HttpCommunication\Response\Concrete;

use App\HttpCommunication\Response\ResponseInterface;

class StaffStartResponse implements ResponseInterface
{
    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var string[][]
     */
    protected $headers;

    /**
     * @var array
     */
    protected $body;

    /**
     * @param string $rawResponse
     * @param int $statusCode
     */
    public function __construct(string $rawResponse, int $statusCode)
    {
        $response = \App\Utils\Http::parseResponse($rawResponse);

        $this->body = $response['body'];
        $this->headers = $response['headers'];
        $this->statusCode = $statusCode;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string[][]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $headers = [];

        foreach ($this->headers as $key => $values) {
            foreach ($values as $value) {
                $headers[] = $key . ': ' . $value;
            }
        }

        return implode("\r\n", $headers) . "\r\n\r\n" . json_encode($this->body);
    }
}
