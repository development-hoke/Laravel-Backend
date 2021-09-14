<?php

namespace App\Listeners\Model;

use App\Events\Contracts\ModelEvent;

class ShohinEcUpdate
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

        \App\Jobs\NotifyShohinEcUpdate::dispatch($model->id);
    }
}
