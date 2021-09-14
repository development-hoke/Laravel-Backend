<?php

namespace App\Http\Controllers\Api\V1\Front\Content;

use App\Criteria\Plan\FrontPlanCriteria;
use App\Http\Controllers\Controller;
use App\Http\Resources\Plan as PlanResource;
use App\Repositories\PlanRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PlanController extends Controller
{
    protected $planRepository;

    public function __construct(
        PlanRepository $planRepository
    ) {
        $this->planRepository = $planRepository;
    }

    /**
     * @param Request $request
     *
     * @return []
     */
    public function getPlans(Request $request)
    {
        $this->planRepository->pushCriteria(new FrontPlanCriteria($request));

        $plans = $this->planRepository
            ->orderBy('period_from', 'desc')
            ->paginate(
                $request->per_page ?? config('repository.pagination.plan')
            );

        return PlanResource::collection($plans);
    }

    /**
     * @param Request $request
     *
     * @return []
     */
    public function getPlan(Request $request, $slug)
    {
        try {
            $plan = $this->planRepository
                ->with([
                    'items',
                    'items.itemImages',
                ])
                ->findByField('slug', $slug)
                ->whereNull('deleted_at')
                ->first();

            return new PlanResource($plan);
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found'), $e);
        }
    }
}
