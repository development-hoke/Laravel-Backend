<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ItemDetailIdentificationRepository.
 *
 * @package namespace App\Repositories;
 */
interface ItemDetailIdentificationRepository extends RepositoryInterface
{
    /**
     * 確保可能なデータをロットの若い順に取得する
     *
     * @param int $itemDetailId
     * @param int $requestedStock
     * @param bool|null $isReservation
     *
     * @return \Illuminate\Database\Eloquent\Collection
     *
     * @throws InvalidArgumentValueException
     */
    public function findSecurableLots(int $itemDetailId, int $requestedStock, ?bool $isReservation = false);

    /**
     * @param int $id
     * @param int $count
     *
     * @return void
     */
    public function addEcStock(int $id, int $count);

    /**
     * @param int $id
     * @param int $count
     *
     * @return void
     */
    public function addReservableStock(int $id, int $count);

    /**
     * 在庫確保のターゲットとなるカラムを取得する
     *
     * @param bool $isReservation
     * @param bool|null $withTable
     *
     * @return string
     */
    public static function getSecuringTargetColumn(bool $isReservation, ?bool $withTable = false);

    /**
     * @param string $countColumn
     * @param int $limit
     * @param array $columns
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateWithDistinct(string $countColumn, $limit = null, $columns = ['*']);

    /**
     *  在庫取り込み時に一旦すべての在庫情報をクリア
     */
    public function clearStock();
}
