<?php

namespace App\HttpCommunication\Exceptions;

class UnprocessableEntityHttpException extends HttpException
{
    /**
     * @return array
     */
    public function parseValidationError()
    {
        $body = $this->getResponseBody();

        if (isset($body['error'])) {
            $body = $body['error'];
        }

        return array_merge(
            ['global' => $body['message']],
            $body['fields']
        );
    }
}
