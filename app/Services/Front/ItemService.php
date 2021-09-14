<?php

namespace App\Services\Front;

use App\Domain\ItemInterface as DomainItemService;
use App\Domain\ItemPriceInterface as ItemPriceService;
use App\Domain\Utils\ItemPrice as ItemPriceUtil;
use App\Enums\Order\OrderType;
use App\Enums\Order\Status;
use App\Exceptions\FatalException;
use App\HttpCommunication\SendGrid\SendGridServiceInterface;
use App\HttpCommunication\Ymdy\MemberInterface;
use App\Mail\BackOrderArrived;
use App\Mail\ReserveArrived;
use App\Models\Order;
use App\Repositories\ClosedMarketRepository;
use App\Repositories\ItemDetailIdentificationRepository;
use App\Repositories\ItemRepository;
use App\Repositories\OrderDetailRepository;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ItemService extends Service implements ItemServiceInterface
{
    private $itemRepository;
    private $itemDetailIndexRepository;
    private $sendGridService;
    private $memberHttp;
    private $itemPriceService;
    private $domainItemService;
    private $closedMarketRepository;

    public function __construct(
        ItemRepository $itemRepository,
        ItemDetailIdentificationRepository $itemDetailIndexRepository,
        OrderDetailRepository $orderDetailRepository,
        ClosedMarketRepository $closedMarketRepository,
        SendGridServiceInterface $sendGridService,
        MemberInterface $memberHttp,
        ItemPriceService $itemPriceService,
        DomainItemService $domainItemService
    ) {
        $this->itemRepository = $itemRepository;
        $this->itemDetailIndexRepository = $itemDetailIndexRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->closedMarketRepository = $closedMarketRepository;
        $this->sendGridService = $sendGridService;
        $this->memberHttp = $memberHttp;
        $this->itemPriceService = $itemPriceService;
        $this->domainItemService = $domainItemService;

        if (auth('api')->check()) {
            $this->memberHttp->setMemberTokenHeader(auth('api')->user()->token);
        }
    }

    /**
     * NOTE: src/server/app/Domain/ItemPrice.phpに商品詳細画面用の同一ロジックがあるため修正のときは注意
     *
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search(array $params)
    {
        $repository = $this->itemRepository;

        $repository->pushCriteria(new \App\Criteria\Item\FrontSearchCriteria($params));
        $repository->pushCriteria(new \App\Criteria\Item\PublicCriteria());
        $repository->pushCriteria(new \App\Criteria\Item\FrontSortCriteria($params));
        $repository->pushCriteria(new \App\Criteria\Item\FrontMypageFavoriteCriteria($params));

        if (auth('api')->check()) {
            $member = $this->memberHttp->fetchMemberDetail(auth('api')->id())->getBody();
            $query = $this->itemPriceService->getMemberSearchScopeQuery($member['member']);
            $repository->scopeQuery($query);
        } else {
            $query = $this->itemPriceService->getNonMemberSearchScopeQuery();
            $repository->scopeQuery($query);
        }

        $items = $repository->paginateWithDistinct('items.id', config('constants.per_page.items'));

        if (isset($params['color_id'])) {
            $items->load(['nonSortItemImages' => function ($query) use ($params) {
                return $query
                    ->select('item_images.*')
                    ->leftJoin('item_images as item_images2', function (JoinClause $query) use ($params) {
                        return $query->on('item_images.id', '=', 'item_images2.id')
                            ->whereIn('item_images2.color_id', $params['color_id']);
                    })
                    ->leftJoin('colors', 'item_images2.color_id', '=', 'colors.id')
                    ->orderBy(DB::raw('item_images2.id is null'))
                    ->orderBy('colors.brightness', 'desc')
                    ->orderBy('item_images2.sort')
                    ->orderBy('item_images.sort');
            }]);
        } else {
            $items->load(['nonSortItemImages' => function ($query) {
                return $query->orderBy('sort');
            }]);
        }

        $items->load([
            'salesTypes' => function ($query) {
                return $query->distinct()
                    ->select('sales_types.*')
                    ->join('item_sales_types as item_sales_types2', 'sales_types.id', '=', 'item_sales_types2.sales_type_id')
                    ->orderBy('item_sales_types2.sort');
            },
            'brand',
            'itemDetails.itemDetailIdentifications',
            'itemDetails.enableClosedMarkets',
            'itemDetails.assignedNormalOrderCartItems',
            'itemDetails.assignedReserveOrderCartItems',
        ]);

        foreach ($items as $item) {
            $this->domainItemService->fillApplicableCartStatus($item);
            $item->can_display_original_price = ItemPriceUtil::canDisplayOriginalPrice($item);
        }

        return $items;
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function ecData(array $params)
    {
        $itemDetailIndexRepository = $this->itemDetailIndexRepository;

        $itemDetailIndexes = $itemDetailIndexRepository
            ->select('id', 'jan_code', 'old_jan_code', 'store_stock', 'ec_stock', 'reservable_stock', 'arrival_date', 'item_detail_id')
            ->with([
                'itemDetail' => function ($query) {
                    $query->select('id', 'color_id', 'size_id', 'item_id');
                    $query->withCount(['redisplayRequests' => function ($query) {
                        $query->where('is_notified', false);
                    }]);
                },
                'itemDetail.color' => function ($query) {
                    $query->select('id', 'name', 'display_name');
                },
                'itemDetail.size' => function ($query) {
                    $query->select('id', 'name');
                },
                'itemDetail.item' => function ($query) {
                    $query->select('id', 'name', 'display_name');
                },
            ])->get()
            ->each(function ($itemDetailIndex) {
                $itemDetailIndex->itemDetail->makeHidden(['color_id', 'size_id', 'item_id']);
            })
            ->makeHidden(['item_detail_id']);

        if (isset($params['jan_code'])) {
            $itemDetailIndexes = $itemDetailIndexes->where('jan_code', $params['jan_code']);
        }

        if (isset($params['offset'])) {
            $itemDetailIndexes = $itemDetailIndexes->skip($params['offset']);
        }

        if (isset($params['limit'])) {
            $itemDetailIndexes = $itemDetailIndexes->take($params['limit']);
        }

        $itemDetailIndexes = $itemDetailIndexes->toArray();

        return $itemDetailIndexes;
    }

    /**
     * @param array $items
     *
     * @return mixed
     */
    public function findOrderedItems(array $items)
    {
        $orderCodes = [];
        try {
            foreach ($items as $item) {
                $conditions = [
                    ['orders.deleted_at', '=', null],
                    ['order_details.deleted_at', '=', null],
                    ['orders.order_type', '='],
                    ['orders.status', '=', OrderType::BackOrder],
                    ['order_details.item_detail_id', '=', $item['item_id']],
                ];
                $orders = Order::join('order_details', 'orders.id', '=', 'order_details.order_id')
                    ->where($conditions)
                    ->get();
                foreach ($orders as $order) {
                    $orderCodes[] = [
                        'purchase_id' => $order->code,
                    ];
                }
            }

            return $orderCodes;
        } catch (Exception $e) {
            throw new FatalException($e->getMessage(), null, $e);
        }
    }

    /**
     * 予約商品・取り寄せ商品入荷処理
     *
     * @param array $items
     * @param int $orderType
     */
    public function storeArriveItems(array $items, int $orderType)
    {
        $orderCodes = [];
        try {
            DB::beginTransaction();
            foreach ($items as $item) {
                $conditions = [
                    ['orders.deleted_at', '=', null],
                    ['order_details.deleted_at', '=', null],
                    ['orders.order_type', '=', $orderType],
                    ['orders.status', '=', Status::Ordered],
                    ['order_details.item_detail_id', '=', $item['item_id']],
                ];
                $orders = Order::join('order_details', 'orders.id', '=', 'order_details.order_id')
                    ->where($conditions)
                    ->limit($item['amount'])
                    ->get();
                // item_details.idでorder_detailsで予約or取り寄せしたものを検索して、受注のステータスを更新
                Order::join('order_details', 'orders.id', '=', 'order_details.order_id')
                    ->where($conditions)
                    ->limit($item['amount'])
                    ->update([
                        'orders.status' => Status::Arrived,
                    ]);
                foreach ($orders as $order) {
                    // 会員情報取得
                    $response = $this->memberHttp->showMember($order->member_id)->getBody();
                    if (!isset($response['member'])) {
                        throw new FatalException(__('error.unexpected'));
                    }
                    $member = $response['member'];
                    // 入荷メール送信
                    $data = [
                        'email' => $member['email'],
                        'lname' => $member['lname'],
                        'fname' => $member['fname'],
                    ];
                    $this->sendArrived($data, $orderType);
                    $orderCodes[] = [
                        'purchase_id' => $order->code,
                    ];
                }
            }
            DB::commit();

            return $orderCodes;
        } catch (Exception $e) {
            DB::rollBack();
            throw new FatalException($e->getMessage(), null, $e);
        }
    }

    /**
     * 商品入荷メール
     *
     * @param array $data
     * @param int $orderType
     */
    private function sendArrived(array $data, int $orderType)
    {
        if ($orderType === OrderType::Reserve) {
            $mail = new ReserveArrived($data);
        } else {
            $mail = new BackOrderArrived($data);
        }

        $mail->to($data['email'], $data['lname'] . $data['fname']);

        $this->sendGridService->send($mail);
    }

    /**
     * 商品詳細のitem取得
     *
     * @param string $productNumber
     * @param int|null $closedMarketId 闇市ID
     * @param bool|null $preview 管理画面のプレビュー機能による実行
     *
     * @return \App\Models\Item
     */
    public function fetchDetail(string $productNumber, ?int $closedMarketId = null, ?bool $preview = false)
    {
        if (!$preview) {
            $this->itemRepository->pushCriteria(new \App\Criteria\Item\PublicCriteria());
        }

        $item = $this->itemRepository->where(['product_number' => $productNumber])->first();

        if (empty($item)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found'));
        }

        if (is_null($closedMarketId)) {
            $this->loadItemDetailRelations($item);
        } else {
            $this->loadItemDetailClosedMarketRelations($item, $closedMarketId);
        }

        $item->load([
            'salesTypes',
            'onlineTags',
            'onlineCategories',
            'itemSubBrands',
            'brand',
            'itemImages',
            'onlineCategories.ancestors',
        ]);

        $member = null;

        if (auth('api')->check()) {
            $member = $this->memberHttp->fetchMemberDetail(auth('api')->id())->getBody();
            $member = $member['member'];
        }

        $this->fillAdditionalItemAttributes($item, $member);

        return $item;
    }

    /**
     * @param \App\Models\Item $item
     *
     * @return \App\Models\Item
     */
    private function loadItemDetailRelations(\App\Models\Item $item)
    {
        $item->load(['itemDetails' => function ($query) {
            return $query
                ->where('status', \App\Enums\Common\Status::Published)
                ->orderBy('sort');
        }]);

        $item->itemDetails->load([
            'enableClosedMarkets',
            'itemDetailIdentifications',
            'assignedNormalOrderCartItems',
            'assignedReserveOrderCartItems',
            'size',
            'color',
        ]);

        return $item;
    }

    /**
     * @param \App\Models\Item $item
     * @param int $closedMarketId
     *
     * @return \App\Models\Item
     */
    private function loadItemDetailClosedMarketRelations(\App\Models\Item $item, int $closedMarketId)
    {
        $this->domainItemService->loadItemDetailClosedMarketRelations($item, $closedMarketId);

        if ($item->itemDetails->isEmpty()) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found'));
        }

        $item->itemDetails->load(['size', 'color']);

        return $item;
    }

    /**
     * レコメンド商品などに価格関連のパラメータを追加する
     *
     * @param \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection $items
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function fillAdditionalItemRecommendationsAttributes($items)
    {
        $item = $items->first();

        if (!$item) {
            return Collection::make([]);
        }

        $items = $item->newCollection()->make($items);

        $items->load([
            'itemDetails.itemDetailIdentifications',
            'itemDetails.enableClosedMarkets',
            'itemDetails.assignedNormalOrderCartItems',
            'itemDetails.assignedReserveOrderCartItems',
        ]);

        $items->each(function ($item) {
            $this->fillAdditionalItemAttributes($item);
        });

        return $items;
    }

    /**
     * 商品の還元ポイントを計算してpointプロパティに追加する
     *
     * @param \App\Models\Item $item
     *
     * @return \App\Models\Item
     */
    public function fillAdditionalItemAttributes(\App\Models\Item $item, array $member = null, $comminicatePoint = true)
    {
        // 割引後の価格
        $this->itemPriceService->fillDisplayedSalePrice($item, $member);

        $item->can_display_original_price = ItemPriceUtil::canDisplayOriginalPrice($item);

        if (!empty($member)) {
            $item->is_favorite = $item->itemFavorites()->where('member_id', $member['id'])->count() > 0;
        } else {
            $item->is_favorite = false;
        }

        $item = $this->domainItemService->fillApplicableCartStatus($item);

        return $item;
    }

    /**
     * @param int $id
     * @param array $params
     *
     * @return bool
     */
    public function verifyEnteringClosedMarket(int $id, array $params)
    {
        $closedMarket = $this->closedMarketRepository->find($id);

        if ($closedMarket->password !== $params['password']) {
            throw new AuthorizationException(__('error.invalid_closed_market_password'));
        }

        return true;
    }
}
