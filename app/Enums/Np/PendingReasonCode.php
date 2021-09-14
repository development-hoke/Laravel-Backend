<?php

namespace App\Enums\Np;

use App\Enums\BaseEnum;

/**
 * Class PendingReasonCode
 */
final class PendingReasonCode extends BaseEnum
{
    const InsufficientAddressInformation = 'RE009';
    const ConfirmationAddress = 'RE014';
    const InsufficientDeliveryAddressInformation = 'RE015';
    const ConfirmationDeliveryAddress = 'RE020';
    const InvalidPhoneNumber = 'RE021';
    const InvalidDeliveryAddressPhoneNumber = 'RE023';
    const Other = 'RE026';
}
