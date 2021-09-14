<?php

namespace App\Enums\Np\ErrorCode;

use App\Enums\BaseEnum;

/**
 * 取引キャンセルエラー
 */
final class Cancel extends BaseEnum
{
    const AlreadyPaid = 'E0100118';
}
