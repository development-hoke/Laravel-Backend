<?php

namespace App\Models\Collections;

use Illuminate\Database\Eloquent\Collection as BaseCollection;

class Collection extends BaseCollection
{
    /**
     * Create a new model instance for a related model.
     *
     * @param string $class
     *
     * @return mixed
     */
    protected function newRelatedInstance($class)
    {
        $item = $this->first();

        if (empty($item)) {
            return new $class();
        }

        return tap(new $class(), function ($instance) use ($item) {
            if (!$instance->getConnectionName()) {
                $instance->setConnection($item->getConnectionName());
            }
        });
    }
}
