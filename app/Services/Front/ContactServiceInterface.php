<?php

namespace App\Services\Front;

interface ContactServiceInterface
{
    /**
     * @param array $params
     */
    public function send(array $params);
}
