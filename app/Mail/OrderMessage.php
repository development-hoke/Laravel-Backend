<?php

namespace App\Mail;

use App\HttpCommunication\SendGrid\Mailable;

class OrderMessage extends Mailable
{
    /**
     * @var array
     */
    private $data;

    public function __construct(array $params)
    {
        $this->data = $params;
    }

    /**
     * メールの構築処理
     *
     * @return $this
     */
    public function build()
    {
        $this->html = $this->data['body'];

        $subject = $this->data['title'];

        return $this->subject($subject);
    }
}
