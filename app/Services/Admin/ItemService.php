<?php

namespace App\Services\Admin;

use Exception;
use Illuminate\Support\Facades\DB;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class ItemService extends Service implements ItemServiceInterface
{
    /**
     * @var string
     */
    protected $delimiter = '・';

    /**
     * @var \App\Repositories\ItemRepository
     */
    private $itemRepository;

    /**
     * @var \App\Repositories\ItemDetailRepository
     */
    private $itemDetailRepository;

    /**
     * @var \App\Repositories\ItemOnlineCategoryRepository
     */
    private $itemOnlineCategoryRepository;

    /**
     * @var \App\Repositories\ItemOnlineTagRepository
     */
    private $itemOnlineTagRepository;

    /**
     * @var \App\Repositories\ItemRecommendRepository
     */
    private $itemRecommendRepository;

    /**
     * @var \App\Repositories\ItemSalesTypesRepository
     */
    private $itemSalesTypesRepository;

    /**
     * @var \App\Repositories\ItemSubBrandRepository
     */
    private $itemSubBrandRepository;

    /**
     * @var \App\Repositories\ItemsUsedSameStylingRepository
     */
    private $itemsUsedSameStylingRepository;

    /**
     * @var \App\Utils\Csv\ExportCsvInterface
     */
    private $exportCsvUtil;

    /**
     * @var \App\Domain\ItemPriceInterface
     */
    private $itemPriceService;

    /**
     * @var \App\Domain\ItemImageInterface
     */
    private $itemImageService;

    /**
     * @var \App\Domain\MemberInterface
     */
    private $memberService;

    /**
     * @param \App\Repositories\ItemRepository $itemRepository
     * @param \App\Repositories\ItemDetailRepository $itemDetailRepository
     * @param \App\Repositories\OrganizationRepository $organizationRepository
     * @param \App\Repositories\ItemImageRepository $itemImageRepository
     * @param \App\Repositories\ItemOnlineCategoryRepository $itemOnlineCategoryRepository
     * @param \App\Repositories\ItemOnlineTagRepository $itemOnlineTagRepository
     * @param \App\Repositories\ItemRecommendRepository $itemRecommendRepository
     * @param \App\Repositories\ItemSalesTypesRepository $itemSalesTypesRepository
     * @param \App\Repositories\ItemSubBrandRepository $itemSubBrandRepository
     * @param \App\Repositories\ItemsUsedSameStylingRepository $itemsUsedSameStylingRepository
     * @param \App\Utils\Csv\ExportCsvInterface $exportCsvUtil
     * @param \App\Domain\ItemPriceInterface $itemPriceService
     * @param \App\Domain\ItemImageInterface $itemImageService
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \App\Repositories\ItemRepository $itemRepository,
        \App\Repositories\ItemDetailRepository $itemDetailRepository,
        \App\Repositories\ItemImageRepository $itemImageRepository,
        \App\Repositories\ItemOnlineCategoryRepository $itemOnlineCategoryRepository,
        \App\Repositories\ItemOnlineTagRepository $itemOnlineTagRepository,
        \App\Repositories\ItemRecommendRepository $itemRecommendRepository,
        \App\Repositories\ItemSalesTypesRepository $itemSalesTypesRepository,
        \App\Repositories\ItemSubBrandRepository $itemSubBrandRepository,
        \App\Repositories\ItemsUsedSameStylingRepository $itemsUsedSameStylingRepository,
        \App\Utils\Csv\ExportCsvInterface $exportCsvUtil,
        \App\Domain\ItemPriceInterface $itemPriceService,
        \App\HttpCommunication\StaffStart\StaffStartInterface $staffStart,
        \App\Repositories\OrganizationRepository $organizationRepository,
        \App\Domain\ItemImageInterface $itemImageService,
        \App\Domain\MemberInterface $memberService
    ) {
        $this->itemRepository = $itemRepository;
        $this->itemDetailRepository = $itemDetailRepository;
        $this->itemImageRepository = $itemImageRepository;
        $this->itemOnlineCategoryRepository = $itemOnlineCategoryRepository;
        $this->itemOnlineTagRepository = $itemOnlineTagRepository;
        $this->itemRecommendRepository = $itemRecommendRepository;
        $this->itemSalesTypesRepository = $itemSalesTypesRepository;
        $this->itemSubBrandRepository = $itemSubBrandRepository;
        $this->itemsUsedSameStylingRepository = $itemsUsedSameStylingRepository;
        $this->exportCsvUtil = $exportCsvUtil;
        $this->itemPriceService = $itemPriceService;
        $this->staffStart = $staffStart;
        $this->organizationRepository = $organizationRepository;
        $this->itemImageService = $itemImageService;
        $this->memberService = $memberService;

        if (auth('admin_api')->check()) {
            $this->memberService->setStaffToken(auth('admin_api')->user()->token);
        }
    }

    /**
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search(array $params)
    {
        $this->itemRepository->pushCriteria(
            new \App\Criteria\Item\AdminSearchCriteria($params)
        );

        $items = $this->itemRepository->with(['itemDetails', 'itemImages'])->paginate(
            config('repository.pagination.admin_limit', 50)
        );

        return $items;
    }

    /**
     * Update
     *
     * @param array $request
     * @param int $itemId
     *
     * @return \App\Models\Item
     */
    public function update(array $params, int $itemId)
    {
        try {
            DB::beginTransaction();

            $item = $this->itemRepository->update($params, $itemId);

            $this->itemDetailRepository->updateBatch($params['item_details']);

            if (isset($params['item_images'])) {
                $this->itemImageService->deleteAndInsertItemImages($params, $item);
            }

            if (isset($params['sales_types'])) {
                $this->itemSalesTypesRepository->deleteAndInsertBatch(array_map(function ($salesType) {
                    return [
                        'sales_type_id' => $salesType['id'],
                        'sort' => $salesType['sort'],
                    ];
                }, $params['sales_types']), 'item_id', $itemId);
            }

            if (isset($params['items_used_same_styling_used_item_id'])) {
                $this->itemsUsedSameStylingRepository->deleteAndInsertBatch(array_map(function ($id) {
                    return ['used_item_id' => $id];
                }, $params['items_used_same_styling_used_item_id']), 'item_id', $itemId);
            }

            if (isset($params['online_category_id'])) {
                $this->itemOnlineCategoryRepository->deleteAndInsertBatch(array_map(function ($id) {
                    return ['online_category_id' => $id];
                }, $params['online_category_id']), 'item_id', $itemId);
            } else {
                $this->itemOnlineCategoryRepository->deleteAndInsertBatch([], 'item_id', $itemId);
            }

            if (isset($params['online_tag_id'])) {
                $this->itemOnlineTagRepository->deleteAndInsertBatch(array_map(function ($id) {
                    return ['online_tag_id' => $id];
                }, $params['online_tag_id']), 'item_id', $itemId);
            } else {
                $this->itemOnlineTagRepository->deleteAndInsertBatch([], 'item_id', $itemId);
            }

            if (isset($params['item_sub_brands'])) {
                $this->itemSubBrandRepository->deleteAndInsertBatch(array_map(function ($subStoreBrand) {
                    return ['sub_store_brand' => $subStoreBrand];
                }, $params['item_sub_brands']), 'item_id', $itemId);
            } else {
                $this->itemSubBrandRepository->deleteAndInsertBatch([], 'item_id', $itemId);
            }

            if (isset($params['recommend_item_id'])) {
                $this->itemRecommendRepository->deleteAndInsertBatch(array_map(function ($id) {
                    return ['recommend_item_id' => $id];
                }, $params['recommend_item_id']), 'item_id', $itemId);
            } else {
                $this->itemRecommendRepository->deleteAndInsertBatch([], 'item_id', $itemId);
            }

            // 同期性を担保するためトランザクション内で実行する
            $item = $this->itemRepository->find($itemId);

            DB::commit();

            return $item;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param \Illuminate\Http\UploadedFile $image
     * @param int $itemId
     *
     * @return Collection $item
     */
    public function uploadImage(\Illuminate\Http\UploadedFile $image, int $itemId)
    {
        $content = $image->get();
        $fileName = $image->getClientOriginalName();

        $item = $this->itemRepository->find($itemId);

        $params = [
            'file_name' => $fileName,
            'caption' => null,
            'color_id' => null,
        ];

        $this->itemImageService->create($item, $content, $params);

        return $item;
    }

    /**
     * @param array $params
     *
     * @return \Closure
     */
    public function getCsvExporter(array $params)
    {
        $this->exportCsvUtil->setHeaders([
            // items
            'term_id' => __('resource.term'),
            'season_id' => __('validation.attributes.item.season_id'),
            'organization_id' => __('validation.attributes.item.organization_id'),
            'division_id' => __('validation.attributes.item.division_id'),
            'department_id' => __('validation.attributes.item.department_id'),
            'product_number' => __('validation.attributes.item.product_number'),
            'maker_product_number' => __('validation.attributes.item.maker_product_number'),
            'fashion_speed' => __('validation.attributes.item.fashion_speed'),
            'name' => __('validation.attributes.item.name'),
            'main_store_brand' => __('validation.attributes.item.main_store_brand'),
            'item_sub_brand' => __('validation.attributes.item_sub_brand.sub_store_brand'),
            'brand_id' => __('validation.attributes.item.brand_id'),
            'display_name' => __('validation.attributes.item.display_name'),
            'retail_price' => __('validation.attributes.item.retail_price'),
            'price_change_rate' => __('validation.attributes.item.price_change_rate'),
            'price_change_period' => __('validation.attributes.item.price_change_period'),
            'discount_rate' => __('validation.attributes.item.discount_rate'),
            'is_member_discount' => __('validation.attributes.item.is_member_discount'),
            'member_discount_rate' => __('validation.attributes.item.member_discount_rate'),
            'sales_period_from' => __('validation.attributes.item.sales_period_from'),
            'sales_period_to' => __('validation.attributes.item.sales_period_to'),
            'sales_status' => __('validation.attributes.item.sales_status'),
            'sales_type' => __('resource.sales_type'),
            'online_category' => __('resource.online_category'),
            'online_tag' => __('resource.online_tag'),
            'description' => __('validation.attributes.item.description'),
            'size_caution' => __('validation.attributes.item.size_caution'),
            'size_optional_info' => __('validation.attributes.item.size_optional_info'),
            'material_caution' => __('validation.attributes.item.material_caution'),
            'material_info' => __('validation.attributes.item.material_info'),
            'status' => __('validation.attributes.item.status'),
            'reserve_status' => __('validation.attributes.item.reserve_status'),

            // item_reserve
            'period_from' => __('validation.attributes.item_reserve.period_from'),
            'period_to' => __('validation.attributes.item_reserve.period_to'),
            'reserve_price' => __('validation.attributes.item_reserve.reserve_price'),
            'limited_stock_threshold' => __('validation.attributes.item_reserve.limited_stock_threshold'),
            'out_of_stock_threshold' => __('validation.attributes.item_reserve.out_of_stock_threshold'),
            'expected_arrival_date' => __('validation.attributes.item_reserve.expected_arrival_date'),
            'reserve_note' => __('validation.attributes.item_reserve.note'),

            // item_details
            'stock' => __('validation.attributes.total_stock'),
        ]);

        $this->itemRepository->pushCriteria(
            new \App\Criteria\Item\AdminSearchCriteria($params)
        );

        $this->itemRepository->with([
            'brand',
            'itemSubBrands',
            'onlineCategories',
            'onlineTags',
            'itemDetails',
            'appliedReservation',
            'salesTypes',
        ]);

        return $this->exportCsvUtil->getExporter(function ($exporter) {
            $this->itemRepository->chunk(config('repository.chunk.default'), function ($chunk) use ($exporter) {
                foreach ($chunk as $row) {
                    $data = [];
                    $data['term_id'] = $row->term_id;
                    $data['season_id'] = $row->season_id;
                    $data['organization_id'] = $this->organizationRepository->find($row->organization_id)['name'];
                    $data['division_id'] = $row->division_id;
                    $data['department_id'] = $row->department_id;
                    $data['product_number'] = $row->product_number;
                    $data['maker_product_number'] = $row->maker_product_number;
                    $data['fashion_speed'] = $row->fashion_speed;
                    $data['name'] = $row->name;

                    if (!empty($row->main_store_brand)) {
                        $data['main_store_brand'] = \App\Enums\Common\StoreBrand::getDescription($row->main_store_brand);
                    }
                    $data['item_sub_brand'] = implode($this->delimiter, array_map(function ($itemSubBrand) {
                        return \App\Enums\Common\StoreBrand::getDescription($itemSubBrand['sub_store_brand']);
                    }, $row->itemSubBrands->toArray()));
                    $data['brand_id'] = $row->brand ? $row->brand->name : '';

                    $data['display_name'] = $row->display_name;
                    $data['retail_price'] = number_format($row->retail_price);
                    $data['price_change_rate'] = \App\Utils\Format::percentile($row->price_change_rate);
                    $data['price_change_period'] = \App\Utils\Csv::formatDatetime($row->price_change_period);
                    $data['discount_rate'] = \App\Utils\Format::percentile($row->discount_rate);
                    $data['is_member_discount'] = \App\Utils\Csv::fomatBoolean($row->is_member_discount);
                    $data['member_discount_rate'] = \App\Utils\Format::percentile($row->member_discount_rate);
                    $data['sales_period_from'] = $row->sales_period_from ? \App\Utils\Csv::formatDatetime($row->sales_period_from) : '';
                    $data['sales_period_to'] = $row->sales_period_to ? \App\Utils\Csv::formatDatetime($row->sales_period_to) : '';
                    $data['sales_status'] = \App\Enums\Item\SalesStatus::getDescription($row->sales_status);
                    $data['sales_type'] = implode($this->delimiter, array_map(function ($salesType) {
                        return $salesType['id'];
                    }, $row->salesTypes->toArray()));

                    $data['online_category'] = implode($this->delimiter, array_map(function ($category) {
                        return $category['id'];
                    }, $row->onlineCategories->toArray()));

                    $data['online_tag'] = implode($this->delimiter, array_map(function ($tag) {
                        return $tag['id'];
                    }, $row->onlineTags->toArray()));

                    $data['description'] = $row->description;
                    $data['size_caution'] = $row->size_caution;
                    $data['size_optional_info'] = $row->size_optional_info;
                    $data['material_caution'] = $row->material_caution;
                    $data['material_info'] = $row->material_info;
                    $data['status'] = \App\Enums\Common\Status::getDescription($row->status);
                    $data['reserve_status'] = \App\Utils\Csv::fomatBoolean($row->is_reservation);
                    $data['stock'] = $row->itemDetails->sum('ec_stock');

                    if (!empty($row->itemReserve)) {
                        $data['period_from'] = \App\Utils\Csv::formatDatetime($row->itemReserve->period_from);
                        $data['period_to'] = \App\Utils\Csv::formatDatetime($row->itemReserve->period_to);
                        $data['reserve_price'] = number_format($row->itemReserve->reserve_price);
                        $data['limited_stock_threshold'] = $row->itemReserve->limited_stock_threshold;
                        $data['out_of_stock_threshold'] = $row->itemReserve->out_of_stock_threshold;
                        $data['expected_arrival_date'] = $row->itemReserve->expected_arrival_date;
                        $data['reserve_note'] = $row->itemReserve->note;
                    }

                    $exporter($data);
                }
            });
        });
    }

    /**
     * @param array $params
     *
     * @return \Closure
     */
    public function getInfoCsvExporter(array $params)
    {
        $this->exportCsvUtil->setHeaders([
            // items
            'product_number' => __('validation.attributes.item.product_number'),
            'discount_rate' => __('validation.attributes.item.discount_rate'),
            'is_member_discount' => __('validation.attributes.item.is_member_discount'),
            'member_discount_rate' => __('validation.attributes.item.member_discount_rate'),
            'sales_status' => __('validation.attributes.item.sales_status'),
            'status' => __('validation.attributes.item.status'),
            'sales_type' => __('resource.sales_type'),
            'online_category' => __('resource.online_category'),
            'online_tag' => __('resource.online_tag'),
        ]);

        $this->itemRepository->pushCriteria(
            new \App\Criteria\Item\AdminSearchCriteria($params)
        );

        $this->itemRepository->with([
            'brand',
            'itemSubBrands',
            'onlineCategories',
            'onlineTags',
            'itemDetails',
            'appliedReservation',
            'salesTypes',
        ]);

        return $this->exportCsvUtil->getExporter(function ($exporter) {
            $this->itemRepository->chunk(config('repository.chunk.default'), function ($chunk) use ($exporter) {
                foreach ($chunk as $row) {
                    $data = [];
                    $data['product_number'] = $row->product_number;
                    $data['discount_rate'] = \App\Utils\Format::percentile($row->discount_rate);
                    $data['is_member_discount'] = \App\Utils\Csv::fomatBoolean($row->is_member_discount);
                    $data['member_discount_rate'] = \App\Utils\Format::percentile($row->member_discount_rate);
                    $data['sales_status'] = \App\Enums\Item\SalesStatus::getDescription($row->sales_status);
                    $data['status'] = \App\Enums\Common\Status::getDescription($row->status);
                    $data['sales_type'] = implode($this->delimiter, array_map(function ($salesType) {
                        return $salesType['id'];
                    }, $row->salesTypes->toArray()));
                    $data['online_category'] = implode($this->delimiter, array_map(function ($category) {
                        return $category['id'];
                    }, $row->onlineCategories->toArray()));
                    $data['online_tag'] = implode($this->delimiter, array_map(function ($tag) {
                        return $tag['id'];
                    }, $row->onlineTags->toArray()));

                    $exporter($data);
                }
            });
        });
    }

    /**
     * @param array $params
     *
     * @return \Closure
     */
    public function getImageCsvExporter(array $params)
    {
        $this->exportCsvUtil->setHeaders([
            // items
            'product_number' => __('file_format.csv.headers.item_image.product_number'),
            'index' => __('file_format.csv.headers.item_image.index'),
            'file_name' => __('file_format.csv.headers.item_image.file_name'),
            'color_id' => __('file_format.csv.headers.item_image.color_id'),
            'caption' => __('file_format.csv.headers.item_image.caption'),
        ]);

        $this->itemRepository->pushCriteria(
            new \App\Criteria\Item\AdminSearchCriteria($params)
        );

        return $this->exportCsvUtil->getExporter(function ($exporter) {
            $this->itemRepository->chunk(config('repository.chunk.default'), function ($chunk) use ($exporter) {
                foreach ($chunk as $row) {
                    $data = [];

                    $itemImages = $row->itemImages;

                    foreach ($itemImages as $itemImage) {
                        $data['product_number'] = $row->product_number;
                        $data['index'] = $itemImage->id;
                        $data['file_name'] = $itemImage->file_name;
                        $data['color_id'] = $itemImage->color ? $itemImage->color->code : '';
                        $data['caption'] = $itemImage->caption;

                        $exporter($data);
                    }
                }
            });
        });
    }

    /**
     * 受注追加用商品検索
     *
     * @param array $params
     * @param \App\Models\Order $order
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchForEditingOrder(array $params, \App\Models\Order $order)
    {
        $order->load([
            'orderDetails.orderDetailUnits',
            'orderDetails.itemDetail',
        ]);

        $member = $this->memberService->fetchOne($order->member_id);

        if ($order->is_guest) {
            $query = $this->itemPriceService->getNonMemberSearchScopeQuery();
        } else {
            $query = $this->itemPriceService->getMemberSearchScopeQuery($member, $order);
        }

        $this->itemRepository->scopeQuery($query);

        $this->itemRepository->pushCriteria(
            new \App\Criteria\Item\AdminSearchForAddingOrderCriteria($params)
        );

        $items = $this->itemRepository->paginate(config('repository.pagination.admin_limit'));

        $this->itemPriceService->fillPriceBeforeOrderAfterOrdered($items->getCollection(), $order, $member, 1);

        $items->load([
            'itemDetails.color',
            'itemDetails.size',
            'department',
            'itemImages',
            'onlineCategories.root',
        ]);

        return $items;
    }
}
