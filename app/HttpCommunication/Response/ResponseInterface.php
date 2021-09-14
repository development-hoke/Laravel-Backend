<?php

namespace App\HttpCommunication\Response;

interface ResponseInterface
{
    /**
     * @return int
     */
    public function getStatusCode();

    /**
     * @return string[][]
     */
    public function getHeaders();

    /**
     * @return array
     */
    public function getBody();
}
