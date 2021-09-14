<?php

namespace App\Jobs;

use App\HttpCommunication\SendGrid\SendGridServiceInterface as SendGridService;
use App\Mail\OrderMessage as OrderMessageMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderMessage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\Models\OrderMessage
     */
    private $orderMessage;

    /**
     * @var array
     */
    private $params;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        \App\Models\OrderMessage $orderMessage,
        array $params
    ) {
        $this->orderMessage = $orderMessage;
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
        $member = $this->params['member'];
        $orderMessage = $this->orderMessage;

        $mail = new OrderMessageMail($orderMessage->toArray());
        $mail->to($member['email'], $member['lname'] . $member['fname']);
        $sendGridService->send($mail);
    }
}
