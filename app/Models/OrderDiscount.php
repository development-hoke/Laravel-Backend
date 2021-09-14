<?php

namespace App\Models;

use App\Models\Contracts\Loggable;
use App\Models\Traits\Logging;
use App\Models\Traits\OrderSoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDiscount extends Model implements Loggable
{
    use OrderSoftDeletes;
    use SoftDeletes;
    use Logging;

    protected $fillable = [
        'orderable_type',
        'orderable_id',
        'applied_price',
        'unit_applied_price',
        'type',
        'method',
        'priority',
        'discount_price',
        'discount_rate',
        'discountable_type',
        'discountable_id',
        'update_staff_id',
    ];

    protected $dispatchesEvents = [
        'saving' => \App\Events\Model\SavingOrderDiscount::class,
        'saved' => \App\Events\Model\SavedOrderDiscount::class,
    ];

    /**
     * savingイベント発火時に実行される
     *
     * @return static
     */
    public function assignAttachedAttributes()
    {
        $this->priority = $this->getPriority();

        return $this;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return \App\Domain\Utils\OrderDiscount::getPriorityByType($this->type);
    }

    /**
     * @return MorphTo
     */
    public function discountable(): MorphTo
    {
        return $this->morphTo();
    }
}
