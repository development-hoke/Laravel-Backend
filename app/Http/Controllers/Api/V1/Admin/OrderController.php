<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\Order\AddCouponRequest;
use App\Http\Requests\Api\V1\Admin\Order\IndexRequest;
use App\Http\Requests\Api\V1\Admin\Order\RemoveCouponRequest;
use App\Http\Requests\Api\V1\Admin\Order\SendMessageRequest;
use App\Http\Requests\Api\V1\Admin\Order\ShowMessageRequest;
use App\Http\Requests\Api\V1\Admin\Order\ShowRequest;
use App\Http\Requests\Api\V1\Admin\Order\UpdatePriceRequest;
use App\Http\Requests\Api\V1\Admin\Order\UpdateRequest;
use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\OrderMessage as OrderMessageResource;
use App\Repositories\OrderMessageRepository;
use App\Services\Admin\OrderCsvServiceInterface as OrderCsvService;
use App\Services\Admin\OrderServiceInterface;
use Illuminate\Http\Response;

class OrderController extends ApiAdminController
{
    /**
     * @var OrderMessageRepository
     */
    private $orderMessageRepository;

    /**
     * @var OrderServiceInterface
     */
    private $service;

    /**
     * @var OrderCsvService
     */
    private $orderCsvService;

    /**
     * @param OrderMessageRepository $orderMessageRepository
     * @param OrderServiceInterface $service
     */
    public function __construct(
        OrderMessageRepository $orderMessageRepository,
        OrderServiceInterface $service,
        OrderCsvService $orderCsvService
    ) {
        $this->orderMessageRepository = $orderMessageRepository;
        $this->service = $service;
        $this->orderCsvService = $orderCsvService;
    }

    /**
     * @param IndexRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $params = $request->validated();

        $orders = $this->service->search($params);

        $orders->load([
            'memberOrderAddress.pref',
            'deliveryOrderAddress.pref',
            'billingOrderAddress.pref',
        ]);

        return OrderResource::collection($orders);
    }

    /**
     * @param ShowRequest $request
     * @param int $id
     *
     * @return OrderResource
     */
    public function show(ShowRequest $request, int $id)
    {
        $order = $this->service->findOne($id);

        return new OrderResource($order);
    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     *
     * @return OrderResource
     */
    public function update(UpdateRequest $request, int $id)
    {
        $order = $this->service->update($request->validated(), $id);

        return new OrderResource($order);
    }

    /**
     * @param int $id
     *
     * @return OrderResource
     */
    public function cancel(int $id)
    {
        $order = $this->service->cancel($id);

        return new OrderResource($order);
    }

    /**
     * @param ShowMessageRequest $request
     * @param int $id
     *
     * @return OrderMessageResource
     */
    public function showMessage(ShowMessageRequest $request, int $id)
    {
        $message = $this->orderMessageRepository->find($id);

        return new OrderMessageResource($message);
    }

    /**
     * @param SendMessageRequest $request
     * @param int $orderId
     *
     * @return OrderMessageResource
     */
    public function sendMessage(SendMessageRequest $request, int $orderId)
    {
        $message = $this->service->sendOrderMessage($orderId, $request->validated());

        return new OrderMessageResource($message);
    }

    /**
     * @param AddCouponRequest $request
     * @param int $id
     *
     * @return OrderResource
     */
    public function addCoupon(AddCouponRequest $request, int $id)
    {
        $order = $this->service->addCoupon($request->validated(), $id);

        return new OrderResource($order);
    }

    /**
     * @param RemoveCouponRequest $request
     * @param int $id
     *
     * @return OrderResource
     */
    public function removeCoupon(RemoveCouponRequest $request, int $id)
    {
        $order = $this->service->removeCoupon($request->validated(), $id);

        return new OrderResource($order);
    }

    /**
     * @param UpdatePriceRequest $request
     * @param int $id
     *
     * @return OrderResource
     */
    public function updatePrice(UpdatePriceRequest $request, int $id)
    {
        $order = $this->service->updatePrice($request->validated(), $id);

        return new OrderResource($order);
    }

    /**
     * @param int $id
     *
     * @return OrderResource
     */
    public function return(int $id)
    {
        $order = $this->service->return($id);

        return new OrderResource($order);
    }

    /**
     * @param IndexRequest $request
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv(IndexRequest $request)
    {
        $fileName = __('file.csv.admin.order', ['datetime' => date('YmdHis')]);

        $headers = \App\Utils\FileDownloadUtil::getExportFileHeaders(
            $fileName,
            \App\Utils\FileUtil::MIME_TYPE_CSV
        );

        return response()->stream(
            $this->orderCsvService->exportOrderCsv($request->validated()),
            Response::HTTP_OK,
            $headers
        );
    }

    /**
     * @param IndexRequest $request
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportDetailCsv(IndexRequest $request)
    {
        $fileName = __('file.csv.admin.order_detail', ['datetime' => date('YmdHis')]);

        $headers = \App\Utils\FileDownloadUtil::getExportFileHeaders(
            $fileName,
            \App\Utils\FileUtil::MIME_TYPE_CSV
        );

        return response()->stream(
            $this->orderCsvService->exportOrderDetailCsv($request->validated()),
            Response::HTTP_OK,
            $headers
        );
    }
}
