<?php

namespace App\Enums\Np\ErrorCode;

use App\Enums\BaseEnum;

final class Reregister extends BaseEnum
{
    const MismatchBillingAmontAndItemPrice = 'E0102004'; // '商品合計金額と顧客請求金額の差額が3000円以内であること。'
    const ExceededBaseTransactionBillingAmount = 'E0131010'; // '基取引の顧客請求金額以下であること。 '
}
