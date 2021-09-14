<?php

namespace App\Http\Controllers\Api\V1\External;

use App\Http\Controllers\Api\V1\Controller as ApiController;
use App\Notifications\SlackNotification;
use Illuminate\Notifications\Notifiable;

class Controller extends ApiController
{
    use Notifiable;

    protected function routeNotificationForSlack()
    {
        return config('slack.webhook');
    }

    /**
     * 異常終了
     *
     * @param array $fields
     *
     * @return void
     */
    public function sendFailure(array $fields)
    {
        $this->notify(new SlackNotification('<!channel> API Error', [
            'title' => url()->full(),
            'error' => true,
            'url' => '',
            'fields' => $fields,
        ]));
    }

    protected function success($params = [])
    {
        return $params + [
            'result' => 'success',
        ];
    }
}
