<?php

namespace App\Enums\Np\ErrorCode;

use App\Enums\BaseEnum;

/**
 * Class Gender
 *
 * @see https://manual-update.np-payment-gateway.com/reference_register
 */
final class Transaction extends BaseEnum
{
    // 購入者
    const InvalidCharacterInCostomerName = 'E0100049';
    const TooManyCharactersOfCostomerName = 'E0100050';
    const TooManyCharactersOfCostomerKana = 'E0100051';
    const InvalidCharacterInCostomerKana = 'E0100052';

    const InvalidCharacterInZip = 'E0100058';
    const IrrelevantZipAndAddress = 'E0100059';
    const InvalidCharacterInAddress = 'E0100061';
    const TooManyCharactersOfAddress = 'E0100062';

    const InvalidFormatTelephoneNumber1 = 'E0100065';
    const InvalidFormatTelephoneNumber2 = 'E0100066';
    const InvalidFormatTelephoneNumber3 = 'E0100067';

    const InvalidCharacterInEmail = 'E0100069';
    const TooManyCharactersOfEmail = 'E0100070';
    const InvalidFormatEmail = 'E0100071';

    // 配送先
    const InvalidCharacterInDestCostomerName = 'E0100073';
    const TooManyCharactersOfDestCostomerName = 'E0100074';
    const TooManyCharactersOfDestCostomerKana = 'E0100075';
    const InvalidCharacterInDestCostomerKana = 'E0100076';

    const InvalidCharacterInDestZip = 'E0100082';
    const IrrelevantZipAndDestAddress = 'E0100083';
    const InvalidCharacterInDestAddress = 'E0100085';
    const TooManyCharactersOfDestAddress = 'E0100086';

    const InvalidFormatDestTelephoneNumber1 = 'E0100089';
    const InvalidFormatDestTelephoneNumber2 = 'E0100090';
    const InvalidFormatDestTelephoneNumber3 = 'E0100091';

    // その他
    const MismatchBillingAmontAndItemPrice = 'E0102004'; // '商品合計金額と顧客請求金額の差額が3000円以内であること。'

    // 更新時のみ
    const AlreadyShipped = 'E0100115';
}
