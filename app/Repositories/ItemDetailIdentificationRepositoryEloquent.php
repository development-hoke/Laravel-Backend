<?php

namespace App\Repositories;

use App\Exceptions\FatalException;
use App\Exceptions\InvalidArgumentValueException;
use App\Models\ItemDetailIdentification;
use App\Repositories\Traits\PaginateWithDistinctTrait;
use Illuminate\Support\Facades\DB;

/**
 * Class ItemDetailIdentificationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 *
 * @SuppressWarnings(PHPMD.LongClassName)
 */
class ItemDetailIdentificationRepositoryEloquent extends BaseRepositoryEloquent implements ItemDetailIdentificationRepository
{
    use PaginateWithDistinctTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ItemDetailIdentification::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

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
    public function findSecurableLots(int $itemDetailId, int $requestedStock, ?bool $isReservation = false)
    {
        $targetColumn = $this->getSecuringTargetColumn($isReservation, false);

        $models = $this->scopeQuery(function ($query) use ($targetColumn) {
            return $query->where($targetColumn, '>', 0)->orderBy('arrival_date');
        })->findWhere(['item_detail_id' => $itemDetailId]);

        $this->resetModel();
        $this->resetScope();

        $lots = [];
        $stock = 0;

        foreach ($models as $model) {
            $lots[] = $model;
            $stock += $model->{$targetColumn};

            if ($stock >= $requestedStock) {
                return (new ItemDetailIdentification())->newCollection($lots);
            }
        }

        throw new InvalidArgumentValueException('error.not_exists_enouch_stock');
    }

    /**
     * @param int $id
     * @param int $count
     *
     * @return void
     */
    public function addEcStock(int $id, int $count)
    {
        if ($count === 0) {
            return;
        }

        $updated = $this->model->where('id', $id)->update([
            'ec_stock' => DB::raw("ec_stock + {$count}"),
        ]);

        if (!$updated) {
            throw new FatalException(error_format('error.failed_to_update_db', ['method', __METHOD__, 'id' => $id, 'count' => $count]));
        }

        $this->resetModel();
    }

    /**
     * @param int $id
     * @param int $count
     *
     * @return void
     */
    public function addReservableStock(int $id, int $count)
    {
        if ($count === 0) {
            return;
        }

        $updated = $this->model->where('id', $id)->update([
            'reservable_stock' => DB::raw("reservable_stock + {$count}"),
        ]);

        if (!$updated) {
            throw new FatalException(error_format('error.failed_to_update_db', ['method', __METHOD__, 'id' => $id, 'count' => $count]));
        }

        $this->resetModel();
    }

    /**
     * 在庫確保のターゲットとなるカラムを取得する
     *
     * @param bool $isReservation
     * @param bool|null $withTable
     *
     * @return string
     */
    public static function getSecuringTargetColumn(bool $isReservation, ?bool $withTable = false)
    {
        $column = ($isReservation ? 'reservable_stock' : 'ec_stock');

        if ($withTable) {
            $column = 'item_detail_identifications.' . $column;
        }

        return $column;
    }

    /**
     *  在庫取り込み時に一旦すべての在庫情報をクリア
     */
    public function clearStock()
    {
        // 全件更新
        $this->model->whereNotNull('created_at')->update([
            'ec_stock' => 0,
            'store_stock' => 0,
            'reservable_stock' => 0,
        ]);
    }

    /**
     * 予約在庫切れ
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function findLowInventory()
    {
        $model = $this->model->join('item_details', 'item_details.id', '=', 'item_detail_identifications.item_detail_id')
                       ->join('items', function ($q) {
                           $q->on('items.id', '=', 'item_details.item_id')
                           ->where('items.status', \App\Enums\Common\Status::Published)
                           ->where('items.sales_status', \App\Enums\Item\SalesStatus::InStoreNow);
                       })
                       ->join('item_reserves', 'item_reserves.item_id', '=', 'items.id')
                       ->whereNotNull('item_reserves.out_of_stock_threshold')
                       ->whereNotNull('item_detail_identifications.reservable_stock')
                       ->whereRaw('item_reserves.out_of_stock_threshold >= item_detail_identifications.reservable_stock')
                       ->select(['item_detail_identifications.*']);

        $this->resetModel();

        return $model;
    }
}
