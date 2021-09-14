<?php

namespace App\HttpCommunication\Response\Mock;

use App\HttpCommunication\Response\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response implements ResponseInterface
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
     * @param array $body
     * @param array $headers
     * @param int $statusCode
     */
    public function __construct(array $body = [], array $headers = [], int $statusCode = 200)
    {
        $this->body = $body;
        $this->headers = $headers;
        $this->statusCode = $statusCode;
    }

    /**
     * @param StreamInterface $body
     *
     * @return array
     */
    public function parseBody(StreamInterface $body)
    {
        return json_decode((string) $body);
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
}
