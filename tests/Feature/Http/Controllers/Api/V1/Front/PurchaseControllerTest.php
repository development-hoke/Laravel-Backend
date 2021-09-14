<?php

namespace Tests\Feature\Http\Controllers\Api\V1\Front;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(\PrefsTableSeeder::class);
    }

    /**
     * お客様情報編集テスト
     */
    public function testConfirmMember()
    {
        $data = [
            'lname' => '田中',
            'fname' => '太郎',
            'lkana' => 'タナカ',
            'fkana' => 'タロウ',
            'tel' => '0300000000',
            'zip' => '1638001',
            'pref_id' => 13,
            'city' => '中央区',
            'town' => '東日本橋',
            'address' => '1-6-9',
            'building' => 'グリーンパーク東日本橋２ ２０１',
        ];
        $response = $this->post('api/v1/purchase/confirm_member', $data);
        $response->assertStatus(200);
        $response->assertJson($data);
    }
}
