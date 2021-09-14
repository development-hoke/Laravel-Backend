<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\Event\CopyRequest;
use App\Http\Requests\Api\V1\Admin\Event\CreateRequest;
use App\Http\Requests\Api\V1\Admin\Event\DeleteRequest;
use App\Http\Requests\Api\V1\Admin\Event\IndexRequest;
use App\Http\Requests\Api\V1\Admin\Event\ShowRequest;
use App\Http\Requests\Api\V1\Admin\Event\UpdateRequest;
use App\Http\Resources\Event as EventResource;
use App\Repositories\EventRepository;
use App\Services\Admin\EventServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EventController extends ApiAdminController
{
    /**
     * @var EventRepository
     */
    private $repository;

    /**
     * @var EventServiceInterface
     */
    private $service;

    /**
     * @param EventRepository $repository
     */
    public function __construct(EventRepository $repository, EventServiceInterface $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * @param IndexRequest $request
     *
     * @return Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $events = $this->repository->orderBy('created_at', 'desc')->paginate(config('repository.pagination.admin_limit'));

        return EventResource::collection($events);
    }

    /**
     * @param ShowRequest $request
     * @param int $id
     *
     * @return EventResource
     */
    public function show(ShowRequest $request, int $id): EventResource
    {
        try {
            $event = $this->repository->with(['eventBundleSales'])->find($id);

            return new EventResource($event);
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('id')), $e);
        }
    }

    /**
     * @param CreateRequest $request
     *
     * @return EventResource
     */
    public function store(CreateRequest $request): EventResource
    {
        $event = $this->service->create($request->validated());

        return new EventResource($event);
    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     *
     * @return EventResource
     */
    public function update(UpdateRequest $request, int $id): EventResource
    {
        try {
            $event = $this->service->update($request->validated(), $id);

            return new EventResource($event);
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('id')), $e);
        }
    }

    /**
     * @param CreateRequest $request
     * @param int $id
     *
     * @return EventResource
     */
    public function copy(CopyRequest $request, int $id)
    {
        list($event) = $this->service->copy($id);

        return new EventResource($event);
    }

    public function delete(DeleteRequest $request, int $id)
    {
        try {
            $this->service->delete($id);
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found'), $e);
        }
    }
}
