<?php

namespace App\Domain\Exceptions;

class NpPaymentReregisterResponseException extends NpPaymentResponseException
{
    /**
     * @var \App\Models\Order
     */
    private $order;

    /**
     * @param array $order
     * @param array $errors
     * @param array $requestParams
     * @param string|null $message
     * @param int|null $code
     * @param \Exception|null $previous
     */
    public function __construct(\App\Models\Order $order, array $errors = [], array $requestParams = [], ?string $message = null, ?int $code = 0, ?\Exception $previous = null)
    {
        parent::__construct($errors, $requestParams, $message, $code, $previous);

        $this->order = $order;
    }

    /**
     * @return \App\Models\Order
     */
    public function getOrder()
    {
        return $this->order;
    }
}
