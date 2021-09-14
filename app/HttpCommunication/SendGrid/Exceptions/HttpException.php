<?php

namespace App\HttpCommunication\SendGrid\Exceptions;

class HttpException extends SendGridException
{
    public function __construct($statusCode, $message = '', \Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
    }
}
