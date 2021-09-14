<?php

namespace App\Jobs;

use App\HttpCommunication\SendGrid\SendGridServiceInterface as SendGridService;
use App\Mail\LowInventory as LowInventoryMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyLowInventory implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $params;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        SendGridService $sendGridService
    ) {
        $itemDetailIdentifications = $this->params;
        $data = [
            'itemDetailIdentifications' => $itemDetailIdentifications,
        ];
        $mail = new LowInventoryMail($data);
        $mail->to(env('CONTACT_ADMIN_EMAIL'), '');
        $sendGridService->send($mail);
    }
}
