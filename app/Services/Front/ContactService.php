<?php

namespace App\Services\Front;

use App\HttpCommunication\SendGrid\SendGridServiceInterface;
use App\Mail\Contact as ContactMail;
use App\Mail\ContactAutoReply as ContactAutoReplyMail;

class ContactService extends Service implements ContactServiceInterface
{
    /**
     * @param SendGridServiceInterface $sendGridService
     */
    public function __construct(
        SendGridServiceInterface $sendGridService
    ) {
        $this->sendGridService = $sendGridService;
    }

    /**
     * @param array $params
     */
    public function send(array $params)
    {
        $mail = new ContactMail($params);
        $mail->to(env('CONTACT_ADMIN_EMAIL'), "{$params['lastName']} {$params['firstName']}");
        $this->sendGridService->send($mail);

        $mail = new ContactAutoReplyMail($params);
        $mail->to($params['email'], "{$params['lastName']} {$params['firstName']}");
        $this->sendGridService->send($mail);

        return true;
    }
}
