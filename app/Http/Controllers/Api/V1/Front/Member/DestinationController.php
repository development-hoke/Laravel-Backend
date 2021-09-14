<?php

namespace App\Http\Controllers\Api\V1\Front\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\Member\Destination\DestroyRequest;
use App\Http\Requests\Api\V1\Front\Member\Destination\GetRequest;
use App\Http\Requests\Api\V1\Front\Member\Destination\IndexRequest;
use App\Http\Requests\Api\V1\Front\Member\Destination\StoreRequest;
use App\Http\Requests\Api\V1\Front\Member\Destination\UpdateRequest;
use App\HttpCommunication\Ymdy\MemberShippingAddressInterface;
use App\Repositories\PrefRepository;

class DestinationController extends Controller
{
    private $memberShippingAddress;
    private $prefRepository;

    public function __construct(
        MemberShippingAddressInterface $memberShippingAddress,
        PrefRepository $prefRepository
    ) {
        $this->memberShippingAddress = $memberShippingAddress;
        $this->prefRepository = $prefRepository;

        if (auth('api')->check()) {
            $this->memberShippingAddress->setMemberTokenHeader(auth('api')->user()->token);
        }
    }

    /**
     * お届け先情報一覧取得
     *
     * @param IndexRequest $request
     * @param $memberId
     *
     * @return array
     */
    public function index(IndexRequest $request, $memberId)
    {
        $params = $request->validated();
        $billingAddressFlag = isset($params['billing_address_flag']) && $params['billing_address_flag'];

        $response = $this->memberShippingAddress->index($memberId, [])->getBody();

        // todo リソースクラスの利用
        $data = array_map(function ($shippingAddress) {
            $data = $shippingAddress;
            if (!isset($data['pref'])) {
                $pref = $this->prefRepository->find($data['pref_id']);
                $data['pref'] = $pref->toArray();
            }

            return $data;
        }, $response['shipping_addresses']);

        $data = array_filter($data, function ($shippingAddress) use ($billingAddressFlag) {
            return (int) $shippingAddress['billing_address_flag'] === (int) $billingAddressFlag;
        });

        return array_values($data);
    }

    /**
     * お届け先情報登録
     *
     * @param StoreRequest $request
     * @param $memberId
     *
     * @return array
     */
    public function store(StoreRequest $request, $memberId)
    {
        $response = $this->memberShippingAddress->store($memberId, $request->validated())->getBody();

        // todo リソースクラスの利用
        $data = $response['shipping_address'];
        if (!isset($data['pref'])) {
            $pref = $this->prefRepository->find($data['pref_id']);
            $data['pref'] = $pref->toArray();
        }

        return $data;
    }

    /**
     * お届け先情報詳細取得
     *
     * @param GetRequest $request
     * @param $memberId
     * @param $destinationId
     *
     * @return array
     */
    public function get(GetRequest $request, $memberId, $destinationId)
    {
        $response = $this->memberShippingAddress->get($destinationId)->getBody();

        // todo リソースクラスの利用
        $data = $response['shipping_address'];
        if (!isset($data['pref'])) {
            $pref = $this->prefRepository->find($data['pref_id']);
            $data['pref'] = $pref->toArray();
        }

        return $data;
    }

    /**
     * お届け先情報編集
     *
     * @param UpdateRequest $request
     * @param $memberId
     * @param $destinationId
     *
     * @return array
     */
    public function update(UpdateRequest $request, $memberId, $destinationId)
    {
        $response = $this->memberShippingAddress->update($destinationId, $request->validated())->getBody();

        // todo リソースクラスの利用
        $data = $response['shipping_address'];
        if (!isset($data['pref'])) {
            $pref = $this->prefRepository->find($data['pref_id']);
            $data['pref'] = $pref->toArray();
        }

        return $data;
    }

    /**
     * お届け先情報削除
     *
     * @param DestroyRequest $request
     * @param $memberId
     * @param $destinationId
     *
     * @return array
     */
    public function destroy(DestroyRequest $request, $memberId, $destinationId)
    {
        return $this->memberShippingAddress->destroy($destinationId)->getBody();
    }
}
