<?php

namespace App\Domain\Exceptions;

class NpFailedAuthorizationException extends NpPaymentResponseException
{
    /**
     * @var \App\Entities\Np\Transaction
     */
    public $transaction;

    /**
     * @var \App\Models\Order
     */
    public $order;

    /**
     * @param \App\Entities\Np\Transaction $transaction
     * @param array $errors
     * @param string|null $message
     */
    public function __construct(
        \App\Entities\Np\Transaction $transaction,
        \App\Models\Order $order,
        array $errors = [],
        array $requestParams = [],
        ?string $message = null,
        ?\Exception $previous = null
    ) {
        $this->transaction = $transaction;
        $this->order = $order;

        parent::__construct($errors, $requestParams, $message ?? error_format('error.fail_np_auth'), null, $previous);
    }

    /**
     * @return \App\Entities\Np\Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @return \App\Models\Order
     */
    public function getOrder()
    {
        return $this->order;
    }
}
