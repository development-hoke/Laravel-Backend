<?php

namespace App\HttpCommunication\SendGrid\Concrete;

use App\HttpCommunication\SendGrid\Exceptions\HttpException;
use App\HttpCommunication\SendGrid\HttpCommunicationService;
use App\HttpCommunication\SendGrid\Mailable;
use App\HttpCommunication\SendGrid\MailBuilder;
use App\HttpCommunication\SendGrid\SendGridServiceInterface;
use SendGrid;

class SendGridService extends HttpCommunicationService implements SendGridServiceInterface
{
    /**
     * @var string
     */
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('http_communication.send_grid.api_key');
    }

    /**
     * @param string $apiKey
     *
     * @return static
     */
    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @param Mailable $email
     *
     * @return SendGrid\Response
     */
    public function send(Mailable $mailable)
    {
        $email = (new MailBuilder($mailable))->build();

        $sendgrid = new SendGrid($this->apiKey);

        $response = $sendgrid->send($email);

        if ($response->statusCode() >= 400) {
            throw new HttpException($response->statusCode(), $response->body());
        }

        return $response;
    }
}
