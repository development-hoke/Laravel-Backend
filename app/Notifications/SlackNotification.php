<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class SlackNotification extends Notification
{
    use Queueable;

    protected $content;
    protected $channel;
    protected $attachments;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($content, $attachments = [])
    {
        $this->channel = config('slack.channel');
        $this->content = $content;
        $this->attachments = $attachments;
        // dd($this->channel);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        $message = (new SlackMessage())
            ->from('watcher')
            ->to($this->channel)
            ->content($this->content);
        if ($this->attachments) {
            $attachments = $this->attachments;
            $message->attachment(function (SlackAttachment $attachment) use ($attachments) {
                $attachment
                    ->title($attachments['title'], @$attachments['url'])
                    ->fields($attachments['fields'])
                    ->timestamp(Carbon::now());
            });

            if (isset($this->attachments['success'])) {
                $message->success();
            }
            if (isset($this->attachments['error'])) {
                $message->error();
            }
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
        ];
    }
}
