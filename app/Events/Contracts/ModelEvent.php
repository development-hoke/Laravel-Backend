<?php

namespace App\Events\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ModelEvent
{
    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel(): Model;
}
