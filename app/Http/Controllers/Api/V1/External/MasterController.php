<?php

namespace App\Http\Controllers\Api\V1\External;

use App\Http\Resources\OnlineCategory as OnlineCategoryResource;
use App\Http\Resources\OnlineTag as OnlineTagResource;
use App\Repositories\OnlineCategoryRepository;
use App\Repositories\OnlineTagRepository;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    /**
     * @var OnlineCategoryRepository
     */
    protected $onlineCategoryRepository;

    /**
     * @var OnlineTagRepository
     */
    protected $onlineTagRepository;

    public function __construct(
        OnlineCategoryRepository $onlineCateRepository,
        OnlineTagRepository $onlineTagRepository
    ) {
        $this->onlineCategoryRepository = $onlineCateRepository;
        $this->onlineTagRepository = $onlineTagRepository;
    }

    public function onlineCategoryPagination(Request $request)
    {
        $onlineTags = null;
        if ($request->has('page')) {
            $limit = $request->has('per_page1') ? $request->query('per_page1') : 1000;
            $onlineTags = $this->onlineCategoryRepository->paginate($limit);
        } else {
            $onlineTags = $this->onlineCategoryRepository->all();
        }

        return OnlineCategoryResource::collection($onlineTags);
    }

    public function onlineTagPagination(Request $request)
    {
        $onlineTags = null;
        if ($request->has('page')) {
            $limit = $request->has('per_page1') ? $request->query('per_page1') : 1000;
            $onlineTags = $this->onlineTagRepository->paginate($limit);
        } else {
            $onlineTags = $this->onlineTagRepository->all();
        }

        return OnlineTagResource::collection($onlineTags);
    }
}
