<?php

namespace App\Repositories;

use App\Models\CartItem;
use App\Repositories\Traits\QueryBuilderMethodTrait;
use Illuminate\Support\Carbon;

/**
 * Class CartItemRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CartItemRepositoryEloquent extends BaseRepositoryEloquent implements CartItemRepository
{
    use QueryBuilderMethodTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CartItem::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * @param int $id
     *
     * @return \App\Models\CartItem
     */
    public function restore($id)
    {
        $cartItem = $this->find($id);

        $cartItem->deleted_at = null;

        $cartItem->posted_at = Carbon::now()->format('Y-m-d H:i:s');

        $cartItem->invalid = false;

        $cartItem->save();

        return $cartItem;
    }
}
