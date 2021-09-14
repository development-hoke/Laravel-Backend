<?php

namespace App\Enums\OrderNp;

use App\Enums\BaseEnum;

final class Status extends BaseEnum
{
    const Authorized = 1;
    const Shipped = 2;
    const Canceled = 3;
    const Pending = 4;
    const NG = 5;
    const CanceledButFailedReregister = 6; // キャンセル済み・再登録失敗
    const AuthRejectedCancelFailed = 7; // 取引登録NGまたは保留・キャンセル失敗
}
