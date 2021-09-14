<?php

namespace App\HttpCommunication\SendGrid;

use App\HttpCommunication\SendGrid\Exceptions\HttpException;
use SendGrid;

interface SendGridServiceInterface
{
    /**
     * @param string $apiKey
     *
     * @return static
     */
    public function setApiKey(string $apiKey);

    /**
     * @param Mailable $email
     *
     * @return SendGrid\Response
     *
     * @throws HttpException
     */
    public function send(Mailable $builder);
}
