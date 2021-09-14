<?php

namespace App\HttpCommunication\SendGrid;

abstract class Mailable
{
    /**
     * @var array
     */
    public $to = [];

    /**
     * @var array
     */
    public $from;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var string
     */
    public $html;

    /**
     * @var string
     */
    public $text;

    /**
     * メールの構築処理
     *
     * @return static
     */
    abstract public function build();

    /**
     * 送信元の設定
     *
     * @param string $address
     * @param string $name
     *
     * @return void
     */
    public function from($address, $name = null)
    {
        $this->from = $this->formatAddress($address, $name);

        return $this;
    }

    /**
     * 宛先の追加
     *
     * @param string $address
     * @param string $name
     *
     * @return static
     */
    public function to($address, $name = null)
    {
        $this->to[] = $this->formatAddress($address, $name);

        return $this;
    }

    /**
     * @param string $address
     * @param string $name
     *
     * @return array
     */
    protected function formatAddress($address, $name = null)
    {
        return [
            'address' => $address,
            'name' => $name,
        ];
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
        $this->subject = $subject;

        return $this;
    }

    /**
     * HTMLメールのセット
     *
     * @param string $view
     * @param array $data
     *
     * @return static
     */
    public function view($view, array $data = [])
    {
        $this->html = view($view, $data)->render();

        return $this;
    }

    /**
     * テキストメールのセット
     *
     * @param string $textView
     * @param array $data
     *
     * @return static
     */
    public function text($textView, array $data = [])
    {
        $this->text = view($textView, $data)->render();

        return $this;
    }
}
