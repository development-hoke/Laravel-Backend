<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\DeliverySetting\ShowRequest;
use App\Http\Requests\Api\V1\Admin\DeliverySetting\UpdateRequest;
use App\Http\Resources\DeliverySetting as DeliverySettingResource;
use App\Repositories\DeliverySettingRepository;
use App\Services\Admin\DeliverySettingServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DeliverySettingController extends ApiAdminController
{
    /**
     * @var DeliverySettingRepository
     */
    private $repository;

    /**
     * @var DeliverySettingServiceInterface
     */
    private $service;

    /**
     * @param DeliverySettingRepository $repository
     * @param DeliverySettingServiceInterface $service
     */
    public function __construct(DeliverySettingRepository $repository, DeliverySettingServiceInterface $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * @param ShowRequest $request
     * @param int $id
     *
     * @return DeliverySettingResource
     */
    public function show(ShowRequest $request, int $id): DeliverySettingResource
    {
        try {
            return new DeliverySettingResource($this->repository->find($id));
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('id')), $e);
        }
    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     *
     * @return DeliverySettingResource
     */
    public function update(UpdateRequest $request, int $id): DeliverySettingResource
    {
        $params = $request->validated();

        return new DeliverySettingResource($this->service->update($params, $id));
    }
}
