<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\OrderDetail\CancelRequest;
use App\Http\Requests\Api\V1\Admin\OrderDetail\IndexItemsRequest;
use App\Http\Requests\Api\V1\Admin\OrderDetail\ReturnRequest;
use App\Http\Requests\Api\V1\Admin\OrderDetail\StoreRequest;
use App\Http\Resources\Front\Item as ItemResource;
use App\Http\Resources\OrderDetail as OrderDetailResource;
use App\Services\Admin\ItemServiceInterface as ItemService;
use App\Services\Admin\OrderDetailServiceInterface as OrderDetailService;
use App\Services\Admin\OrderServiceInterface as OrderService;

class OrderDetailController extends ApiAdminController
{
    /**
     * @var array
     */
    private $baseRelations = [
        'itemDetail.item.itemImages',
        'itemDetail.color',
        'itemDetail.size',
        'itemDetail.item.department',
        'itemDetail.item.onlineCategories.root',
        'orderDetailUnits',
    ];

    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * @var OrderDetailService
     */
    private $orderDetailService;

    /**
     * @var ItemService
     */
    private $itemService;

    /**
     * @param OrderDetailServiceInterface $service
     */
    public function __construct(
        OrderService $orderService,
        OrderDetailService $orderDetailService,
        ItemService $itemService
    ) {
        $this->orderService = $orderService;
        $this->orderDetailService = $orderDetailService;
        $this->itemService = $itemService;
    }

    /**
     * @param int $orderId
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(int $orderId)
    {
        $orderDetails = $this->orderDetailService->findByOrderId($orderId);

        $orderDetails->load($this->baseRelations);

        return OrderDetailResource::collection($orderDetails);
    }

    /**
     * @param int $id
     *
     * @return OrderDetailResource
     */
    public function show(int $id)
    {
        $orderDetail = $this->orderDetailService->findOne($id);

        $orderDetail->load($this->baseRelations);

        return new OrderDetailResource($orderDetail);
    }

    /**
     * @param StoreRequest $request
     * @param int $orderId
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function store(StoreRequest $request, int $orderId)
    {
        $orderDetails = $this->orderDetailService->add($request->validated(), $orderId);

        $orderDetails->load($this->baseRelations);

        return OrderDetailResource::collection($orderDetails);
    }

    /**
     * @param CancelRequest $request
     * @param int $orderId
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function cancel(CancelRequest $request, int $orderId)
    {
        $orderDetails = $this->orderDetailService->cancel($orderId, $request->validated());

        $orderDetails->load($this->baseRelations);

        return OrderDetailResource::collection($orderDetails);
    }

    /**
     * @param int $id
     *
     * @return OrderDetailResource
     */
    public function return(ReturnRequest $request, int $orderId)
    {
        $orderDetails = $this->orderDetailService->return($orderId, $request->validated());

        $orderDetails->load($this->baseRelations);

        return OrderDetailResource::collection($orderDetails);
    }

    /**
     * @param IndexItemsRequest $request
     * @param int $orderId
     *
     * @return ItemResource
     */
    public function indexItems(IndexItemsRequest $request, int $orderId)
    {
        $order = $this->orderService->findOne($orderId);

        $params = $request->validated();

        $itemDetails = $this->itemService->searchForEditingOrder($params, $order);

        return ItemResource::collection($itemDetails);
    }
}
