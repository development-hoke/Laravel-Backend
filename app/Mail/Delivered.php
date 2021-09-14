<?php

namespace App\Mail;

use App\HttpCommunication\SendGrid\Mailable;

class Delivered extends Mailable
{
    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * メールの構築処理
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->view('emails.orders.delivered', $this->data)
            ->subject(__('emails.subject.delivered'));
    }
}
