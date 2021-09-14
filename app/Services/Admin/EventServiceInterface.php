<?php

namespace App\Services\Admin;

interface EventServiceInterface
{
    /**
     * @param int $eventId
     *
     * @return array
     */
    public function copy(int $eventId);

    /**
     * @param array $params
     *
     * @return \App\Models\Event
     */
    public function create(array $params);

    /**
     * @param array $params
     * @param int $id
     *
     * @return \App\Models\Event
     */
    public function update(array $params, int $id);
}
