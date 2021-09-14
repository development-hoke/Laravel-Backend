<?php

namespace App\Enums\OrderLog;

use App\Enums\BaseEnum;

/**
 * @method static static Create()
 * @method static static Cancel()
 * @method static static Add()
 * @method static static OtherChange()
 */
final class DetailEventType extends BaseEnum
{
    const Create = 1;
    const Cancel = 2;
    const Add = 3;
    const OtherChange = 4;
}
