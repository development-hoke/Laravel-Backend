<?php

namespace App\Repositories;

use App\Models\Cart;

/**
 * Class CartRepositoryEloquent
 *
 * @package App\Repositories
 */
class CartRepositoryEloquent extends BaseRepositoryEloquent implements CartRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Cart::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * 有効なカート検索
     *
     * @param array $where
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     */
    public function search(array $where = [])
    {
        $where[] = ['deleted_at', '=', null];

        return parent::orderBy('id', 'desc')->findWhere($where);
    }

    /**
     * ユニークなトークン発行
     *
     * @return string
     */
    public static function createUniqueToken()
    {
        $token = \Webpatser\Uuid\Uuid::generate(4);

        return str_replace('-', '', $token);
    }

    /**
     * @param array $attributes
     *
     * @return \App\Models\Cart
     */
    public function create(array $attributes)
    {
        if (!isset($attributes['token'])) {
            $attributes['token'] = $this->createUniqueToken();
        }

        if (!isset($attributes['use_coupon_ids'])) {
            $attributes['use_coupon_ids'] = [];
        }

        return parent::create($attributes);
    }

    /**
     * 無効になった商品のカートの移行
     *
     * @param int $id
     *
     * @return \App\Models\Cart
     */
    public function transferCartItems(int $id)
    {
        $cart = $this->find($id);

        $lapsedCartItems = $cart->getLapsedCartItems();

        $newCart = $this->create(['member_id' => $cart->member_id]);

        if ($lapsedCartItems->isEmpty()) {
            return $newCart;
        }

        $newCart->order_type = $cart->order_type;
        $newCart->save();

        $lapsedCartItems->each(function ($cartItem) use ($newCart) {
            $cartItem->cart_id = $newCart->id;
            $cartItem->save();
        });

        return $newCart;
    }
}
