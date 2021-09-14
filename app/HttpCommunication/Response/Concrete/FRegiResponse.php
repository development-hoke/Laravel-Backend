<?php

namespace App\HttpCommunication\Response\Concrete;

use App\HttpCommunication\Response\FRegiResponseInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\StreamInterface;

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
    public function __construct(PsrResponseInterface $response)
    {
        $this->body = $this->parseBody($response->getBody());

        $this->headers = $response->getHeaders();

        $this->statusCode = $response->getStatusCode();

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
     * @param $responseBody
     *
     * @return array
     */
    private function parseBody(StreamInterface $body)
    {
        $body = mb_convert_encoding((string) $body, 'UTF-8', 'EUC-JP');

        return array_map('trim', explode("\n", $body));
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
