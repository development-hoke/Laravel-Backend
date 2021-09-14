<?php

namespace App\HttpCommunication\Response;

interface FRegiResponseInterface extends ResponseInterface
{
    const RESULT_OK = 'OK';
    const RESULT_NG = 'NG';

    /**
     * @return bool
     */
    public function hasErrorCode();

    /**
     * @return string
     */
    public function getErrorCode();
}
