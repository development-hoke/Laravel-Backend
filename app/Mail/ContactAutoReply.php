<?php

namespace App\Mail;

use App\HttpCommunication\SendGrid\Mailable;

class ContactAutoReply extends Mailable
{
    /**
     * @var array
     */
    private $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->view('emails.contact.autoReply', $this->data)
            ->subject(__('emails.subject.contact_autoreply'));
    }
}
