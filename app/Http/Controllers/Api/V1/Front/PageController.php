<?php

namespace App\Http\Controllers\Api\V1\Front;

use App\Criteria\Page\FrontPageCriteria;
use App\Http\Controllers\Controller;
use App\Http\Resources\Page as PageResource;
use App\Repositories\PageRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PageController extends Controller
{
    /**
     * @var PageRepository
     */
    private $repository;

    /**
     * @param PageRepository $repository
     */
    public function __construct(PageRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return PageResource
     */
    public function show(Request $request, $slug): PageResource
    {
        try {
            $this->repository->pushCriteria(new FrontPageCriteria());
            $page = $this->repository->findByField('slug', $slug)->whereNull('deleted_at')->first();

            if (empty($page)) {
                throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('slug')));
            }

            return new PageResource($page);
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('slug')), $e);
        }
    }
}
