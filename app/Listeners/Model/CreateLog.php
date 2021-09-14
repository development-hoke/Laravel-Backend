<?php

namespace App\Listeners\Model;

use App\Events\Contracts\ModelEvent;
use App\Exceptions\FatalException;

class CreateLog
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

        if (!method_exists($model, 'createLog')) {
            throw new FatalException(__('error.class_method_not_defined', ['class' => get_class($model), 'method' => 'createLog']));
        }

        $model->createLog();
    }
}
