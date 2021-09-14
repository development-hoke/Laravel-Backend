<?php

namespace App\Http\Controllers\Api\V1\External;

use App\Domain\AmazonPayInterface;
use App\Exceptions\ErrorUtil;
use App\Http\Response;
use App\HttpCommunication\AmazonPay\Exceptions\InvalidIpnMessageException;
use App\HttpCommunication\AmazonPay\IpnReciever;
use App\Repositories\AmazonPay\NotificationRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AmazonPayIpnController extends Controller
{
    /**
     * @var array
     */
    private $ignoreMessageTypes = [
        \App\Enums\AmazonPay\NotificationType::ChargebackDetailedNotification,
    ];

    /**
     * @var array
     */
    private $dontReportExceptions = [
        // HttpException::class,
        // ModelNotFoundException::class,
    ];

    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * @var AmazonPayInterface
     */
    private $amazonPayService;

    /**
     * @var IpnReciever
     */
    private $ipnReciever;

    /**
     * @param NotificationRepository $notificationRepository
     * @param AmazonPayInterface $amazonPayService
     */
    public function __construct(
        NotificationRepository $notificationRepository,
        AmazonPayInterface $amazonPayService,
        IpnReciever $ipnReciever
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->amazonPayService = $amazonPayService;
        $this->ipnReciever = $ipnReciever;
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function recieve()
    {
        try {
            $data = $this->ipnReciever->revieve();

            if ($this->notificationRepository->findWhere([
                ['message_id', '=', $data['MessageId']],
                ['status', '!=', \App\Enums\AmazonPay\NotificationStatus::Failed],
            ])->isNotEmpty()) {
                return response(null, Response::HTTP_CONFLICT);
            }

            if ($this->shouldIgnoreMessage($data)) {
                return response(null, Response::HTTP_OK);
            }

            $objectId = $this->extractObjectId($data);

            $notification = $this->notificationRepository->updateOrCreate([
                'message_id' => $data['MessageId'],
            ], [
                'notification_reference_id' => $data['NotificationReferenceId'],
                'requested_body' => json_encode($data),
                'type' => $data['NotificationType'],
                'amazon_object_id' => $objectId,
            ]);

            $this->importMessage($data);

            $this->notificationRepository->update([
                'status' => \App\Enums\AmazonPay\NotificationStatus::Processed,
            ], $notification->id);

            return response(null, Response::HTTP_OK);
        } catch (InvalidIpnMessageException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, '', $e);
        } catch (\Exception $e) {
            $notification = $this->notificationRepository->findWhere(['message_id' => $data['MessageId']])->first();

            $failedInfo = [
                'message_id' => $data['MessageId'],
                'error' => implode("\n", ErrorUtil::formatException($e)),
            ];

            if (!empty($notification)) {
                $this->notificationRepository->update([
                    'status' => \App\Enums\AmazonPay\NotificationStatus::Failed,
                    'failed_info' => json_encode($failedInfo),
                ], $notification->id);
            }

            if (in_array(get_class($e), $this->dontReportExceptions, true)) {
                throw $e;
            }

            $this->sendFailure($failedInfo);

            throw $e;
        }
    }

    /**
     * @param array $message
     *
     * @return bool
     */
    private function shouldIgnoreMessage(array $message)
    {
        if (in_array($message['NotificationType'], $this->ignoreMessageTypes)) {
            return true;
        }

        return false;
    }

    /**
     * @param array $message
     *
     * @return string
     */
    private function extractObjectId(array $message)
    {
        switch ($message['NotificationType']) {
            case \App\Enums\AmazonPay\NotificationType::OrderReferenceNotification:
                return $message['OrderReference']['AmazonOrderReferenceId'];
            case \App\Enums\AmazonPay\NotificationType::PaymentAuthorize:
                return $message['AuthorizationDetails']['AmazonAuthorizationId'];
            case \App\Enums\AmazonPay\NotificationType::PaymentCapture:
                return $message['CaptureDetails']['AmazonCaptureId'];
            case \App\Enums\AmazonPay\NotificationType::PaymentRefund:
                return $message['RefundDetails']['AmazonRefundId'];
            default:
                throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param array $message
     *
     * @return \App\Entities\AmazonPay\OrderReferenceDetails|\App\Entities\AmazonPay\AuthorizationDetails|\App\Entities\AmazonPay\CaptureDetails|\App\Entities\AmazonPay\RefundDetails
     */
    private function importMessage(array $message)
    {
        switch ($message['NotificationType']) {
            case \App\Enums\AmazonPay\NotificationType::OrderReferenceNotification:
                return $this->amazonPayService->importAmazonPayOrder(
                    new \App\Entities\AmazonPay\OrderReferenceDetails($message['OrderReference'])
                );

            case \App\Enums\AmazonPay\NotificationType::PaymentAuthorize:
                return $this->amazonPayService->importAuthorizationDetails(
                    new \App\Entities\AmazonPay\AuthorizationDetails($message['AuthorizationDetails'])
                );

            case \App\Enums\AmazonPay\NotificationType::PaymentCapture:
                return $this->amazonPayService->importCaptureDetails(
                    new \App\Entities\AmazonPay\CaptureDetails($message['CaptureDetails'])
                );

            case \App\Enums\AmazonPay\NotificationType::PaymentRefund:
                // TODO: Refundの処理を作成する
                // return new \App\Entities\AmazonPay\RefundDetails($message['RefundDetails']);
                return;

            default:
                throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
