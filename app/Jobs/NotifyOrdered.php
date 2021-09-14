<?php

namespace App\Jobs;

use App\HttpCommunication\SendGrid\SendGridServiceInterface as SendGridService;
use App\Mail\Ordered as OrderedMail;
use App\Repositories\OrderMessageRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyOrdered implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\Models\Order
     */
    private $order;

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
        \App\Models\Order $order,
        array $params
    ) {
        $this->order = $order;
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        SendGridService $sendGridService,
        OrderMessageRepository $orderMessageRepository
    ) {
        $order = $this->order;
        $order->load(['memberOrderAddress.pref', 'deliveryOrderAddress.pref']);
        $memberOrderAddress = $order->memberOrderAddress;
        $params = $this->params;

        $order->orderDetails->each(function ($orderDetail) {
            switch ($orderDetail->displayed_discount_type) {
                case \App\Enums\OrderDiscount\Type::Normal:
                case \App\Enums\OrderDiscount\Type::Member:
                case \App\Enums\OrderDiscount\Type::Staff:
                case \App\Enums\OrderDiscount\Type::Reservation:
                    $orderDetail->ordered_item_price = $orderDetail->displayed_sale_price;
                    $orderDetail->total_ordered_item_price = $orderDetail->displayed_sale_price * $orderDetail->amount;

                    break;
                default:
                    $orderDetail->ordered_item_price = $orderDetail->retail_price;
                    $orderDetail->total_ordered_item_price = $orderDetail->retail_price * $orderDetail->amount;
                    break;
            }
        });

        $order->orderDetails->each(function ($orderDetail) {
            $orderDetail->total_event_discount_price = $orderDetail->bundle_discount_price + (
                $orderDetail->displayed_discount_type === \App\Enums\OrderDiscount\Type::EventSale
                    ? $orderDetail->displayed_discount_price
                    : 0
            );
        });

        $mail = new OrderedMail($order, $params);
        $mail->to($memberOrderAddress->email, $memberOrderAddress->lname . $memberOrderAddress->fname);
        $sendGridService->send($mail);

        $orderMessageRepository->create([
            'title' => $mail->subject,
            'body' => $mail->html,
            'order_id' => $order->id,
            'type' => \App\Enums\OrderMessage\Type::Store,
        ]);
    }
}
