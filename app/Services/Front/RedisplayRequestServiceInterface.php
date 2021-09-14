<?php

namespace App\Services\Front;

interface RedisplayRequestServiceInterface
{
    /**
     * @param array $params
     *
     * @return \App\Models\ItemDetailRedisplayRequest
     */
    public function acceptNewRequest(array $params);
}
