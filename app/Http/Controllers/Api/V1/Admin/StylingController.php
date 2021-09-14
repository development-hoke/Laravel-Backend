<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Styling\IndexRequest;
use App\Http\Resources\Styling as StylingResource;
use App\Services\Admin\StylingServiceInterface as StylingService;

class StylingController extends Controller
{
    /**
     * @var StylingService
     */
    private $stylingService;

    /**
     * @param StylingService $stylingService
     */
    public function __construct(StylingService $stylingService)
    {
        $this->stylingService = $stylingService;
    }

    /**
     * @param IndexRequest $request
     *
     * @return array
     */
    public function index(IndexRequest $request)
    {
        $stylings = $this->stylingService->search($request->validated());

        $stylings->setPath($request->url());
        $stylings->appends($request->query());

        return StylingResource::collection($stylings);
    }
}
