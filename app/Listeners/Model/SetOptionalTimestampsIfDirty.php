<?php

namespace App\Listeners\Model;

use App\Events\Contracts\ModelEvent;
use App\Exceptions\FatalException;

/**
 * TODO: src/server/app/Listeners/Model/SetOptionalTimestamps.phpの使用に切り替えていく
 */
class SetOptionalTimestampsIfDirty
{
    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle(ModelEvent $event)
    {
        $model = $event->getModel();

        if (!method_exists($model, 'setOptionalTimestampsIfDirty')) {
            throw new FatalException(__('error.class_method_not_defined', ['class' => get_class($model), 'method' => 'setOptionalTimestampsIfDirty']));
        }

        $model->setOptionalTimestampsIfDirty();
    }
}
