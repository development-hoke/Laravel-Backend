<?php

namespace App\Repositories\AmazonPay;

use App\Models\AmazonPayAuthorization;
use App\Repositories\AmazonPay\Traits\HasReferenceId;

/**
 * Class AmazonPayAuthorizationRepositoryEloquent.
 *
 * @package namespace App\Repositories\AmazonPay;
 */
class AuthorizationRepositoryEloquent extends BaseRepositoryEloquent implements AuthorizationRepository
{
    use HasReferenceId;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AmazonPayAuthorization::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
