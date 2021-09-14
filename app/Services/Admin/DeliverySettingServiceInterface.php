<?php

namespace App\Services\Admin;

interface DeliverySettingServiceInterface
{
    /**
     * Update
     *
     * @param array $request
     * @param int $deliverySettingId
     *
     * @return \App\Models\DeliverySetting
     */
    public function update(array $params, int $deliverySettingId);
}
