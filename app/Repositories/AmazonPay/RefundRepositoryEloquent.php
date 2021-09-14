<?php

namespace App\Repositories\AmazonPay;

use App\Models\AmazonPayRefund;
use App\Repositories\AmazonPay\Traits\HasReferenceId;

/**
 * Class RefundRepositoryEloquent.
 *
 * @package namespace App\Repositories\AmazonPay;
 */
class RefundRepositoryEloquent extends BaseRepositoryEloquent implements RefundRepository
{
    use HasReferenceId;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AmazonPayRefund::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
