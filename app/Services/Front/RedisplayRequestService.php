<?php

namespace App\Services\Front;

use App\HttpCommunication\SendGrid\SendGridServiceInterface;
use App\Mail\RedisplayCanceled as RedisplayCanceledMail;
use App\Mail\RedisplayRequested as RedisplayRequestedMail;
use App\Repositories\ItemDetailRedisplayRequestRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RedisplayRequestService extends Service implements RedisplayRequestServiceInterface
{
    /**
     * @var ItemDetailRedisplayRequestRepository
     */
    private $redisplayRequestRepository;

    /**
     * @param ItemDetailRedisplayRequestRepository $redisplayRequestRepository
     * @param SendGridServiceInterface $sendGridService
     */
    public function __construct(
        ItemDetailRedisplayRequestRepository $redisplayRequestRepository,
        SendGridServiceInterface $sendGridService
    ) {
        $this->redisplayRequestRepository = $redisplayRequestRepository;
        $this->sendGridService = $sendGridService;
    }

    /**
     * @param array $params
     *
     * @return \App\Models\ItemDetailRedisplayRequest
     */
    public function acceptNewRequest(array $params)
    {
        $redispalyRequest = $this->redisplayRequestRepository->create($params);

        $redispalyRequest->load(['itemDetail.item']);

        $mail = new RedisplayRequestedMail([
            'itemDetailRedisplayRequest' => $redispalyRequest,
        ]);

        $mail->to($redispalyRequest->email, $redispalyRequest->name);

        $this->sendGridService->send($mail);

        return $redispalyRequest;
    }

    /**
     * @param array $params
     *
     * @return \App\Models\ItemDetailRedisplayRequest
     */
    public function destroy(array $params)
    {
        $redispalyRequest = $this->redisplayRequestRepository->findWhere($params)->first();

        if (empty($redispalyRequest)) {
            throw new NotFoundHttpException(error_format('error.model_not_found'));
        }

        $redispalyRequest->load(['itemDetail.item']);

        $mail = new RedisplayCanceledMail([
            'itemDetailRedisplayRequest' => $redispalyRequest,
        ]);

        $redispalyRequest->delete();

        $mail->to($redispalyRequest->email, $redispalyRequest->name);

        $this->sendGridService->send($mail);

        return $redispalyRequest;
    }
}
