<?php

namespace App\Services\Front;

use App\Domain\Adapters\Ymdy\CartMemberPurchaseInterface as MemberPurchaseAdapter;
use App\HttpCommunication\Ymdy\MemberShippingAddressInterface as MemberShippingAddress;
use App\Models\Cart;
use App\Services\Service;

class PointService extends Service implements PointServiceInterface
{
    /**
     * @var MemberPurchaseAdapter
     */
    private $memberPurchaseAdapter;

    /**
     * @var MemberShippingAddress
     */
    private $memberShippingAddress;

    /**
     * @param MemberPurchaseAdapter $memberPurchaseAdapter
     */
    public function __construct(
        MemberPurchaseAdapter $memberPurchaseAdapter,
        MemberShippingAddress $memberShippingAddress
    ) {
        $this->memberPurchaseAdapter = $memberPurchaseAdapter;
        $this->memberShippingAddress = $memberShippingAddress;

        if (auth('api')->check()) {
            $user = auth('api')->user();
            $this->memberPurchaseAdapter->setMemberToken($user->token);
            $this->memberShippingAddress->setMemberTokenHeader($user->token);
        }
    }

    /**
     * 購買時ポイント計算APIを利用して還元ポイントの取得
     *
     * @param Cart $cart
     * @param array $prices
     * @param array $options
     *
     * @return array
     */
    public function getPoint(Cart $cart, array $prices, array $options = [])
    {
        if (empty($cart->member_id) || $cart->is_guest) {
            return [
                'base_grant_point' => 0,
                'special_grant_point' => 0,
                'effective_point' => 0,
            ];
        }

        $pointInfo = $this->memberPurchaseAdapter->calculatePoint($cart, $prices, $options);

        return $pointInfo;
    }
}
