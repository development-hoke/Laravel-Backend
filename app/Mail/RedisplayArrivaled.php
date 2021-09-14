<?php

namespace App\Mail;

use App\HttpCommunication\SendGrid\Mailable;

class RedisplayArrivaled extends Mailable
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
            ->view('emails.redisplayArrivaled', $this->data)
            ->subject(__('emails.subject.redisplay_arrivaled'));
    }
}
