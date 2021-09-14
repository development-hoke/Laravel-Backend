<?php

namespace App\Http\Controllers\Api\V1\Front\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\Help\RateRequest;
use App\Http\Resources\Help as HelpResource;
use App\Http\Resources\HelpCategory as HelpCategoryResource;
use App\Repositories\HelpCategoryRepository;
use App\Repositories\HelpRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HelpController extends Controller
{
    /**
     * @var HelpRepository
     */
    protected $helpRepository;

    /**
     * @var HelpCategoryRepository
     */
    protected $helpCategoryRepository;

    private $helpsPerPage;

    public function __construct(
        HelpRepository $helpRepository,
        HelpCategoryRepository $helpCategoryRepository
    ) {
        $this->helpRepository = $helpRepository;
        $this->helpCategoryRepository = $helpCategoryRepository;
        $this->helpsPerPage = 15;
    }

    /**
     * @param Request $request
     *
     * @return []
     */
    public function index(Request $request)
    {
        $this->helpRepository->pushCriteria(new \App\Criteria\Help\FrontHelpCriteria($request));

        $helps = $this->helpRepository->orderBy('sort')->paginate(
            $request->per_page ?? $this->helpsPerPage
        );

        return HelpResource::collection($helps);
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return HelpResource
     */
    public function show(Request $request, int $id)
    {
        try {
            $help = $this->repository->published()->with([
                'helpCategories',
            ])->findOrFail($id);

            $related = $this->helpRepository->pushCriteria(new \App\Criteria\Help\FrontRelatedHelpCriteria($id))
                ->all();

            return response()->json([
                'help' => new HelpResource($help),
                'related' => HelpResource::collection($related),
            ]);
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('id')), $e);
        }
    }

    /**
     * @param Request $request
     *
     * @return []
     */
    public function rate(RateRequest $request, $id)
    {
        try {
            $params = $request->validated();
            $this->helpRepository->update([
                $params['rate'] => \DB::raw("{$params['rate']} + 1"),
            ], $id);
            $help = $this->helpRepository->find($id);

            return new HelpResource($help);
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('id')), $e);
        }
    }

    public function getCategories()
    {
        $categories = $this->helpCategoryRepository->all();

        return HelpCategoryResource::collection($categories);
    }
}
