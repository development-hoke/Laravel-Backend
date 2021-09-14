<?php

namespace App\Services\Admin;

use App\Domain\MemberInterface as MemberService;
use App\Exceptions\FatalException;
use App\Repositories\EventUserRepository;
use App\Utils\Arr;
use App\Utils\Csv\ImportCsvInterface;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EventUserService extends Service implements EventUserServiceInterface
{
    /**
     * @var EventUserRepository
     */
    private $eventUserRepository;

    /**
     * @var ImportCsvInterface
     */
    private $importCsvUtil;

    /**
     * @var MemberService
     */
    private $memberService;

    /**
     * @param EventUserRepository $eventUserRepository
     * @param ImportCsvInterface $importCsvUtil
     * @param MemberService $memberService
     */
    public function __construct(EventUserRepository $eventUserRepository, ImportCsvInterface $importCsvUtil, MemberService $memberService)
    {
        $this->eventUserRepository = $eventUserRepository;
        $this->importCsvUtil = $importCsvUtil;
        $this->memberService = $memberService;

        if (auth('admin_api')->check()) {
            $staff = auth('admin_api')->user();
            $this->memberService->setStaffToken($staff->token);
        }
    }

    /**
     * CSVの保存を実行する。
     * エラーメッセージの配列と保存済みのデータの配列を返す。
     *
     * @param array $params
     * @param int $eventId
     *
     * @return array
     */
    public function storeCsv(array $params, int $eventId)
    {
        $this->setupImportingCsv();

        $params = $this->importCsvUtil->extract($params['content']);

        $succeeded = [];
        $failed = [];
        $validated = [];

        foreach ($params as $index => $param) {
            try {
                $this->importCsvUtil->validate($param);

                $validated[$param['member_id']] = [
                    'index' => $index,
                    'member_id' => $param['member_id'],
                ];
            } catch (Exception $e) {
                if ($e instanceof ValidationException === false) {
                    report($e);
                    $e = new FatalException(error_format('error.unexpected'), null, $e);
                }

                $failed[$index] = $this->importCsvUtil->formatErrorReport($e, $index + 1);
            }
        }

        $members = $this->memberService->fetchBatchMembers(collect($validated)->pluck('member_id'));
        $memberDict = Arr::dict($members);

        foreach ($validated as $params) {
            if (!isset($memberDict[$params['member_id']])) {
                $failed[$params['index']] = $this->importCsvUtil->formatErrorReport(
                    __('validation.exists', ['attribute' => __('validation.attributes.event_user.member_id')]),
                    $params['index'] + 1
                );
            }
        }

        foreach ($members as $member) {
            try {
                $model = $this->eventUserRepository->updateOrCreate(
                    [
                        'event_id' => $eventId,
                        'member_id' => $member['id'],
                    ],
                );
                $succeeded[] = $model;
            } catch (Exception $e) {
                report($e);
                $failed[$index] = $this->importCsvUtil->formatErrorReport(
                    new FatalException(error_format('error.unexpected'), null, $e),
                    $index + 1
                );
            }
        }

        ksort($failed);

        return ['failed' => array_values($failed), 'succeeded' => $succeeded];
    }

    /**
     * @return void
     */
    private function setupImportingCsv()
    {
        $this->importCsvUtil->setHeaders([
            0 => 'member_id',
        ]);

        $this->importCsvUtil->setValidationRules([
            'member_id' => 'required|integer',
        ]);

        $this->importCsvUtil->setValidationAttributes([
            'member_id' => __('validation.attributes.event_user.member_id'),
        ]);
    }

    /**
     * @param int $eventId
     * @param int $limit
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function paginate(int $eventId, int $limit)
    {
        $eventUsers = $this->eventUserRepository
            ->scopeQuery(function ($query) use ($eventId) {
                return $query->where('event_id', $eventId);
            })
            ->paginate($limit);

        if ($eventUsers->isEmpty()) {
            return $eventUsers;
        }

        $members = $this->memberService->fetchBatchMembers($eventUsers->pluck('member_id'));
        $members = Arr::dict($members);

        foreach ($eventUsers as $eventUser) {
            $eventUser->member = $members[$eventUser->member_id] ?? null;
        }

        return $eventUsers;
    }

    /**
     * @param int $eventId
     * @param int $id
     *
     * @return void
     */
    public function delete(int $eventId, int $id)
    {
        $params = ['event_id' => $eventId, 'id' => $id];

        $eventItem = $this->eventUserRepository->findWhere($params)->first();

        if (empty($eventItem)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', $params));
        }

        $this->eventUserRepository->deleteWhere($params);
    }
}
