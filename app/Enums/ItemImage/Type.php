<?php

namespace App\Enums\ItemImage;

use App\Enums\BaseEnum;

/**
 * @method static static Normal()
 * @method static static DeploymentPhotoFront()
 * @method static static DeploymentPhotoBack()
 */
final class Type extends BaseEnum
{
    const Normal = 1;
    const DeploymentPhotoFront = 2;
    const DeploymentPhotoBack = 3;
}
