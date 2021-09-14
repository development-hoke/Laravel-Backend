<?php

namespace App\Utils;

use App\Notifications\SlackNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification as BaseNotification;

class Notification extends BaseNotification
{
    const MESSAGE_TYPE_ERROR = 'error';
    const MESSAGE_TYPE_SUCCESS = 'success';

    /**
     * @param string $title
     * @param array $fields
     * @param string|null $messageType
     * @param string $content
     * @param string $url
     *
     * @return void
     */
    public static function sendSlack(string $title, array $fields = [], ?string $messageType = null, string $content = '', string $url = '')
    {
        $attachments = [
            'title' => $title,
            'url' => $url,
            'fields' => $fields,
        ];

        switch ($messageType) {
            case static::MESSAGE_TYPE_ERROR:
                $attachments['error'] = true;
                $content = $content ?: 'Error';
                break;

            case static::MESSAGE_TYPE_SUCCESS:
                $attachments['success'] = true;
                $content = $content ?: 'Success';
                break;
        }

        static::send(
            (new AnonymousNotifiable())->route('slack', config('slack.webhook')),
            new SlackNotification(implode(' ', ['<!channel>', $content]), $attachments)
        );
    }

    /**
     * @param string $title
     * @param array $fields
     * @param string $content
     * @param string $url
     *
     * @return void
     */
    public static function sendSlackError(string $title, array $fields = [], string $content = '', string $url = '')
    {
        static::sendSlack($title, $fields, static::MESSAGE_TYPE_ERROR, $content, $url);
    }
}
