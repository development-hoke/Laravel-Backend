<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Criteria\Staff\AdminSearchCriteria;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Staff\IndexRequest;
use App\Http\Resources\PublicStaff as PublicStaffResource;
use App\Repositories\StaffRepository;

class StaffController extends Controller
{
    private $staffRepository;

    public function __construct(StaffRepository $staffRepository)
    {
        $this->staffRepository = $staffRepository;
    }

    /**
     * @param IndexRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $this->staffRepository->pushCriteria(new AdminSearchCriteria($request->validated()));

        $staffs = $this->staffRepository->all();

        return PublicStaffResource::collection($staffs);
    }
}
