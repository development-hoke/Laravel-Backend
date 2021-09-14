<?php

namespace App\Domain;

use App\Domain\Utils\Item as ItemUtil;
use App\Domain\Utils\ItemPrice as ItemPriceUtil;
use App\Domain\Utils\Member as MemberUtil;
use App\Models\Event;
use App\Models\EventUser;
use App\Utils\Arr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

/**
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ItemPrice implements ItemPriceInterface
{
    /**
     * 販売価格の代入をする
     *
     * @param \Illuminate\Database\Eloquent\Collection|\App\Models\Item $item
     * @param array|null $member
     * @param string|null $orderedDate
     *
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Item
     */
    public function fillDisplayedSalePrice($items, ?array $member = null, ?string $orderedDate = null)
    {
        $isSingle = $items instanceof \App\Models\Item;
        $items = $isSingle ? $items->newCollection([$items]) : $items;

        $this->loadAvailableEventRelation($items, $member, $orderedDate);

        // 予約販売設定の割当。
        // NOTE: ここでは予約期間中の判定に、受注日ではなく常に現在日時を使用する。
        $items->load('appliedReservation');

        foreach ($items as $item) {
            $this->fillDisplayedDiscountInfo($item, $member);
        }

        return $isSingle ? $items->first() : $items;
    }

    /**
     * 利用可能なイベントのリレーションを読み込み
     *
     * @param \Illuminate\Database\Eloquent\Collection $item
     * @param array|null $member
     * @param string|null $orderedDate
     *
     * @return \Illuminate\Database\Eloquent\Collection $item
     */
    private function loadAvailableEventRelation(
        \Illuminate\Database\Eloquent\Collection $items,
        array $member = null,
        ?string $orderedDate = null
    ) {
        $items->load(['eventItems.event' => function ($query) use ($member, $orderedDate) {
            $query = $this->applyBaseEventConditions($query, $member, $orderedDate);

            return $query->where('events.sale_type', \App\Enums\Event\SaleType::Normal);
        }]);

        $items->each(function ($item) {
            $eventItems = $item->eventItems->filter(function ($eventItem) {
                return !empty($eventItem->event);
            });
            $item->setRelation('eventItems', $eventItems);
        });

        return $items;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param array|null $member
     * @param string|null $orderedDate
     *
     * @return \Illuminate\Database\Query\Builder
     */
    private function applyBaseEventConditions($query, ?array $member = null, ?string $orderedDate = null)
    {
        $baseDate = DB::raw(isset($orderedDate) ? "'$orderedDate'" : 'NOW()');

        if (empty($member)) {
            $query = $query->where('events.target_user_type', \App\Enums\Event\TargetUserType::All);
        } else {
            $query = $query->where(function ($query) use ($member) {
                $subQeury = \App\Models\EventUser::select('event_id')->where('member_id', $member['id']);

                return $query->whereIn('events.target_user_type', [\App\Enums\Event\TargetUserType::All, \App\Enums\Event\TargetUserType::MemberOnly])
                    ->orWhereIn('events.id', $subQeury);
            });
        }

        return $query
            ->whereBetween($baseDate, [DB::raw('events.period_from'), DB::raw('events.period_to')])
            ->where('events.published', \App\Enums\Common\Status::Published);
    }

    /**
     * 適用可能なイベントの割当
     * NOTE: イベントのリレーションを事前に読み込む
     *
     * @param \App\Models\Item $item
     * @param array $member
     *
     * @return \App\Models\Item
     */
    private function setApplicableEvent(\App\Models\Item $item)
    {
        $eventItem = $this->findApplicableEventItem($item);

        if ($eventItem === null) {
            return $item;
        }

        switch ((int) $eventItem->event->discount_type) {
            case \App\Enums\Event\DiscountType::Flat:
                $item->setRelation('applicableEvent', $eventItem->event);
                $item->applicable_event_discount_rate = $eventItem->event->discount_rate;

                break;
            case \App\Enums\Event\DiscountType::EachProduct:
                $item->setRelation('applicableEvent', $eventItem->event);
                $item->applicable_event_discount_rate = $eventItem->discount_rate;

                break;

            default:
                break;
        }

        return $item;
    }

    /**
     * @param mixed $query
     *
     * @return mixed
     */
    public static function applyAppliedReservationCondition($query)
    {
        return $query
            ->where('item_reserves.is_enable', \App\Enums\Common\Boolean::IsTrue)
            ->whereBetween(DB::raw('NOW()'), [DB::raw('item_reserves.period_from'), DB::raw('item_reserves.period_to')]);
    }

    /**
     * @param \App\Models\Item $item
     *
     * @return \App\Models\EventItem
     */
    private function findApplicableEventItem(\App\Models\Item $item)
    {
        if ($item->eventItems->isEmpty()) {
            return null;
        }

        $eventItems = $item->eventItems->sortByDesc(function ($eventItem) {
            return Carbon::parse($eventItem->event->period_from)->timestamp;
        });

        foreach ($eventItems as $eventItem) {
            if (!empty($eventItem->event)) {
                return $eventItem;
            }
        }

        return null;
    }

    /**
     * @param \App\Models\Item $item
     * @param array $member
     *
     * @return \App\Models\Item
     */
    private function fillDisplayedDiscountInfo(\App\Models\Item $item, array $member = null)
    {
        $rate = 0.0;
        $type = \App\Enums\Item\DiscountType::None;

        if ($this->isFixedMethodDisplayedDiscount($item)) {
            [$price, $type] = $this->computeFixedDisplayedDiscountInfo($item, $member);

            // 社員割引の場合定率値引になるため除外する
            if ($type !== \App\Enums\Item\DiscountType::Staff) {
                $item->displayed_discount_price = $price;
                $item->displayed_discount_type = $type;
                $item->displayed_sale_price = $item->retail_price - $price;

                return $item;
            }

            // 社員割引の場合、変数を再代入し定率値引と同様に扱う
            $type = \App\Enums\Item\DiscountType::Staff;
            $rate = ItemPriceUtil::getEmployeeDiscountRate($item->maker_product_number);
        } else {
            $this->setApplicableEvent($item, $member);

            [$rate, $type] = $this->computePercentileDisplayedDiscountInfo($item, $member);
        }

        $item->displayed_discount_rate = $rate;
        $item->displayed_discount_type = $type;
        $item->displayed_sale_price = ItemPriceUtil::calcDiscountedPriceByScalar($item->retail_price, $rate);

        return $item;
    }

    private function isFixedMethodDisplayedDiscount(\App\Models\Item $item)
    {
        return !empty($item->appliedReservation);
    }

    /**
     * 定額値引の割引情報を取得
     *
     * @param \App\Models\Item $item
     * @param array $member
     *
     * @return array [$discountedPrice, $appliedDiscountType]
     */
    private function computeFixedDisplayedDiscountInfo(\App\Models\Item $item, array $member = null)
    {
        [$discountPrice, $discountType] = $this->computeReservationDiscountInfo($item);

        $isStaff = !empty($member) && \App\Domain\Utils\Member::isStaffAccount($member);

        if (!$isStaff) {
            return [$discountPrice, $discountType];
        }

        $staffDiscountPrice = ItemPriceUtil::calculateEmployeeDiscount($item->retail_price, $item->maker_product_number);

        if ($staffDiscountPrice > $discountPrice) {
            return [$staffDiscountPrice, \App\Enums\Item\DiscountType::Staff];
        }

        return [$discountPrice, $discountType];
    }

    /**
     * 予約販売の割引情報を取得する。
     *
     * NOTE: 予約販売金額が上代より安くない場合、割引としては扱わない。
     *
     * @param \App\Models\Item $item
     *
     * @return void
     */
    private function computeReservationDiscountInfo(\App\Models\Item $item)
    {
        $discountPrice = $item->retail_price - $item->appliedReservation->reserve_price;

        $discountType = $discountPrice > 0
            ? \App\Enums\Item\DiscountType::Reservation
            : \App\Enums\Item\DiscountType::None;

        return [$discountPrice, $discountType];
    }

    /**
     * 定率値引の割引情報を取得
     * NOTE: getNonMemberSearchScopeQuery, getMemberSearchScopeQueryに一覧画面用の同一ロジックがあるため修正のときは注意
     *
     * @param \App\Models\Item $item
     * @param array $member (default: null)
     *
     * @return array [$discountedRate, $appliedDiscountType]
     */
    private function computePercentileDisplayedDiscountInfo(\App\Models\Item $item, array $member = null)
    {
        [$rate, $appliedDisplayedSaleType] = $this->computePercentileDisplayedDiscountInfoWithoutStaffDiscount($item, $member);

        $isStaff = !empty($member) && \App\Domain\Utils\Member::isStaffAccount($member);

        if (!$isStaff) {
            return [$rate, $appliedDisplayedSaleType];
        }

        $staffDiscountRate = ItemPriceUtil::getEmployeeDiscountRate($item->maker_product_number);

        if ($staffDiscountRate > $rate) {
            return [$staffDiscountRate, \App\Enums\Item\DiscountType::Staff];
        }

        return [$rate, $appliedDisplayedSaleType];
    }

    /**
     * 販売価格の計算（社員割引除外）
     *
     * @param \App\Models\Item $item
     * @param array $member (default: null)
     *
     * @return array [$rate, $type]
     */
    private function computePercentileDisplayedDiscountInfoWithoutStaffDiscount(\App\Models\Item $item, array $member = null)
    {
        if ($item->applicable_event_discount_rate !== null) {
            return [$item->applicable_event_discount_rate, \App\Enums\Item\DiscountType::Event];
        }

        if (!empty($member) && $item->is_member_discount) {
            return [$item->member_discount_rate, \App\Enums\Item\DiscountType::Member];
        }

        if ($item->discount_rate > 0) {
            return [$item->discount_rate, \App\Enums\Item\DiscountType::Normal];
        }

        return [0.0, \App\Enums\Item\DiscountType::None];
    }

    /**
     * 商品検索scopeQuery（非会員・ゲスト会員）
     *
     * @return \Closure
     */
    public function getNonMemberSearchScopeQuery()
    {
        return function ($query) {
            $query = $query->select(array_merge(
                [
                    'items.*',
                    DB::raw('0 as is_favorite'),
                    $this->getIsReservationSelectStatement(),
                ],
                $this->getNonMemberPriceSelectStatement()
            ));

            $query = $query->leftJoin('item_reserves', 'items.id', '=', 'item_reserves.item_id');

            $eventQuery = Event::select([
                'event_items.item_id',
                $this->getEventSaleDiscountRateSelectStatement(\App\Enums\Event\DiscountType::Flat),
            ])
                ->leftJoin('event_items', 'events.id', '=', 'event_items.event_id')
                ->where('events.target_user_type', \App\Enums\Event\TargetUserType::All)
                ->whereBetween(DB::raw('NOW()'), [DB::raw('events.period_from'), DB::raw('events.period_to')])
                ->where('events.sale_type', \App\Enums\Event\SaleType::Normal)
                ->where('events.published', \App\Enums\Common\Status::Published)
                ->groupBy(['event_items.item_id']);

            $query->leftJoinSub($eventQuery, 'applicable_events', function (JoinClause $join) {
                return $join->on('applicable_events.item_id', '=', 'items.id');
            });

            return $query;
        };
    }

    /**
     * 商品検索scopeQuery（会員）
     *
     * @param array $member
     * @param \App\Models\Order|null $order
     *
     * @return \Closure
     */
    public function getMemberSearchScopeQuery(array $member, ?\App\Models\Order $editingOrder = null)
    {
        return function ($query) use ($member, $editingOrder) {
            $isStaff = MemberUtil::isStaffAccount($member);
            $baseDatetime = empty($editingOrder) ? DB::raw('NOW()') : DB::raw("'{$editingOrder->order_date}'");

            $query = $query->select(array_merge(
                [
                    'items.*',
                    DB::raw('`item_favorites`.`id` is not null as is_favorite'),
                    $this->getIsReservationSelectStatement(),
                ],
                $this->getMemberPriceSelectStatement($isStaff)
            ))
                ->leftJoin('item_favorites', function (JoinClause $query) use ($member) {
                    return $query->on('items.id', '=', 'item_favorites.item_id')
                        ->where('item_favorites.member_id', $member['id']);
                });

            $query = $query->leftJoin('item_reserves', 'items.id', '=', 'item_reserves.item_id');

            $eventQuery = Event::select([
                'event_items.item_id',
                $this->getEventSaleDiscountRateSelectStatement(\App\Enums\Event\DiscountType::Flat),
            ])
                ->join('event_items', 'events.id', '=', 'event_items.event_id')
                ->where(function ($query) use ($member) {
                    $subQeury = EventUser::select('event_id')->where('member_id', $member['id']);

                    return $query->whereIn('events.target_user_type', [\App\Enums\Event\TargetUserType::All, \App\Enums\Event\TargetUserType::MemberOnly])
                        ->orWhereIn('events.id', $subQeury);
                })
                ->whereBetween($baseDatetime, [DB::raw('events.period_from'), DB::raw('events.period_to')])
                ->where('events.sale_type', \App\Enums\Event\SaleType::Normal)
                ->where('events.published', \App\Enums\Common\Status::Published)
                ->groupBy(['event_items.item_id']);

            $query = $query->leftJoinSub($eventQuery, 'applicable_events', function (JoinClause $join) {
                return $join->on('applicable_events.item_id', '=', 'items.id');
            });

            return $query;
        };
    }

    /**
     * @param int $eventDiscountType
     *
     * @return \Illuminate\Database\Query\Expression
     */
    private function getEventSaleDiscountRateSelectStatement(int $eventDiscountType)
    {
        return DB::raw("
            SUBSTRING_INDEX(
                GROUP_CONCAT((
                    CASE
                        WHEN events.discount_type = {$eventDiscountType} THEN
                            events.discount_rate
                        ELSE
                            event_items.discount_rate
                    END
                ) ORDER BY events.period_from DESC
            ), ',', 1) as discount_rate
        ");
    }

    /**
     * @param string $date
     *
     * @return \Illuminate\Database\Query\Expression
     */
    private function getIsReservationSelectStatement()
    {
        return DB::raw('
            @isReservation := IFNULL(item_reserves.is_enable AND (
                NOW() BETWEEN item_reserves.period_from AND item_reserves.period_to
            ), 0) as is_reservation
        ');
    }

    /**
     * 非会員（ゲスト会員含む）の表示価格のSELECT文
     *
     * @return \Illuminate\Database\Query\Expression[]
     */
    private function getNonMemberPriceSelectStatement()
    {
        return [
            DB::raw('(
                    CASE
                        WHEN @isReservation THEN
                            item_reserves.reserve_price
                        WHEN applicable_events.item_id IS NOT null THEN
                            items.retail_price - FLOOR(applicable_events.discount_rate * items.retail_price)
                        WHEN items.discount_rate > 0 THEN
                            items.retail_price - FLOOR(items.discount_rate * items.retail_price)
                        ELSE
                            items.retail_price
                    END
                ) as displayed_sale_price
            '),
            DB::raw('(
                    CASE
                        WHEN @isReservation THEN
                            '.\App\Enums\Item\DiscountType::Reservation.'
                        WHEN applicable_events.item_id IS NOT null THEN
                            '.\App\Enums\Item\DiscountType::Event.'
                        WHEN items.discount_rate > 0 THEN
                            '.\App\Enums\Item\DiscountType::Normal.'
                        ELSE
                            '.\App\Enums\Item\DiscountType::None.'
                    END
                ) as displayed_discount_type
            '),
        ];
    }

    /**
     * @param bool $isStaff
     *
     * @return \Illuminate\Database\Query\Expression[]
     */
    private function getMemberPriceSelectStatement(bool $isStaff)
    {
        $intermediate = $this->getIntermediateMemberPriceSelectStatement();

        if (!$isStaff) {
            return array_merge($intermediate, [
                DB::raw('@displayedSalePriceWithoutStaff as displayed_sale_price'),
                DB::raw('@displayedDiscountTypeWithoutStaff as displayed_discount_type'),
            ]);
        }

        $staffDiscountRateExpression = $this->getStaffDiscountRateExpression();

        return array_merge($intermediate, [
            DB::raw("@staffSalePrice := (items.retail_price - FLOOR({$staffDiscountRateExpression} * items.retail_price))"),
            DB::raw('IF(
                CAST(@displayedSalePriceWithoutStaff AS INT) > CAST(@staffSalePrice AS INT),
                @staffSalePrice,
                @displayedSalePriceWithoutStaff
            ) as displayed_sale_price'),
            DB::raw('IF(
                CAST(@displayedSalePriceWithoutStaff AS INT) > CAST(@staffSalePrice AS INT),
                '.\App\Enums\Item\DiscountType::Staff.',
                @displayedDiscountTypeWithoutStaff
            ) as displayed_discount_type'),
        ]);
    }

    /**
     * @return string
     */
    private function getStaffDiscountRateExpression()
    {
        $prefixes = ItemUtil::getOwnProductMakerProductNumberPrefixes();

        $conditions = array_map(function ($prefix) {
            return "items.maker_product_number like '{$prefix}%'";
        }, $prefixes);

        $staffDiscountRateExpression = sprintf(
            'IF(%s, %s, %s)',
            implode(' OR ', $conditions),
            ItemPriceUtil::getStaffDiscountRateOwnProduct(),
            ItemPriceUtil::getStaffDiscountRateOtherProduct(),
        );

        return $staffDiscountRateExpression;
    }

    /**
     * @return \Illuminate\Database\Query\Expression[]
     */
    private function getIntermediateMemberPriceSelectStatement()
    {
        return [
            DB::raw('
                @displayedSalePriceWithoutStaff := (
                    CASE
                        WHEN @isReservation THEN
                            item_reserves.reserve_price
                        WHEN applicable_events.item_id IS NOT null THEN
                            items.retail_price - FLOOR(applicable_events.discount_rate * items.retail_price)
                        WHEN items.is_member_discount = 1 THEN
                            items.retail_price - FLOOR(items.member_discount_rate * items.retail_price)
                        WHEN items.discount_rate > 0 THEN
                            items.retail_price - FLOOR(items.discount_rate * items.retail_price)
                        ELSE
                            items.retail_price
                    END
                )'),
            DB::raw('
                @displayedDiscountTypeWithoutStaff := (
                    CASE
                        WHEN @isReservation THEN
                            '.\App\Enums\Item\DiscountType::Reservation.'
                        WHEN applicable_events.item_id IS NOT null THEN
                            '.\App\Enums\Item\DiscountType::Event.'
                        WHEN items.is_member_discount = 1 AND items.member_discount_rate > 0 THEN
                            '.\App\Enums\Item\DiscountType::Member.'
                        WHEN items.discount_rate > 0 THEN
                            '.\App\Enums\Item\DiscountType::Normal.'
                        ELSE
                            '.\App\Enums\Item\DiscountType::None.'
                    END
                )
            '),
        ];
    }

    /**
     * 注文以前（カート投入後）の商品販売価格を代入する (新規注文用)
     *
     * @param \App\Models\Cart $cart
     * @param string $orderDate
     *
     * @return \App\Models\Cart
     */
    public function fillPriceBeforeOrderToCreateNewOrder(\App\Models\Cart $cart, $orderDate = null)
    {
        $member = $cart->is_guest ? null : $cart->member;

        $cart->cartItems->load('itemDetail.item');

        $targetItems = $cart->cartItems->map(function ($cartItem) {
            return $cartItem->itemDetail->item;
        });

        $this->fillDisplayedSalePrice($targetItems, $member);

        $orderingItems = $cart->cartItems->map(function ($cartItem) {
            return new \App\Entities\Ymdy\Ec\OrderingItem([
                'amount' => $cartItem->count,
                'id' => $cartItem->itemDetail->item_id,
            ]);
        });

        // 注文日時 or カートに入れる際の日時
        $orderDate = $orderDate ?? Carbon::now()->format('Y-m-d H:i:s');

        $this->fillPriceBeforeOrder($targetItems, $orderingItems, $orderDate, $member, 0);

        // 価格表示ロジック
        $targetItems->each(function ($item) {
            $item->can_display_original_price = \App\Domain\Utils\ItemPrice::canDisplayOriginalPrice($item);

            if ($item->can_display_original_price) {
                $item->cart_original_price = $item->retail_price;
            } else {
                $item->cart_original_price = $item->displayed_sale_price;
            }
        });

        return $cart;
    }

    /**
     * 注文以前（カート投入後）の商品販売価格を代入する (注文後用)
     *
     * @param \Illuminate\Database\Eloquent\Collection|\App\Models\Item $targetItems
     * @param \App\Models\Order $order
     * @param array $member
     * @param int $addingCount
     *
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Item
     */
    public function fillPriceBeforeOrderAfterOrdered(
        $targetItems,
        \App\Models\Order $order,
        array $member,
        int $addingCount
    ) {
        $orderedMember = $order->is_guest ? null : $member;

        $this->fillDisplayedSalePrice($targetItems, $orderedMember, $order->order_date);

        $orderedItems = $order->orderDetails->map(function ($orderDetail) {
            return new \App\Entities\Ymdy\Ec\OrderingItem([
                'amount' => $orderDetail->amount,
                'id' => $orderDetail->itemDetail->item_id,
            ]);
        });

        $this->fillPriceBeforeOrder($targetItems, $orderedItems, $order->order_date, $orderedMember, $addingCount);

        return $targetItems;
    }

    /**
     * 注文以前（カート投入後）の商品販売価格を代入する
     *
     * @param \Illuminate\Database\Eloquent\Collection|\App\Models\Item $items
     * @param \Illuminate\Support\Collection|\App\Entities\Ymdy\Ec\OrderingItem[] $orderingItems
     * @param array|null $member
     * @param string|null $orderedDate
     * @param int|null $addingCount $targetItemsを$orderedItemsに何個カウントするか(新規注文の場合0、管理画面で追加する場合追加分の個数)
     *
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Item
     */
    private function fillPriceBeforeOrder(
        $items,
        $orderingItems,
        ?string $orderedDate = null,
        ?array $member = null,
        ?int $addingCount = 0
    ) {
        $isSingle = $items instanceof \App\Models\Item;

        $items = $isSingle ? $items->newCollection([$items]) : $items;

        $this->fillBundleSales($items, $orderingItems, $orderedDate, $member, $addingCount);

        foreach ($items as $item) {
            $item->price_before_order = empty($item->bundle_sale_price)
                ? $item->displayed_sale_price
                : $item->bundle_sale_price;
        }

        return $isSingle ? $items->first() : $items;
    }

    /**
     * 適用対象になるバンドル販売を代入する
     *
     * @param \Illuminate\Database\Eloquent\Collection $targetItems 割引計算対象の商品
     * @param \Illuminate\Support\Collection|\App\Entities\Ymdy\Ec\OrderingItem[] $orderingItems 注文済み|注文しようとしている商品
     * @param string|null $orderedDate
     * @param array|null $member
     * @param int|null $addingCount $targetItemsを$orderedItemsに何個カウントするか(新規注文の場合0、管理画面で追加する場合追加分の個数)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function fillBundleSales(
        Collection $targetItems,
        $orderingItems,
        ?string $orderedDate = null,
        ?array $member = null,
        ?int $addingCount = 0
    ) {
        $targetItems->load(['bundleSaleEvents' => function ($query) use ($member, $orderedDate) {
            $query = $this->applyBaseEventConditions($query, $member, $orderedDate);

            return $query
                ->where('events.sale_type', \App\Enums\Event\SaleType::Bundle)
                ->orderBy('events.period_from', 'desc');
        },
            'bundleSaleEvents.eventBundleSales',
            'bundleSaleEvents.eventItems',
        ]);

        foreach ($targetItems as $targetItem) {
            $itemAmountDict = $this->createItemAmountDict($orderingItems);

            if ($addingCount > 0) {
                $itemAmountDict = $this->meregeOrderingItemToItemAmountDict($itemAmountDict, $targetItem, $addingCount);
            }

            $targetItem->bundle_discount_rate = null;
            $targetItem->bundle_sale_price = null;
            $targetItem->unsetRelation('appliedBundleSale');

            $eventBundleSale = $this->findApplingEventBundleSale($targetItem, $itemAmountDict);

            if (empty($eventBundleSale)) {
                continue;
            }

            $targetItem->setRelation('appliedBundleSale', $eventBundleSale);
            $targetItem->bundle_discount_rate = $eventBundleSale->rate;
            $targetItem->bundle_sale_price = ItemPriceUtil::calcDiscountedPriceByScalar(
                $targetItem->displayed_sale_price,
                $eventBundleSale->rate
            );
        }
    }

    /**
     * 注文中の商品に、注文候補の商品を追加した場合の数量をIDとマッピングして連想配列にする。
     *
     * @param \Illuminate\Support\Collection|\App\Entities\Ymdy\Ec\OrderingItem[] $orderedItems
     * @param \App\Models\Item $orderingItem
     *
     * @return array
     */
    private function createItemAmountDict($orderedItems)
    {
        $orderedItemDict = Arr::reduce($orderedItems, function ($dict, $orderedItem) {
            if (!isset($dict[$orderedItem->id])) {
                $dict[$orderedItem->id] = 0;
            }

            $dict[$orderedItem->id] += $orderedItem->amount;

            return $dict;
        }, []);

        return $orderedItemDict;
    }

    /**
     * @param array $itemAmountDict
     * @param \App\Models\Item $orderingItem
     * @param int $addingCount
     *
     * @return array
     */
    private function meregeOrderingItemToItemAmountDict(array $itemAmountDict, \App\Models\Item $orderingItem, int $addingCount)
    {
        if (!isset($itemAmountDict[$orderingItem->id])) {
            $itemAmountDict[$orderingItem->id] = 0;
        }

        $itemAmountDict[$orderingItem->id] += $addingCount;

        return $itemAmountDict;
    }

    /**
     * 適用されるバンドル販売の設定を取得する。
     * NOTE: パラメータの$itemはソート済みのリレーションを事前に読み込む必要がある。
     *
     * @param \App\Models\Item $item
     * @param array $itemAmountDict
     *
     * @return \App\Models\EventBundleSale
     */
    private function findApplingEventBundleSale(\App\Models\Item $item, $itemAmountDict)
    {
        foreach ($item->bundleSaleEvents as $event) {
            if ($event->eventBundleSales->isEmpty()) {
                continue;
            }

            $orderingCount = Arr::reduce($event->eventItems, function ($sum, $eventItem) use ($itemAmountDict) {
                return $sum + ($itemAmountDict[$eventItem->item_id] ?? 0);
            }, 0);

            $targets = $event->eventBundleSales->where('count', '<=', $orderingCount);

            if ($targets->isEmpty()) {
                continue;
            }

            return $targets->sortBy('rate')->last();
        }

        return null;
    }
}
