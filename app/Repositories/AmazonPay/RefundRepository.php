<?php

namespace App\Repositories\AmazonPay;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface RefundRepository.
 *
 * @package namespace App\Repositories\AmazonPay;
 */
interface RefundRepository extends RepositoryInterface
{
    /**
     * ReferenceIDを生成する
     *
     * @return string
     */
    public static function generateReferenceId();
}
