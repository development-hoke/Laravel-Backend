<?php

namespace App\Repositories\Front;

use App\Repositories\EnumMasterRepositoryConstant as BaseEnumMasterRepositoryConstant;

class EnumMasterRepositoryConstant extends BaseEnumMasterRepositoryConstant implements EnumMasterRepositoryConstantInterface
{
    /**
     * 除外enums
     */
    protected $exculudes = [
        'App\Enums\AdminLog\Type',
        'App\Enums\Params\Item\Stock',
        'App\Enums\OnlineCategory\Level',
        'App\Enums\ItemDetail\DeadInventoryDayType',
        'App\Enums\ItemDetail\SlowMovingInventoryDayType',
    ];

    public function __construct()
    {
        $this->buildEnumMaster();
    }
}
