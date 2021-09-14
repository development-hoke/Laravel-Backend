<?php

namespace App\Mail;

use App\HttpCommunication\SendGrid\Mailable;

class SlowMovingInventory extends Mailable
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
            ->view('emails.slowMovingInventory', $this->data)
            ->subject(__('emails.subject.slow_moving_inventory'));
    }
}
