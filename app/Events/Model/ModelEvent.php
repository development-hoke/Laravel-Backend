<?php

namespace App\Events\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class ModelEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var \App\Models\Model
     */
    public $model;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Model $model
     *
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return \App\Models\Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }
}
