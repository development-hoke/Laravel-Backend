<?php

namespace App\Http\Requests\Api\V1\Front\Purchase;

use App\Http\Requests\Api\V1\Front\BaseRequest;

class DestroyMemberCreditCardRequest extends BaseRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        $id = $this->route('id');

        $memberCreditCard = \App\Models\MemberCreditCard::where([
            'id' => $id,
            'member_id' => auth('api')->id(),
        ])->first();

        return !empty($memberCreditCard);
    }
}
