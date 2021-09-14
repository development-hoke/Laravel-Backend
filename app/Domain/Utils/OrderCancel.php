<?php

namespace App\Domain\Utils;

use App\Enums\Order\Status;
use Carbon\Carbon;

class OrderCancel
{
    /**
     * キャンセル期限 60分
     */
    private const CANCEL_LIMIT_TIME = 60;

    /**
     * 受注して１時間以内であるか判定
     *
     * @param Carbon $orderDate
     *
     * @return bool
     */
    public static function canCancelDatetime(Carbon $orderDate)
    {
        $cancelLimit = $orderDate->addMinutes(self::CANCEL_LIMIT_TIME);

        return Carbon::now()->lte($cancelLimit);
    }

    /**
     * キャンセル済み以外であるか判定
     *
     * @param int $status
     *
     * @return bool
     */
    public static function canCancelStatus(int $status)
    {
        return $status !== Status::Canceled;
    }
}
