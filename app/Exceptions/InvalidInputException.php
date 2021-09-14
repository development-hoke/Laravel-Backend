<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class InvalidInputException extends BaseException
{
    /**
     * @var string
     */
    protected $message;

    /**
     * @var int
     */
    protected $code;

    /**
     * @var \Illuminate\Http\Response
     */
    protected $response;

    /**
     * @param string|string[] $messages
     * @param string $errorCode \App\Enums\Common\ErrorCode
     * @param int $statusCode
     * @param \Throwable $previous
     */
    public function __construct($messages, $errorCode = null, $previous = null)
    {
        $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

        if (!is_array($messages)) {
            $messages = ['global' => $messages];
        }

        $globalMessage = $messages['global'] ?? current($messages);

        if (is_array($globalMessage)) {
            $globalMessage = current($globalMessage);
        }

        parent::__construct(
            $globalMessage,
            isset($previous) ? $previous->getCode() : $statusCode,
            $previous
        );

        $this->message = $globalMessage;

        $this->response = response([
            'errors' => $messages,
            'code' => $errorCode,
        ], $statusCode);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * @param int $code
     *
     * @return static
     */
    public function setStatusCode(int $code)
    {
        $this->response->setStatusCode($code);

        return $this;
    }

    /**
     * Get the underlying response instance.
     *
     * @return \Illuminate\Http\Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}
