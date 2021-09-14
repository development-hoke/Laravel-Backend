<?php

namespace App\Jobs;

use App\HttpCommunication\SendGrid\SendGridServiceInterface as SendGridService;
use App\Mail\RedisplayArrivaled as RedisplayArrivaledMail;
use App\Models\ItemDetailRedisplayRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyRedisplayRequested implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        SendGridService $sendGridService
    ) {
        $itemDetailId = $this->id;
        $itemDetailRedisplayRequests = ItemDetailRedisplayRequest::sendTarget($itemDetailId)->with('itemDetail')->get();

        foreach ($itemDetailRedisplayRequests as $itemDetailRedisplayRequest) {
            $data = [
                'itemDetailRedisplayRequest' => $itemDetailRedisplayRequest,
            ];
            $mail = new RedisplayArrivaledMail($data);
            $mail->to($itemDetailRedisplayRequest->email, $itemDetailRedisplayRequest->user_name);
            $sendGridService->send($mail);

            ItemDetailRedisplayRequest::where('id', $itemDetailRedisplayRequest->id)->update(['is_notified' => 1]);
        }
    }
}
