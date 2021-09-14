<?php

namespace App\Domain\Exceptions;

use App\Entities\Np\Transaction;

/**
 * 取引登録・更新でNGまたは保留となった後の処理で、注文キャンセルに失敗した時に使用する。
 */
class NpPaymentUnsolvedFailedTransactionException extends NpPaymentException
{
    /**
     * @var \App\Models\Order
     */
    private $order;

    /**
     * @var \App\Entities\Np\Transaction
     */
    private $transaction;

    /**
     * @param \App\Models\Order $order
     * @param string $message
     * @param int|null $code
     * @param \Exception|null $previous
     */
    public function __construct(\App\Models\Order $order, Transaction $transaction, string $message, ?int $code = null, ?\Exception $previous = null)
    {
        $this->order = $order;
        $this->transaction = $transaction;
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
     * @return \App\Entities\Np\Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
