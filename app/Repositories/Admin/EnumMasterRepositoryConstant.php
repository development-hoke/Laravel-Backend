<?php

namespace App\Repositories\Admin;

use App\Repositories\EnumMasterRepositoryConstant as BaseEnumMasterRepositoryConstant;

class EnumMasterRepositoryConstant extends BaseEnumMasterRepositoryConstant implements EnumMasterRepositoryConstantInterface
{
    public function __construct()
    {
        $this->buildEnumMaster();
    }
}
