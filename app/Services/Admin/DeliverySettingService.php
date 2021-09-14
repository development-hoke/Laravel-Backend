<?php

namespace App\Services\Admin;

use Exception;
use Illuminate\Support\Facades\DB;

class DeliverySettingService extends Service implements DeliverySettingServiceInterface
{
    /**
     * @var \App\Repositories\DeliverySettingRepository
     */
    private $deliverySettingRepository;

    /**
     * @param \App\Repositories\DeliverySettingRepository $deliverySettingRepository
     */
    public function __construct(
        \App\Repositories\DeliverySettingRepository $deliverySettingRepository
    ) {
        $this->deliverySettingRepository = $deliverySettingRepository;
    }

    /**
     * Update
     *
     * @param array $request
     * @param int $deliverySettingId
     *
     * @return \App\Models\DeliverySetting
     */
    public function update(array $params, int $deliverySettingId)
    {
        try {
            DB::beginTransaction();

            $this->deliverySettingRepository->update($params, $deliverySettingId);
            $deliverySetting = $this->deliverySettingRepository->find($deliverySettingId);

            DB::commit();

            return $deliverySetting;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
