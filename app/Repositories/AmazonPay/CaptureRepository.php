<?php

namespace App\Repositories\AmazonPay;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface AmazonPayCaptureRepository.
 *
 * @package namespace App\Repositories\AmazonPay;
 */
interface CaptureRepository extends RepositoryInterface
{
    /**
     * ReferenceIDを生成する
     *
     * @return string
     */
    public static function generateReferenceId();
}
