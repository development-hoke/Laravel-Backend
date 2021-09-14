<?php

namespace App\Jobs;

use App\Domain\MemberInterface as MemberService;
use App\HttpCommunication\SendGrid\SendGridServiceInterface as SendGridService;
use App\Mail\OrderInspected as OrderInspectedMail;
use App\Repositories\OrderMessageRepository;
use App\Repositories\OrderRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyOrderInspected implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $params;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        OrderRepository $orderRepository,
        OrderMessageRepository $orderMessageRepository,
        MemberService $memberService,
        SendGridService $sendGridService
    ) {
        $orderId = $this->params['id'];
        $order = $orderRepository->find($orderId);
        $order->member = $memberService->fetchOne($order->member_id);

        $mail = new OrderInspectedMail($order->toArray());
        $mail->to($order->member['email'], $order->member['lname'] . $order->member['fname']);
        $sendGridService->send($mail);

        $orderMessageRepository->create([
            'order_id' => $order->id,
            'title' => $mail->subject,
            'body' => $mail->html ?? $mail->text,
        ]);
    }
}
