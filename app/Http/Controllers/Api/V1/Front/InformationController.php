<?php

namespace App\Http\Controllers\Api\V1\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\Information\FetchRecentRequest;
use App\Http\Resources\Information as InformationResource;
use App\Repositories\InformationRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InformationController extends Controller
{
    /**
     * @var InformationRepository
     */
    protected $infoRepository;

    private $infosPerPage;

    public function __construct(
        InformationRepository $infoRepository
    ) {
        $this->infoRepository = $infoRepository;
        $this->infosPerPage = 24;
    }

    public function index(Request $request, $place = null)
    {
        $params = $request->all();
        $this->infoRepository->pushCriteria(new \App\Criteria\Information\FrontInformationCriteria($place));

        $informations = $this->infoRepository->paginate(
            $params['per_page'] ?? $this->infosPerPage
        );

        return InformationResource::collection($informations);
    }

    /**
     * @param int $id
     *
     * @return InformationResource
     */
    public function show(int $id): InformationResource
    {
        try {
            $information = $this->infoRepository->find($id);

            return new InformationResource($information);
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('id')), $e);
        }
    }

    /**
     * @param Request $request
     *
     * @return InformationResource
     */
    public function getTop(Request $request)
    {
        $informations = $this->infoRepository->getTop();

        return InformationResource::collection($informations);
    }

    /**
     * @param FetchRecentRequest $request
     *
     * @return InformationResource
     */
    public function getRecent(FetchRecentRequest $request)
    {
        $params = $request->validated();

        $informations = $this->infoRepository->getRecent($params);

        return InformationResource::collection($informations);
    }
}
