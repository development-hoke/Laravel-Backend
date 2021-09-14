<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // Saving model
        \App\Events\Model\SavingOrderDiscount::class => [
            \App\Listeners\Model\AssignAttachedAttributes::class,
        ],

        // Saved model
        \App\Events\Model\SavedOrder::class => [
            \App\Listeners\Model\CreateLog::class,
            \App\Listeners\Model\SetOptionalTimestamps::class,
        ],
        \App\Events\Model\SavedOrderDetail::class => [
            \App\Listeners\Model\CreateLog::class,
        ],
        \App\Events\Model\SavedOrderDetailUnit::class => [
            \App\Listeners\Model\CreateLog::class,
        ],
        \App\Events\Model\SavedOrderAddress::class => [
            \App\Listeners\Model\CreateLog::class,
        ],
        \App\Events\Model\SavedOrderUsedCoupon::class => [
            \App\Listeners\Model\CreateLog::class,
        ],
        \App\Events\Model\SavedOrderDiscount::class => [
            \App\Listeners\Model\CreateLog::class,
        ],
        \App\Events\Model\CreatingStaff::class => [
            \App\Listeners\Model\SetOptionalTimestampsIfNotNull::class,
        ],

        // Updating model
        \App\Events\Model\UpdatingItem::class => [
            \App\Listeners\Model\SetOptionalTimestampsIfDirty::class,
            \App\Listeners\Model\ShohinEcUpdate::class,
        ],
        \App\Events\Model\UpdatingOrder::class => [
            \App\Listeners\Model\SetOptionalTimestamps::class,
        ],
        \App\Events\Model\UpdatingStaff::class => [
            \App\Listeners\Model\SetOptionalTimestampsIfDirty::class,
        ],
        \App\Events\Model\UpdatingCart::class => [
            \App\Listeners\Model\SetOptionalTimestamps::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
