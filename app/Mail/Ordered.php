<?php

namespace App\Mail;

use App\HttpCommunication\SendGrid\Mailable;
use Illuminate\Support\Str;

class Ordered extends Mailable
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var bool
     */
    private $isGuest;

    private $configs = [
        \App\Enums\Order\OrderType::Normal => 'ordered',
        \App\Enums\Order\OrderType::Reserve => 'orderedReservation',
        \App\Enums\Order\OrderType::BackOrder => 'orderedBackOrder',
    ];

    public function __construct(\App\Models\Order $order, array $params)
    {
        $this->data = [
            'order' => $order,
            'message' => $params['message'] ?? null,
        ];

        $this->isGuest = $params['is_guest'] ?? false;

        if ($this->isGuest) {
            $this->data['memberActivateLink'] = $params['member_activate_link'];
        }
    }

    /**
     * メールの構築処理
     *
     * @return $this
     */
    public function build()
    {
        if ($this->isGuest) {
            $templateName = 'guestOrdered';
        } else {
            $type = $this->data['order']->order_type;
            $templateName = $this->configs[$type];
        }

        $subjectName = Str::snake($templateName);

        return $this->view('emails.orders.'.$templateName, $this->data)
            ->subject(__('emails.subject.'.$subjectName));
    }
}
