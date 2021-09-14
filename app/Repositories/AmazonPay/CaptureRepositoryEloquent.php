<?php

namespace App\Repositories\AmazonPay;

use App\Models\AmazonPayCapture;
use App\Repositories\AmazonPay\Traits\HasReferenceId;

/**
 * Class AmazonPayCaptureRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CaptureRepositoryEloquent extends BaseRepositoryEloquent implements CaptureRepository
{
    use HasReferenceId;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AmazonPayCapture::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
