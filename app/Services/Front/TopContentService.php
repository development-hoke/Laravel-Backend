<?php

namespace App\Services\Front;

use App\HttpCommunication\Exceptions\HttpException;
use App\HttpCommunication\Ymdy\MemberInterface;
use App\Models\TopContent;
use App\Repositories\ItemRepository;
use App\Repositories\PlanRepository;
use App\Repositories\TopContentAdminRepository;
use Illuminate\Http\Response;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TopContentService extends Service implements TopContentServiceInterface
{
    /**
     * @var TopContentAdminRepository
     */
    private $topContentRepository;

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @var PlanRepository
     */
    private $planRepository;

    /**
     * @var MemberInterface
     */
    private $memberHttp;

    /**
     * @var ItemServiceInterface
     */
    private $itemService;

    /**
     * @param TopContentAdminRepository $topContentRepository
     */
    public function __construct(
        TopContentAdminRepository $topContentRepository,
        ItemRepository $itemRepository,
        PlanRepository $planRepository,
        MemberInterface $memberHttp,
        ItemServiceInterface $itemService
    ) {
        $this->topContentRepository = $topContentRepository;
        $this->itemRepository = $itemRepository;
        $this->planRepository = $planRepository;
        $this->memberHttp = $memberHttp;
        $this->itemService = $itemService;

        if (auth('api')->check()) {
            $user = auth('api')->user();
            $this->memberHttp->setMemberTokenHeader($user->token);
        }
    }

    /**
     * 該当ストアブランドのtop_contentを取得
     *
     * @param int|null $storeBrand
     *
     * @return TopContent
     */
    public function fetchOneByStoreBrand(array $params)
    {
        $storeBrand = $params['store_brand'] ?? null;
        $topContent = $this->topContentRepository->where('top_contents.store_brand', $storeBrand)->first();

        if (empty($topContent)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', ['store_brand' => $storeBrand]));
        }

        $topContent->main_visuals = array_filter($topContent->main_visuals, [$this, 'statusActive']);

        $this->loadRelatedData($topContent);

        return $topContent;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function statusActive($item)
    {
        return $item['status'] == true;
    }

    /**
     * jsonデータに関連するデータを読み込む
     *
     * @param TopContent $topContent
     *
     * @return void
     */
    private function loadRelatedData(TopContent $topContent)
    {
        $this->loadNewItems($topContent);
        $this->loadPickups($topContent);
        $this->loadNews($topContent);
        $this->loadFeatures($topContent);
    }

    /**
     * new_itemsのデータを読み込む
     *
     * @param TopContent $topContent
     *
     * @return void
     */
    private function loadNewItems(TopContent $topContent)
    {
        $newItems = $topContent->new_items ?? [];

        if (count($newItems) === 0) {
            return;
        }

        $itemIds = array_column($newItems, 'item_id');
        $itemOrder = implode(',', $itemIds);

        $items = $this->itemRepository->scopeQuery(function ($query) use ($itemIds, $itemOrder) {
            return $query->whereIn('items.id', $itemIds)->public()->orderByRaw("FIELD(id, $itemOrder)");
        })->with(['itemImages'])->all();

        $items = $this->getItemAdditionalAttirbutes($items);

        $topContent->new_items = $items;
    }

    /**
     * pickupsのデータを読み込む
     *
     * @param TopContent $topContent
     *
     * @return void
     */
    private function loadPickups(TopContent $topContent)
    {
        $pickups = $topContent->pickups ?? [];

        if (count($pickups) === 0) {
            return;
        }

        $itemIds = array_column($pickups, 'item_id');
        $itemOrder = implode(',', $itemIds);

        $items = $this->itemRepository->scopeQuery(function ($query) use ($itemIds, $itemOrder) {
            return $query->whereIn('items.id', $itemIds)->public()->orderByRaw("FIELD(id, $itemOrder)");
        })->with(['itemImages'])->all();

        $items = $this->getItemAdditionalAttirbutes($items);

        $topContent->pickups = $items;
    }

    /**
     * featuresのデータを読み込む
     *
     * @param TopContent $topContent
     *
     * @return void
     */
    private function loadFeatures(TopContent $topContent)
    {
        $features = $topContent->features ?? [];
        $storeBrand = $topContent->store_brand ?? null;

        if (count($features) === 0) {
            return;
        }

        $planIds = array_column($features, 'plan_id');
        $planOrder = implode(',', $planIds);

        $plans = $this->planRepository->scopeQuery(function ($query) use ($planIds, $planOrder, $storeBrand) {
            $query = $storeBrand === null ? $query : $query->where('plans.store_brand', $storeBrand);

            return $query->whereIn('plans.id', $planIds)->published()->active()->orderByRaw("FIELD(id, $planOrder)");
        })->get();

        $topContent->features = $plans;
    }

    /**
     * newsのデータを読み込む
     *
     * @param TopContent $topContent
     *
     * @return void
     */
    private function loadNews(TopContent $topContent)
    {
        $news = $topContent->news ?? [];
        $storeBrand = $topContent->store_brand ?? null;

        if (count($news) === 0) {
            return;
        }

        $planIds = array_column($news, 'plan_id');
        $planOrder = implode(',', $planIds);

        $plans = $this->planRepository->scopeQuery(function ($query) use ($planIds, $planOrder, $storeBrand) {
            $query = $storeBrand === null ? $query : $query->where('plans.store_brand', $storeBrand);

            return $query->whereIn('plans.id', $planIds)->published()->active()->orderByRaw("FIELD(id, $planOrder)");
        })->get();

        $topContent->news = $plans;
    }

    /**
     * itemの補足情報を読み込む
     *
     * @param $items
     *
     * @return void
     */
    private function getItemAdditionalAttirbutes($items)
    {
        $member = null;

        if (auth('api')->check()) {
            $member = $this->memberHttp->fetchMemberDetail(auth('api')->id())->getBody();
            $member = $member['member'];
        }

        foreach ($items as &$item) {
            $this->itemService->fillAdditionalItemAttributes($item, $member);
        }

        return $items;
    }

    /**
     * ストアブランドに紐付く新着商品の情報を返す
     *
     * @param $storeBrand
     *
     * @return $items
     */
    public function fetchNewItemsByStoreBrand($storeBrand)
    {
        $topContent = $this->topContentRepository->where('top_contents.store_brand', $storeBrand)->first();
        $newItems = $topContent->new_items ?? [];

        if (count($newItems) === 0) {
            $items = [];
        } else {
            $itemIds = array_column($newItems, 'item_id');

            $items = $this->itemRepository->scopeQuery(function ($query) use ($itemIds) {
                return $query->whereIn('items.id', $itemIds)->public()->orderby('sales_period_from', 'desc');
            })->with(['itemImages', 'salesTypes', 'brand'])->all();
        }

        return $items;
    }
}
