<?php

namespace App\Services\Front;

use App\Exceptions\FatalException;
use App\Exceptions\InvalidInputException;
use App\HttpCommunication\Ymdy\MemberInterface;
use App\Repositories\CartRepository;
use App\Services\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GuestPurchaseService extends Service implements GuestPurchaseServiceInterface
{
    const GUEST_TOKEN_EXPIRED_DAY = 1;

    /**
     * @var MemberInterface
     */
    private $memberHttpCommunication;

    /**
     * @var CartRepository
     */
    private $cartRepository;

    /**
     * @param MemberInterface $memberHttpCommunication
     * @param CartRepository $cartRepository
     */
    public function __construct(
        MemberInterface $memberHttpCommunication,
        CartRepository $cartRepository
    ) {
        $this->memberHttpCommunication = $memberHttpCommunication;
        $this->cartRepository = $cartRepository;

        if (auth('api')->check()) {
            $this->memberHttpCommunication->setMemberTokenHeader(auth('api')->user()->token);
        }
    }

    /**
     * @param int $cartId
     * @param array $params
     *
     * @return array
     */
    public function emailAuth(int $cartId, array $params)
    {
        try {
            $cartToken = $params['token'];

            $cart = $this->cartRepository->findWhere(['id' => $cartId, 'token' => $cartToken])->first();

            if (empty($cart)) {
                throw new NotFoundHttpException(__('validation.guest_purchase.not_found_cart'));
            }

            $guestToken = static::generateGuestAuthToken();
            $successUrl = $this->getEmailAuthSuccessUrl($cart->id, $cartToken, $guestToken);
            $errorUrl = $this->getEmailAuthErrorUrl();

            $data = $this->memberHttpCommunication->storeTemp([
                'email' => $params['email'],
                'success_url' => $successUrl,
                'error_url' => $errorUrl,
                'guest_flag' => true,
            ])->getBody();

            $this->cartRepository->update([
                'guest_token' => $guestToken,
                'member_id' => $data['member']['id'],
            ], $cart->id);

            return $data;
        } catch (\App\HttpCommunication\Exceptions\HttpException $e) {
            if ($e->getResponseStatusCode() === Response::HTTP_CONFLICT) {
                throw new InvalidInputException(
                    error_format('error.email_already_in_use'),
                    \App\Enums\Common\ErrorCode::EmailAlreadyInUse
                );
            }

            throw new FatalException(error_format('error.failed_guest_purchase_mail_auth', ['cart_id' => $cartId]), null, $e);
        }
    }

    /**
     * @param int $cartId
     * @param string $cartToken
     * @param string $guestToken
     *
     * @return string
     */
    private static function getEmailAuthSuccessUrl(int $cartId, string $cartToken, string $guestToken)
    {
        return config('app.front_url') . '/cart/guest-order/verify?member_id={%member_id%}&member_token={%member_token%}&' . http_build_query([
            'cart_id' => $cartId,
            'cart_token' => $cartToken,
            'guest_token' => $guestToken,
        ]);
    }

    /**
     * @return string
     */
    private static function getEmailAuthErrorUrl()
    {
        return config('app.front_url') . '/register/error?error_code={%error_code%}';
    }

    /**
     * @return string
     */
    private static function generateGuestAuthToken()
    {
        return str_replace('-', '', \Webpatser\Uuid\Uuid::generate(4));
    }

    /**
     * @param int $cartId
     * @param array $params
     *
     * @return \App\Models\Cart
     */
    public function verify(int $cartId, array $params)
    {
        $cartToken = $params['cart_token'];
        $guestToken = $params['guest_token'];
        $memberId = $params['member_id'];

        $cart = $this->cartRepository->findWhere([
            'id' => $cartId,
            'token' => $cartToken,
            'guest_token' => $guestToken,
            'member_id' => $memberId,
        ])->first();

        if (empty($cart)) {
            throw new NotFoundHttpException(__('validation.guest_purchase.not_found_cart'));
        }

        if (!$this->validateGuestCart($cart)) {
            throw new InvalidInputException(error_format('validation.guest_purchase.expired_verification'));
        }

        $cart = $this->cartRepository->update(['guest_verified' => true], $cart->id);

        return $cart;
    }

    /**
     * @param \App\Models\Cart $cart
     *
     * @return bool
     */
    public function validateGuestCart(\App\Models\Cart $cart)
    {
        return Carbon::now()->subDays(self::GUEST_TOKEN_EXPIRED_DAY)->lt($cart->guest_token_created_at);
    }

    /**
     * @param int $memberId
     * @param string $memberToken
     *
     * @return array
     */
    public function fetchMemberDetail(int $memberId, string $memberToken)
    {
        $response = $this->memberHttpCommunication->showMember($memberId, $memberToken);

        $data = $response->getBody();

        return $data['member'];
    }
}
