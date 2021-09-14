<?php

namespace App\Repositories\AmazonPay;

use App\Models\AmazonPayNotification;

/**
 * Class NotificationRepositoryEloquent.
 *
 * @package namespace App\Repositories\AmazonPay;
 */
class NotificationRepositoryEloquent extends BaseRepositoryEloquent implements NotificationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AmazonPayNotification::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
