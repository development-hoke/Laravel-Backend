<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\EventUser\DeleteRequest;
use App\Http\Requests\Api\V1\Admin\EventUser\IndexRequest;
use App\Http\Requests\Api\V1\Admin\EventUser\StoreCsvRequest;
use App\Http\Resources\EventUser as EventUserResource;
use App\Services\Admin\EventUserServiceInterface;
use Illuminate\Http\Response;

class EventUserController extends ApiAdminController
{
    /**
     * @var EventUserServiceInterface
     */
    private $service;

    /**
     * @param EventUserServiceInterface $service
     */
    public function __construct(EventUserServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * @param IndexRequest $request
     * @param int $eventId
     *
     * @return Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index(IndexRequest $request, int $eventId)
    {
        $eventUsers = $this->service->paginate($eventId, config('repository.pagination.admin_limit'));

        return EventUserResource::collection($eventUsers);
    }

    /**
     * @param DeleteRequest $request
     * @param int $eventId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteRequest $request, int $eventId, int $id)
    {
        $this->service->delete($eventId, $id);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param StoreCsvRequest $request
     * @param int $eventId
     *
     * @return \Illuminate\Http\Response
     */
    public function storeCsv(StoreCsvRequest $request, int $eventId)
    {
        $reslst = $this->service->storeCsv($request->validated(), $eventId);

        return response(['data' => $reslst]);
    }
}
