<?php

namespace App\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection
{
    public function toVisibleArray()
    {
        return $this->map(function ($value) {
            if (method_exists($value, 'toVisibleArray')) {
                return $value->toVisibleArray();
            } elseif ($value instanceof Arrayable) {
                return $value->toArray();
            } else {
                return $value;
            }
        })->all();
    }
}
