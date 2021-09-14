<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface StaffRepository.
 *
 * @package namespace App\Repositories;
 */
interface StaffRepository extends RepositoryInterface
{
    /**
     * Update or Create an entity in repository
     *
     * @throws ValidatorException
     *
     * @param array $attributes
     * @param int $id
     *
     * @return mixed
     */
    public function safeUpdateOrCreate(array $attributes, int $id);
}
