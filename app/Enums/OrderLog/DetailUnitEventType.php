<?php

namespace App\Enums\OrderLog;

use App\Enums\BaseEnum;

/**
 * @method static static Create()
 * @method static static Cancel()
 * @method static static Add()
 */
final class DetailUnitEventType extends BaseEnum
{
    const Create = 1;
    const Cancel = 2;
    const Add = 3;
}
