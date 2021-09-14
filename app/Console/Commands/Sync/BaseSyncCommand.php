<?php

namespace App\Console\Commands\Sync;

use App\Notifications\SlackNotification;
use Illuminate\Console\Command;
use Illuminate\Notifications\Notifiable;

class BaseSyncCommand extends Command
{
    use Notifiable;

    protected $success = 0;
    protected $failure = 0;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function routeNotificationForSlack()
    {
        return config('slack.webhook');
    }

    /**
     *  スタート
     */
    public function sendStart()
    {
        $this->notify(new SlackNotification('Sync start. '.get_class($this)));
    }

    /**
     *  正常終了
     */
    public function sendSuccess()
    {
        $this->notify(new SlackNotification('Sync success.', [
            'title' => get_class($this),
            'url' => '',
            'success' => true,
            'fields' => [
                'success' => $this->success.'件',
                'failure' => $this->failure.'件',
            ],
        ]));
    }

    /**
     *  異常終了
     */
    public function sendFailure($error)
    {
        $this->notify(new SlackNotification('<!channel> Sync Error!!', [
            'title' => get_class($this),
            'error' => true,
            'url' => '',
            'fields' => [
                'success' => $this->success.'件',
                'failure' => $this->failure.'件',
                'error' => $error,
            ],
        ]));
    }
}
