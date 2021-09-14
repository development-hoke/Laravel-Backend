<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\EventItem\DeleteRequest;
use App\Http\Requests\Api\V1\Admin\EventItem\IndexRequest;
use App\Http\Requests\Api\V1\Admin\EventItem\StoreCsvRequest;
use App\Http\Requests\Api\V1\Admin\EventItem\StoreRequest;
use App\Http\Requests\Api\V1\Admin\EventItem\UpdateRequest;
use App\Http\Resources\EventItem as EventItemResource;
use App\Repositories\EventItemRepository;
use App\Repositories\ItemRepository;
use App\Services\Admin\EventItemServiceInterface;
use Illuminate\Http\Response;

class EventItemController extends ApiAdminController
{
    /**
     * @var EventItemServiceInterface
     */
    private $service;

    /**
     * @param EventItemServiceInterface $service
     * @param EventItemRepository $eventItemRepository
     * @param ItemRepository $itemRepository
     */
    public function __construct(EventItemServiceInterface $service)
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
        $eventItems = $this->service->paginate($eventId, config('repository.pagination.admin_limit'));

        return EventItemResource::collection($eventItems);
    }

    /**
     * @param UpdateRequest $request
     * @param int $eventId
     * @param int $id
     *
     * @return EventItemResource
     */
    public function update(UpdateRequest $request, int $eventId, int $id)
    {
        $attributes = $request->except(['id', 'event_id']);

        $eventItem = $this->service->update($attributes, $eventId, $id);

        return new EventItemResource($eventItem);
    }

    /**
     * @param StoreRequest $request
     * @param int $eventId
     *
     * @return EventItemResource
     */
    public function store(StoreRequest $request, int $eventId)
    {
        $eventItem = $this->service->store($request->validated(), $eventId);

        return new EventItemResource($eventItem);
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
