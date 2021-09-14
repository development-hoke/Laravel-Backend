<?php

namespace App\Repositories\AmazonPay\Traits;

use Webpatser\Uuid\Uuid;

trait HasReferenceId
{
    /**
     * ReferenceIDを生成する
     *
     * @return string
     */
    public static function generateReferenceId()
    {
        $uuid = Uuid::generate(4);

        return str_replace('-', '', $uuid);
    }
}
