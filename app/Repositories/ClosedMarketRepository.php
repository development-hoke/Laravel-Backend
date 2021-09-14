<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ClosedMarketRepository.
 *
 * @package namespace App\Repositories;
 */
interface ClosedMarketRepository extends RepositoryInterface
{
    /**
     * @param int $id
     * @param int $count
     *
     * @return void
     */
    public function addStock(int $id, int $count);
}
