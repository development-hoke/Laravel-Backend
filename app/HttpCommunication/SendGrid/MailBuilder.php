<?php

namespace App\HttpCommunication\SendGrid;

use App\Exceptions\FatalException;
use SendGrid\Mail\From;
use SendGrid\Mail\Mail as SendGridMail;
use SendGrid\Mail\MimeType;

class MailBuilder
{
    /**
     * @var Mailable
     */
    protected $mailable;

    /**
     * @var \SendGrid\Mail\Mail
     */
    protected $email;

    /**
     * @var array
     */
    protected $required = ['to', 'from', 'subject'];

    /**
     * @param Mailable $mailable
     * @param SendGridMail $email
     */
    public function __construct(Mailable $mailable, SendGridMail $email = null)
    {
        $this->mailable = $mailable;
        $this->email = $email ?? new SendGridMail();
    }

    /**
     * @return SendGridMail
     */
    public function build()
    {
        $mailable = $this->mailable;

        $mailable->build();

        if (!isset($mailable->from)) {
            $mailable->from(
                config('http_communication.send_grid.from.address'),
                config('http_communication.send_grid.from.name')
            );
        }

        foreach (get_object_vars($mailable) as $key => $value) {
            if (empty($value) && in_array($key, $this->required, true)) {
                throw new FatalException(__('error.required_parameter', ['name' => $key]));
            }

            if (!isset($value)) {
                continue;
            }

            if (method_exists($this, $key)) {
                $this->{$key}($value);
            }
        }

        return $this->email;
    }

    /**
     * 送信元の設定
     *
     * @param array $from
     *
     * @return void
     */
    public function from($from)
    {
        $this->email->setFrom(new From($from['address'], $from['name']));

        return $this;
    }

    /**
     * 宛先の追加
     *
     * @param array $toList
     *
     * @return static
     */
    public function to($toList)
    {
        foreach ((array) $toList as $to) {
            $this->email->addTo($to['address'], $to['name']);
        }

        return $this;
    }

    /**
     * 件名の設定
     *
     * @param string $subject
     *
     * @return static
     */
    public function subject(string $subject)
    {
        $this->email->setSubject($subject);

        return $this;
    }

    /**
     * HTMLメールのセット
     *
     * @param string $view
     *
     * @return static
     */
    public function html($html)
    {
        $this->email->addContent(MimeType::HTML, $html);

        return $this;
    }

    /**
     * テキストメールのセット
     *
     * @param string $textView
     *
     * @return static
     */
    public function text($text)
    {
        $this->email->addContent(MimeType::TEXT, $text);

        return $this;
    }
}
