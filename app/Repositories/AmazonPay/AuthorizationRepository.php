<?php

namespace App\Repositories\AmazonPay;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface AmazonPayAuthorizationRepository.
 *
 * @package namespace App\Repositories;
 */
interface AuthorizationRepository extends RepositoryInterface
{
    /**
     * ReferenceIDを生成する
     *
     * @return string
     */
    public static function generateReferenceId();
}
