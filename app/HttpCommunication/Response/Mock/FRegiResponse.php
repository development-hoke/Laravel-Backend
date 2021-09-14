<?php

namespace App\HttpCommunication\Response\Mock;

use App\HttpCommunication\Response\FRegiResponseInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class FRegiResponse implements FRegiResponseInterface
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
     * @var string
     */
    protected $errorCode;

    /**
     * @param PsrResponseInterface $response
     */
    public function __construct(array $body = [], array $headers = [], int $statusCode = 200)
    {
        $this->body = $body;
        $this->headers = $headers;
        $this->statusCode = $statusCode;
        $this->errorCode = $this->parseErrorCode($this->body);
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
     * @return bool
     */
    public function hasErrorCode()
    {
        return !empty($this->errorCode);
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param array $body
     *
     * @return string|null
     */
    private function parseErrorCode(array $body)
    {
        if ($body[0] === self::RESULT_OK) {
            return null;
        }

        if (!preg_match('/^([A-Z\-0-9]+)\(.*$/', $body[1], $matches)) {
            return \App\Enums\FRegi\ErrorCode::Unrecognized;
        }

        return $matches[1];
    }
}
