<?php

namespace App\Domain\Exceptions;

class PaymentException extends \App\Exceptions\FatalException
{
    /**
     * @var \App\Models\Order
     */
    private $order;

    public function __construct(\App\Models\Order $order, ?string $message = null, ?int $code = null, ?\Exception $previous = null)
    {
        $this->order = $order;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return \App\Models\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return int
     */
    public function getPaymentType()
    {
        return $this->order->payment_type;
    }
}
