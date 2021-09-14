<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\Help\CreateRequest;
use App\Http\Requests\Api\V1\Admin\Help\DeleteRequest;
use App\Http\Requests\Api\V1\Admin\Help\IndexRequest;
use App\Http\Requests\Api\V1\Admin\Help\ShowRequest;
use App\Http\Requests\Api\V1\Admin\Help\UpdateRequest;
use App\Http\Resources\Help as HelpResource;
use App\Repositories\HelpRepository;
use App\Services\Admin\HelpServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HelpController extends ApiAdminController
{
    /**
     * @var HelpRepository
     */
    private $repository;

    /**
     * @var HelpServiceInterface
     */
    private $service;

    /**
     * @param HelpRepository $repository
     * @param HelpServiceInterface $service
     */
    public function __construct(HelpRepository $repository, HelpServiceInterface $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * @param IndexRequest $request
     *
     * @return HelpResource
     */
    public function index(IndexRequest $request)
    {
        $params = $request->validated();
        $this->repository->pushCriteria(new \App\Criteria\Help\AdminSortCriteria($params));
        $help = $this->repository->paginate(config('repository.pagination.admin_limit'));

        return HelpResource::collection($help);
    }

    /**
     * @param ShowRequest $request
     * @param int $id
     *
     * @return HelpResource
     */
    public function show(ShowRequest $request, int $id): HelpResource
    {
        try {
            $help = $this->repository->with([
                'helpCategories',
            ])->find($id);

            return new HelpResource($help);
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('id')), $e);
        }
    }

    /**
     * @param CreateRequest $request
     *
     * @return HelpResource
     */
    public function store(CreateRequest $request): HelpResource
    {
        $params = $request->validated();

        $help = $this->service->create($params);

        $help->load('helpCategories');

        return new HelpResource($help);
    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     *
     * @return HelpResource
     */
    public function update(UpdateRequest $request, int $id): HelpResource
    {
        $params = $request->validated();

        $help = $this->service->update($params, $id);

        $help->load('helpCategories');

        return new HelpResource($help);
    }

    /**
     * @param DeleteRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteRequest $request, int $id)
    {
        $this->service->delete($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
