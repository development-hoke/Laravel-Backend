<?php

namespace App\HttpCommunication\Response\Concrete;

use App\HttpCommunication\Response\ResponseInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
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
     * @param PsrResponseInterface $response
     */
    public function __construct(PsrResponseInterface $response)
    {
        $this->body = $this->parseBody($response->getBody());

        $this->headers = $response->getHeaders();

        $this->statusCode = $response->getStatusCode();
    }

    /**
     * @param StreamInterface $body
     *
     * @return array
     */
    public function parseBody(StreamInterface $body)
    {
        return json_decode((string) $body, true);
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
