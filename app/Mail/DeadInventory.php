<?php

namespace App\Mail;

use App\HttpCommunication\SendGrid\Mailable;

class DeadInventory extends Mailable
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
            ->view('emails.deadInventory', $this->data)
            ->subject(__('emails.subject.dead_inventory'));
    }
}
