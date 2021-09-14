<?php

namespace App\Models\Contracts;

interface Timestampable
{
    const TIMESTAMPING_TYPE_NOT_NULL = 'if_not_null';
    const TIMESTAMPING_TYPE_DIRTY = 'if_dirty';
    const TIMESTAMPING_TYPE_TRUE = 'if_true';

    /**
     * @param string|null $type
     *
     * @return \App\Models\Model
     */
    public function setOptionalTimestamps(string $type = null);
}
