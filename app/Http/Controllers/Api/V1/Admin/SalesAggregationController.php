<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\SalesAggregation\AggregateItemsRequest;
use App\Http\Requests\Api\V1\Admin\SalesAggregation\AggregateOrdersRequest;
use App\Http\Requests\Api\V1\Admin\SalesAggregation\ExportItemCsvRequest;
use App\Http\Resources\ItemSalesAggregation as ItemResource;
use App\Http\Resources\OrderSalesAggregation as OrderResource;
use App\Repositories\SalesAggregation\ItemRepository;
use App\Services\Admin\SalesAggregationServiceInterface as SalesAggregationService;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class SalesAggregationController extends ApiAdminController
{
    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @var SalesAggregationService
     */
    private $salesAggregationService;

    /**
     * @param ItemRepository $itemRepository
     * @param SalesAggregationService $salesAggregationService
     */
    public function __construct(
        ItemRepository $itemRepository,
        SalesAggregationService $salesAggregationService
    ) {
        $this->itemRepository = $itemRepository;
        $this->salesAggregationService = $salesAggregationService;
    }

    /**
     * @param AggregateOrdersRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function aggregateOrders(AggregateOrdersRequest $request)
    {
        $params = $request->validated();

        if (isset($params['use_default']) && $params['use_default']) {
            $params = array_merge($params, $request->getDefaultParams());
        }

        $data = $this->salesAggregationService->aggregateOrders($params);

        return OrderResource::collection($data)->additional([
            // デフォルト値を取得するため、リクエストパラメータを一緒に返却する
            // 'product_number', 'maker_product_number'は件数が多いかもしれないので除外する
            'request_params' => Arr::except($params, ['product_number', 'maker_product_number']),
        ]);
    }

    /**
     * @param AggregateItemsRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function aggregateItems(AggregateItemsRequest $request)
    {
        $params = $request->validated();

        if (isset($params['use_default']) && $params['use_default']) {
            $params = array_merge($params, $request->getDefaultParams());
        }

        $items = $this->salesAggregationService->aggregateItems($params);

        return ItemResource::collection($items)->additional([
            // デフォルト値を取得するため、リクエストパラメータを一緒に返却する
            // 'product_number', 'maker_product_number'は件数が多いかもしれないので除外する
            'request_params' => Arr::except($params, ['product_number', 'maker_product_number']),
        ]);
    }

    /**
     * @return collection
     */
    public function aggreateDailyOrder()
    {
        $totalRetailByBrand = $this->salesAggregationService->aggreateDailyOrder();

        return $totalRetailByBrand;
    }

    /**
     * @return collection
     */
    public function aggregateMonthlyOrder()
    {
        $totalRetailByBrandByOrder = $this->salesAggregationService->aggreateMonthlyOrder('order_date');
        $totalRetailByBrandByDelivery = $this->salesAggregationService->aggreateMonthlyOrder('delivery_hope_date');
        $result = [];
        $result['order'] = $totalRetailByBrandByOrder;
        $result['delivery'] = $totalRetailByBrandByDelivery;

        return $result;
    }

    /**
     * @param AggregateOrdersRequest $request
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportOrderCsv(AggregateOrdersRequest $request)
    {
        $params = $request->validated();

        if (isset($params['use_default']) && $params['use_default']) {
            $params = array_merge($params, $request->getDefaultParams());
        }

        $fileName = __('file.csv.admin.sales_aggregation_order', ['datetime' => date('YmdHis')]);

        $headers = \App\Utils\FileDownloadUtil::getExportFileHeaders(
            $fileName,
            \App\Utils\FileUtil::MIME_TYPE_CSV
        );

        return response()->stream(
            $this->salesAggregationService->getSalesOrderCsvExporter($params),
            Response::HTTP_OK,
            $headers
        );
    }

    /**
     * @param AggregateOrdersRequest $request
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportOrderDetailCsv(AggregateOrdersRequest $request)
    {
        $params = $request->validated();

        if (isset($params['use_default']) && $params['use_default']) {
            $params = array_merge($params, $request->getDefaultParams());
        }

        $fileName = __('file.csv.admin.sales_aggregation_order_detail', ['datetime' => date('YmdHis')]);

        $headers = \App\Utils\FileDownloadUtil::getExportFileHeaders(
            $fileName,
            \App\Utils\FileUtil::MIME_TYPE_CSV
        );

        return response()->stream(
            $this->salesAggregationService->getSalesOrderDetailCsvExporter($params),
            Response::HTTP_OK,
            $headers
        );
    }

    /**
     * @param ExportItemCsvRequest $request
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportItemCsv(ExportItemCsvRequest $request)
    {
        $params = $request->validated();

        if (isset($params['use_default']) && $params['use_default']) {
            $params = array_merge($params, $request->getDefaultParams());
        }

        $fileName = __('file.csv.admin.sales_aggregation_order_detail', ['datetime' => date('YmdHis')]);

        $headers = \App\Utils\FileDownloadUtil::getExportFileHeaders(
            $fileName,
            \App\Utils\FileUtil::MIME_TYPE_CSV
        );

        return response()->stream(
            $this->salesAggregationService->getSalesItemCsvExporter($params),
            Response::HTTP_OK,
            $headers
        );
    }
}
