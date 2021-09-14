<?php

namespace App\Domain\Exceptions;

use App\HttpCommunication\Exceptions\HttpException;

class NpPaymentResponseException extends NpPaymentException
{
    /**
     * @var array
     */
    public $errors;

    /**
     * @var array
     */
    public $requestParams;

    /**
     * @param array $errors
     * @param array $requestParams
     * @param string|null $message
     * @param int|null $code
     * @param \Exception|null $previous
     */
    public function __construct(array $errors = [], array $requestParams = [], ?string $message = null, ?int $code = 0, ?\Exception $previous = null)
    {
        $message = $message ?? error_format('[NP後払い]リクエストに失敗しました。', ['errors' => $errors]);

        parent::__construct($message, $code, $previous);

        $this->errors = $errors;

        $this->requestParams = $requestParams;
    }

    /**
     * @return array
     */
    public function getRequestParams()
    {
        return $this->requestParams;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     *
     * @return static
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * @return bool
     */
    public function isHttpError()
    {
        return $this->getPrevious() instanceof HttpException;
    }
}
