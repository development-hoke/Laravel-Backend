<?php

namespace App\Enums\Np;

use App\Enums\BaseEnum;

/**
 * Class Gender
 */
final class AuthoriResult extends BaseEnum
{
    const OK = '00';
    const Pending = '10';
    const NG = '20';
    const Unscreened = '40';
}
