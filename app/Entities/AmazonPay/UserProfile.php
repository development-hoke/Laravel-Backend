<?php

namespace App\Entities\AmazonPay;

use App\Entities\Entity;

/**
 * @see https://developer.amazon.com/ja/docs/amazon-pay-automatic/obtain-profile-information.html
 *
 * @property string $name 名前または会社名
 * @property string $email 名前または会社名
 * @property string $user_id 名前または会社名
 */
class UserProfile extends Entity
{
}
