<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\PastItem\IndexRequest;
use App\Http\Resources\PastItem as PastItemResource;
use App\Services\Admin\PastItemServiceInterface as PastItemService;

class PastItemController extends ApiAdminController
{
    /**
     * @var PastItemService
     */
    private $pastItemService;

    /**
     * @param PastItemService $pastItemService
     */
    public function __construct(
        PastItemService $pastItemService
    ) {
        $this->pastItemService = $pastItemService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest
     *
     * @return ResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $pastItems = $this->pastItemService->search($request->validated());

        return PastItemResource::collection($pastItems);
    }
}
