<?php

namespace App\Domain;

use App\Domain\Exceptions\StockShortageException;
use App\Exceptions\FatalException;
use App\Exceptions\InvalidInputException;
use App\HttpCommunication\Shohin\ItemInterface as ShohinHttpCommunication;
use App\Repositories\ClosedMarketRepository;
use App\Repositories\ItemDetailIdentificationRepository;
use App\Repositories\ItemDetailRepository;

class Stock implements StockInterface
{
    private $closedMarketStockRelations = [
        'assignedClosedMarketCartItems',
    ];

    /**
     * @var ShohinHttpCommunication
     */
    private $shohinHttpCommunication;

    /**
     * @var ClosedMarketRepository
     */
    private $closedMarketRepository;

    /**
     * @var ItemDetailRepository
     */
    private $itemDetailRepository;

    /**
     * @var ItemDetailIdentificationRepository
     */
    private $itemDetailIdRepository;

    public function __construct(
        ShohinHttpCommunication $shohinHttpCommunication,
        ClosedMarketRepository $closedMarketRepository,
        ItemDetailRepository $itemDetailRepository,
        ItemDetailIdentificationRepository $itemDetailIdRepository
    ) {
        $this->shohinHttpCommunication = $shohinHttpCommunication;
        $this->closedMarketRepository = $closedMarketRepository;
        $this->itemDetailRepository = $itemDetailRepository;
        $this->itemDetailIdRepository = $itemDetailIdRepository;
    }

    /**
     * EC在庫の確認 (闇市除外)
     *
     * @param int $itemDetailId
     * @param int $requestedStock
     * @param array $targetIds
     * @param bool $lockItemDetail
     *
     * @return bool
     */
    private function hasEcStock(int $itemDetailId, int $requestedStock, array $targetIds = [], bool $lockItemDetail = false)
    {
        // 在庫に対して実際に減算処理を行わないが在庫確保が必要な処理は、
        // item_detailへの排他ロックを掛けてから、在庫確認するようにする。
        if ($lockItemDetail) {
            $this->itemDetailRepository->scopeQuery(function ($query) {
                return $query->lockForUpdate();
            })->find($itemDetailId);
        }

        $ecStockRelations = [
            'itemDetailIdentifications' => function ($query) use ($targetIds) {
                if (empty($targetIds)) {
                    return $query;
                }

                return $query->whereIn('item_detail_identifications.id', $targetIds);
            },
            'enableClosedMarkets',
            'assignedNormalOrderCartItems',
        ];

        $itemDetail = $this->itemDetailRepository->with($ecStockRelations)->find($itemDetailId);

        return $itemDetail->secuarable_ec_stock >= $requestedStock;
    }

    /**
     * 闇市在庫確認
     *
     * @param int $closedMarketId
     * @param int $requestedStock
     *
     * @return bool
     */
    private function hasClosedMarketStock(int $closedMarketId, int $requestedStock)
    {
        $closedMarket = $this->closedMarketRepository->with(
            $this->closedMarketStockRelations
        )->find($closedMarketId);

        return $closedMarket->hasStock($requestedStock);
    }

    /**
     * 予約在庫確認
     *
     * @param int $itemDetailId
     * @param int $requestedStock
     * @param array $targetIds
     * @param bool $lockItemDetail
     *
     * @return bool
     */
    private function hasReservableStock(int $itemDetailId, int $requestedStock, array $targetIds = [], bool $lockItemDetail = false)
    {
        // 在庫に対して実際に減算処理を行わないが在庫確保が必要な処理は、
        // item_detailへの排他ロックを掛けてから、在庫確認するようにする。
        if ($lockItemDetail) {
            $this->itemDetailRepository->scopeQuery(function ($query) {
                return $query->lockForUpdate();
            })->find($itemDetailId);
        }

        $reservableStockRelations = [
            'itemDetailIdentifications' => function ($query) use ($targetIds) {
                if (empty($targetIds)) {
                    return $query;
                }

                return $query->whereIn('item_detail_identifications.id', $targetIds);
            },
            'item.appliedReservation',
            'assignedReserveOrderCartItems',
        ];

        $itemDetail = $this->itemDetailRepository->with(
            $reservableStockRelations
        )->find($itemDetailId);

        return $itemDetail->secuarable_reservable_stock >= $requestedStock;
    }

    /**
     * @param bool $isReservation
     * @param int $itemDetailId
     * @param int $requestedStock
     * @param array $targetIds
     *
     * @return bool
     */
    private function hasEcOrReservableStock(bool $isReservation, int $itemDetailId, int $requestedStock, array $targetIds = [])
    {
        if ($isReservation) {
            return $this->hasReservableStock($itemDetailId, $requestedStock, $targetIds);
        }

        return $this->hasEcStock($itemDetailId, $requestedStock, $targetIds);
    }

    /**
     * 確保可能なデータをロットの若い順に取得し、在庫にロックを掛ける
     *
     * @param int $itemDetailId
     * @param int $requestedStock
     * @param bool $isReservation
     * @param bool $isAlreadySecured
     *
     * @return \Illuminate\Database\Eloquent\Collection
     *
     * @throws \App\Domain\Exceptions\StockShortageException
     */
    private function findSecurableItemDetailIdentifications(int $itemDetailId, int $requestedStock, bool $isReservation, bool $isAlreadySecured)
    {
        try {
            $validationCount = $isAlreadySecured ? 0 : $requestedStock;

            $itemDetailIdents = $this->itemDetailIdRepository->findSecurableLots($itemDetailId, $requestedStock, $isReservation);

            $targetIds = $itemDetailIdents->pluck('id')->toArray();

            $itemDetailIdents = $this->itemDetailIdRepository->scopeQuery(function ($query) {
                return $query->lockForUpdate();
            })->findWhereIn('id', $targetIds);

            if ($this->hasEcOrReservableStock($isReservation, $itemDetailId, $validationCount, $targetIds) === false) {
                throw new StockShortageException(error_format('error.not_exists_enouch_stock', [
                    'is_reservation' => $isReservation,
                    'item_detail_id' => $itemDetailId,
                    'reqested_stock' => $requestedStock,
                    'target_ids' => $targetIds,
                ]));
            }

            return $itemDetailIdents;
        } catch (\App\Exceptions\InvalidArgumentValueException $e) {
            throw new StockShortageException(error_format('error.not_exists_enouch_stock', [
                'item_detail_id' => $itemDetailId,
                'requested_stock' => $requestedStock,
            ]), null, $e);
        }
    }

    /**
     * JANを指定して商品基幹より在庫情報を取得する
     *
     * @param string $janCode
     *
     * @return \App\Entities\Collection
     */
    private function fetchShohinStocks(string $janCode)
    {
        $response = $this->shohinHttpCommunication->fetchStocks(['item_jan_code' => $janCode]);

        $data = $response->getBody();

        return \App\Entities\Ymdy\Shohin\Item::collection($data['data']);
    }

    /**
     * お取り寄せ可能かを商品基幹のデータで判定する
     *
     * @param int $itemDetailId
     *
     * @return bool
     */
    public function isBackOrderbleSecuredItem(int $itemDetailId)
    {
        try {
            $items = $this->findBackOrderbleSecuredItems($itemDetailId);

            return $items->isNotEmpty();
        } catch (StockShortageException $e) {
            return false;
        }
    }

    /**
     * お取り寄せに指定するJANに紐づく商品データを取得する
     *
     * @param int $itemDetailId
     *
     * @return \App\Entities\Collection|\App\Entities\Ymdy\Ec\SecuredItem[]
     */
    public function findBackOrderbleSecuredItems(int $itemDetailId)
    {
        $itemDetail = $this->itemDetailRepository->find($itemDetailId);

        $itemDetail->load(['itemDetailIdentifications' => function ($query) {
            return $query->orderBy('arrival_date');
        }]);

        $threshold = \App\Domain\Utils\Stock::computeBackOrderbleStockThreshold($itemDetail);
        $candidates = [];

        foreach ($itemDetail->itemDetailIdentifications as $itemDetailIdent) {
            $targetjanCode = $itemDetailIdent->jan_code;

            $items = $this->fetchShohinStocks($targetjanCode);

            foreach ($items as $item) {
                if ((int) $item->shop_id === config('constants.store.ec_store_id')) {
                    continue;
                }

                if ($item->code2241 !== $targetjanCode) {
                    continue;
                }

                if (\App\Domain\Utils\Stock::isStoreStock($item)) {
                    $candidates[] = $itemDetailIdent;
                }

                if (count($candidates) >= $threshold) {
                    $assigned = collect($candidates)->first();

                    return \App\Entities\Ymdy\Ec\SecuredItem::collection([
                        [
                            'id' => $assigned->id,
                            'secured_num' => 1,
                            'item_detail_identification' => $assigned,
                        ],
                    ]);
                }
            }
        }

        throw new StockShortageException(error_format('error.not_exists_enouch_stock', [
            'item_detail_id' => $itemDetailId,
        ]));
    }

    /**
     * SKUを指定して在庫を確保する
     *
     * @param int $itemDetailId
     * @param int $requestedStock
     * @param array $options
     *
     * options:
     * [closed_market_id]: 闇市ID。指定した場合、closed_markets.stockも更新する
     * [is_reservation]: 予約在庫対象フラグ
     * [is_aleady_secured]: 在庫確保済みフラグ。カート投入済みの商品など在庫確保済みの商品の在庫を実際に減算する場合に指定する。
     *
     * @return \App\Entities\Collection|\App\Entities\Ymdy\Ec\SecuredItem[]
     *
     * @throws \App\Domain\Exceptions\StockShortageException
     */
    public function secureStock(int $itemDetailId, int $requestedStock, array $options = [])
    {
        $closedMarketId = $options['closed_market_id'] ?? null;
        $isReservation = $options['is_reservation'] ?? false;
        $isAlreadySecured = $options['is_aleady_secured'] ?? false;
        $targetColumn = $this->itemDetailIdRepository->getSecuringTargetColumn($isReservation);

        $securedNum = 0;

        // 在庫確保するJANを確定する
        $lots = $this->findSecurableItemDetailIdentifications($itemDetailId, $requestedStock, $isReservation, $isAlreadySecured);

        $secured = \App\Entities\Ymdy\Ec\SecuredItem::collection([]);

        foreach ($lots as $lot) {
            $securingNum = min($lot->{$targetColumn}, $requestedStock);

            $securedNum += $securingNum;

            $isReservation
                ? $this->itemDetailIdRepository->addReservableStock($lot->id, -$securingNum)
                : $this->itemDetailIdRepository->addEcStock($lot->id, -$securingNum);

            $lot = $this->itemDetailIdRepository->find($lot->id);

            $secured->add(new \App\Entities\Ymdy\Ec\SecuredItem([
                'id' => $lot->id,
                'secured_num' => $securingNum,
                'item_detail_identification' => $lot,
            ]));
        }

        if ($securedNum < $requestedStock) {
            throw new StockShortageException(error_format('error.not_exists_enouch_stock', [
                'item_detail_id' => $itemDetailId,
                'reqested_stock' => $requestedStock,
            ]));
        }

        if (isset($closedMarketId)) {
            $this->secureClosedMarketStock($closedMarketId, $requestedStock);
        }

        return $secured;
    }

    /**
     * 闇市から在庫を確保
     *
     * @param int $closedMarketId
     * @param int $requestedStock
     *
     * @return \App\Models\ClosedMarket
     */
    private function secureClosedMarketStock(int $closedMarketId, int $requestedStock)
    {
        $closedMarket = $this->closedMarketRepository->with(
            $this->closedMarketStockRelations
        )->scopeQuery(function ($query) {
            return $query->lockForUpdate();
        })->find($closedMarketId);

        if ($closedMarket->hasStock($requestedStock)) {
            throw new StockShortageException(error_format('error.not_exists_enouch_stock', [
                'closed_market_id' => $closedMarketId,
                'reqested_stock' => $requestedStock,
            ]));
        }

        $closedMarket = $this->closedMarketRepository->addStock($closedMarket->id, -$requestedStock);

        return $closedMarket;
    }

    /**
     * item_detail_identifications.idを指定してEC在庫を加算する
     * NOTE: 減算する処理には対応していない
     *
     * @param int $identId
     * @param int $requestedStock
     *
     * @return void
     */
    public function addEcStock(int $identId, int $requestedStock)
    {
        if ($requestedStock < 0) {
            throw new FatalException(__('error.invalid_value', ['attribute' => 'requestedStock', 'min' => 0]));
        }

        $this->itemDetailIdRepository->addEcStock($identId, $requestedStock);
    }

    /**
     * item_detail_identifications.idを指定してEC在庫を確保する
     *
     * @param int $identId
     * @param int $requestedStock
     *
     * @return void
     */
    public function secureEcStock(int $identId, int $requestedStock)
    {
        $itemDetailIdent = $this->itemDetailIdRepository->scopeQuery(function ($query) {
            return $query->lockForUpdate();
        })->find($identId);

        if ($itemDetailIdent->ec_stock < $requestedStock) {
            throw new StockShortageException(error_format('error.not_exists_enouch_stock', [
                'item_detail_id' => $identId,
                'requested_stock' => $requestedStock,
            ]));
        }

        if (!$this->hasEcStock($itemDetailIdent->item_detail_id, $requestedStock)) {
            throw new StockShortageException(error_format('error.not_exists_enouch_stock', [
                'item_detail_id' => $identId,
                'requested_stock' => $requestedStock,
            ]));
        }

        $this->itemDetailIdRepository->addEcStock($identId, -$requestedStock);
    }

    /**
     * item_detail_identifications.idを指定して在庫を加算する
     * NOTE: 減算する処理には対応していない
     *
     * @param int $identId
     * @param int $requestedStock
     *
     * @return void
     */
    public function addReservableStock(int $identId, int $requestedStock)
    {
        if ($requestedStock < 0) {
            throw new FatalException(__('error.invalid_value', ['attribute' => 'requestedStock', 'min' => 0]));
        }

        $this->itemDetailIdRepository->addReservableStock($identId, $requestedStock);
    }

    /**
     * item_detail_identifications.idを指定して予約在庫を確保する
     *
     * @param int $identId
     * @param int $requestedStock
     *
     * @return void
     */
    public function secureReservableStock(int $identId, int $requestedStock)
    {
        $itemDetailIdent = $this->itemDetailIdRepository->scopeQuery(function ($query) {
            return $query->lockForUpdate();
        })->find($identId);

        if ($itemDetailIdent->reservable_stock < $requestedStock) {
            throw new StockShortageException(error_format('error.not_exists_enouch_stock', [
                'item_detail_id' => $identId,
                'requested_stock' => $requestedStock,
            ]));
        }

        if (!$this->hasReservableStock($itemDetailIdent->item_detail_id, $requestedStock)) {
            throw new StockShortageException(error_format('error.not_exists_enouch_stock', [
                'item_detail_id' => $identId,
                'requested_stock' => $requestedStock,
            ]));
        }

        $this->itemDetailIdRepository->addReservableStock($identId, -$requestedStock);
    }

    /**
     * 在庫確認をする
     *
     * @param \App\Models\Cart $cart
     * @param bool|null $isAlreadyAdded
     *
     * @return bool
     */
    public function hasStockForCart(\App\Models\Cart $cart, ?bool $isAlreadyAdded = false)
    {
        $cart->assginOrderTypeToCartItems();

        foreach ($cart->getSecuredCartItems() as $cartItem) {
            if (!$this->hasStockForCartItem($cartItem, $cart->order_type, $isAlreadyAdded)) {
                return false;
            }
        }

        return true;
    }

    /**
     * item_detailへの排他ロックと、在庫確認をする
     *
     * @param \App\Models\CartItem $cartItem
     * @param int $orderType
     *
     * @return bool
     */
    public function lockAndValidateCartItemCount(\App\Models\CartItem $cartItem, int $orderType)
    {
        $hasStock = $this->hasStockForCartItem($cartItem, $orderType, false, true);

        return $hasStock;
    }

    /**
     * カート投入、注文時の在庫確認
     *
     * @param \App\Models\CartItem $cartItems
     * @param int $orderType
     * @param bool|null $isAlreadyAdded
     *
     * @return bool
     *
     * @throws InvalidInputException
     */
    private function hasStockForCartItem($cartItem, int $orderType, ?bool $isAlreadyAdded = false, bool $lockItemDetail = false)
    {
        $requestCount = $isAlreadyAdded ? 0 : ($cartItem['count'] ?? 1);

        $itemDetail = $cartItem->itemDetail;

        if (!$itemDetail) {
            throw new FatalException(__('error.no_item_details'));
        }

        if ($cartItem['is_closed_market']) {
            return $this->hasClosedMarketStock($cartItem['closed_market_id'], $requestCount);
        }

        switch ((int) $orderType) {
            case \App\Enums\Order\OrderType::Normal:
                return $this->hasEcStock($itemDetail->id, $requestCount, [], $lockItemDetail);

            case \App\Enums\Order\OrderType::Reserve:
                return $this->hasReservableStock($itemDetail->id, $requestCount, [], $lockItemDetail);

            case \App\Enums\Order\OrderType::BackOrder:
                return $this->isBackOrderbleSecuredItem($itemDetail->id);

            default:
                throw new FatalException(__('error.invalid_status'));
        }
    }

    /**
     * item_detailへの排他ロックと、EC在庫の確認をする
     *
     * @param int $itemDetailId
     * @param int $requestCount
     *
     * @return bool
     */
    public function lockAndValidateEcStock(int $itemDetailId, int $requestCount)
    {
        return $this->hasEcStock($itemDetailId, $requestCount, [], true);
    }
}
