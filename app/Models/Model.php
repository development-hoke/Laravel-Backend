<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel
{
    public function replicateWithKey(?array $except = null)
    {
        $key = $this->getKeyName();
        $replica = $this->replicate($except);
        $replica->{$key} = $this->{$key};

        return $replica;
    }
}
