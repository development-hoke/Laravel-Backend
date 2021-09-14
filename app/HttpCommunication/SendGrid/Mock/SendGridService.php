<?php

namespace App\HttpCommunication\SendGrid\Mock;

use App\HttpCommunication\SendGrid\HttpCommunicationService;
use App\HttpCommunication\SendGrid\Mailable;
use App\HttpCommunication\SendGrid\MailBuilder;
use App\HttpCommunication\SendGrid\SendGridServiceInterface;
use Illuminate\Support\Facades\Log;
use SendGrid;
use SendGrid\Response;

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

        Log::debug(sprintf('(SendGrid) API Key: %s', $this->apiKey));
        Log::debug(sprintf('(SendGrid) Mail Content: %s', json_encode($email->jsonSerialize(), JSON_UNESCAPED_UNICODE)));

        return new Response(200);
    }
}
