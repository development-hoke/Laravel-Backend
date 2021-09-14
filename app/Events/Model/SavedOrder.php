<?php

namespace App\Events\Model;

use App\Events\Contracts\ModelEvent;
use App\Events\Model\ModelEvent as BaseEvent;

class SavedOrder extends BaseEvent implements ModelEvent
{
}
