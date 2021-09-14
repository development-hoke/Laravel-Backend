<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\Page\CopyRequest;
use App\Http\Requests\Api\V1\Admin\Page\CreateRequest;
use App\Http\Requests\Api\V1\Admin\Page\DeleteRequest;
use App\Http\Requests\Api\V1\Admin\Page\IndexRequest;
use App\Http\Requests\Api\V1\Admin\Page\ShowRequest;
use App\Http\Requests\Api\V1\Admin\Page\UpdateRequest;
use App\Http\Resources\Page as PageResource;
use App\Repositories\PageRepository;
use App\Services\Admin\PageServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PageController extends ApiAdminController
{
    /**
     * @var PageRepository
     */
    private $repository;

    /**
     * @var PageServiceInterface
     */
    private $service;

    /**
     * @param PageRepository $repository
     */
    public function __construct(PageRepository $repository, PageServiceInterface $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * @param IndexRequest $request
     *
     * @return PageResource
     */
    public function index(IndexRequest $request)
    {
        $page = $this->repository->paginate(config('repository.pagination.admin_limit'));

        return PageResource::collection($page);
    }

    /**
     * @param ShowRequest $request
     * @param int $id
     *
     * @return PageResource
     */
    public function show(ShowRequest $request, int $id): PageResource
    {
        try {
            $page = $this->repository->find($id);

            return new PageResource($page);
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('id')), $e);
        }
    }

    /**
     * @param CreateRequest $request
     *
     * @return PageResource
     */
    public function store(CreateRequest $request): PageResource
    {
        $page = $this->repository->create($request->except('id'));

        return new PageResource($page);
    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     *
     * @return PageResource
     */
    public function update(UpdateRequest $request, int $id): PageResource
    {
        try {
            $page = $this->repository->update($request->except('id'), $id);

            return new PageResource($page);
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('id')), $e);
        }
    }

    /**
     * @param DeleteRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteRequest $request, int $id)
    {
        $this->repository->delete($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param CopyRequest $request
     * @param int $id
     *
     * @return PageResource
     */
    public function copy(CopyRequest $request, int $id)
    {
        $page = $this->service->copy($id);

        return new PageResource($page);
    }
}
