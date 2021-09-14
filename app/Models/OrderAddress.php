<?php

namespace App\Models;

use App\Models\Contracts\Loggable;
use App\Models\Traits\Logging;
use App\Models\Traits\OrderSoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderAddress extends Model implements Loggable
{
    use OrderSoftDeletes;
    use SoftDeletes;
    use Logging;

    protected $dispatchesEvents = [
        'saved' => \App\Events\Model\SavedOrderAddress::class,
    ];

    protected $fillable = [
        'order_id',
        'type',
        'fname',
        'lname',
        'fkana',
        'lkana',
        'tel',
        'pref_id',
        'zip',
        'city',
        'town',
        'address',
        'building',
        'email',
        'update_staff_id',
    ];

    /**
     * @return BelongsTo
     */
    public function pref(): BelongsTo
    {
        return $this->belongsTo(Pref::class);
    }
}
