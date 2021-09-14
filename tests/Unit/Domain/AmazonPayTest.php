<?php

namespace Tests\Unit\Domain;

use App\Domain\AmazonPay;
use App\Repositories\AmazonPay\AuthorizationRepository;
use App\Repositories\AmazonPay\CaptureRepository;
use App\Repositories\AmazonPay\OrderRepository as AmazonPayOrderRepository;
use App\Repositories\AmazonPay\RefundRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\Mocks\Domain\Adapters\AmazonPayAdapter;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

class CouponTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->truncateTables();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function tearDown(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->truncateTables();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        parent::tearDown();
    }

    public function truncateTables()
    {
        \App\Models\AmazonPayOrder::truncate();
        \App\Models\AmazonPayAuthorization::truncate();
        \App\Models\AmazonPayCapture::truncate();
        \App\Models\AmazonPayRefund::truncate();
    }

    public function provideTestCapture()
    {
        return [
            'オーソリ1件' => [
                'params' => [
                    'authorizations' => [
                        ['amount' => 2000, 'capturing_amount' => 2000, 'authorization_reference_id' => '01', 'amazon_authorization_id' => '01'],
                    ],
                    'get_authorization_details_results' => [
                        '01' => [
                            'amazon_authorization_id' => '01',
                            'authorization_reference_id' => '01',
                            'authorization_amount' => ['amount' => 2000],
                            'capture_amount' => ['amount' => 0],
                            'authorization_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'expiration_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'soft_decline' => false,
                            'authorization_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Authorization::Open,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                    'capture_results' => [
                        '01' => [
                            'amazon_capture_id' => '11',
                            'capture_reference_id' => '11',
                            'capture_amount' => ['amount' => 2000],
                            'refund_amount' => ['amount' => 0],
                            'capture_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'capture_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Capture::Completed,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
                'expected' => [
                    'authorizations' => [
                        ['amazon_authorization_id' => '01', 'status' => \App\Enums\AmazonPay\Status\Authorization::Closed],
                    ],
                    'captures' => [
                        ['authorization_reference_id' => '01', 'amount' => 2000],
                    ],
                    'close_authorize_params' => [],
                ],
            ],
            'オーソリ1件 期限切れ' => [
                'params' => [
                    'authorizations' => [
                        ['amount' => 2000, 'capturing_amount' => 2000, 'authorization_reference_id' => '01', 'amazon_authorization_id' => '01'],
                    ],
                    'get_authorization_details_results' => [
                        '01' => [
                            'amazon_authorization_id' => '01',
                            'authorization_reference_id' => '01',
                            'authorization_amount' => ['amount' => 2000],
                            'capture_amount' => ['amount' => 0],
                            'authorization_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'expiration_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'soft_decline' => false,
                            'authorization_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Authorization::Closed,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                    'authorize_results' => [
                        '21' => [
                            'amazon_authorization_id' => '02',
                            'authorization_reference_id' => '02',
                            'authorization_amount' => ['amount' => 2000],
                            'capture_amount' => ['amount' => 0],
                            'authorization_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'expiration_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'soft_decline' => false,
                            'authorization_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Authorization::Open,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                    'capture_results' => [
                        '02' => [
                            'amazon_capture_id' => '11',
                            'capture_reference_id' => '11',
                            'capture_amount' => ['amount' => 2000],
                            'refund_amount' => ['amount' => 0],
                            'capture_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'capture_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Capture::Completed,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
                'expected' => [
                    'authorizations' => [
                        ['amazon_authorization_id' => '01', 'status' => \App\Enums\AmazonPay\Status\Authorization::Closed],
                        ['amazon_authorization_id' => '02', 'status' => \App\Enums\AmazonPay\Status\Authorization::Closed],
                    ],
                    'captures' => [
                        ['authorization_reference_id' => '02', 'amount' => 2000],
                    ],
                    'close_authorize_params' => [],
                ],
            ],
            'オーソリ1件 一部キャンセル' => [
                'params' => [
                    'authorizations' => [
                        ['amount' => 2000, 'capturing_amount' => 1600, 'authorization_reference_id' => '01', 'amazon_authorization_id' => '01'],
                    ],
                    'get_authorization_details_results' => [
                        '01' => [
                            'amazon_authorization_id' => '01',
                            'authorization_reference_id' => '01',
                            'authorization_amount' => ['amount' => 2000],
                            'capture_amount' => ['amount' => 0],
                            'authorization_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'expiration_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'soft_decline' => false,
                            'authorization_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Authorization::Open,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                    'capture_results' => [
                        '01' => [
                            'amazon_capture_id' => '11',
                            'capture_reference_id' => '11',
                            'capture_amount' => ['amount' => 1600],
                            'refund_amount' => ['amount' => 0],
                            'capture_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'capture_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Capture::Completed,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
                'expected' => [
                    'authorizations' => [
                        ['amazon_authorization_id' => '01', 'status' => \App\Enums\AmazonPay\Status\Authorization::Closed],
                    ],
                    'captures' => [
                        ['authorization_reference_id' => '01', 'amount' => 1600],
                    ],
                    'close_authorize_params' => ['01'],
                ],
            ],
            'オーソリ2件 (商品追加)' => [
                'params' => [
                    'authorizations' => [
                        ['amount' => 2000, 'capturing_amount' => 2000, 'authorization_reference_id' => '01', 'amazon_authorization_id' => '01'],
                        ['amount' => 100, 'capturing_amount' => 100, 'authorization_reference_id' => '02', 'amazon_authorization_id' => '02'],
                    ],
                    'get_authorization_details_results' => [
                        '01' => [
                            'amazon_authorization_id' => '01',
                            'authorization_reference_id' => '01',
                            'authorization_amount' => ['amount' => 2000],
                            'capture_amount' => ['amount' => 0],
                            'authorization_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'expiration_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'soft_decline' => false,
                            'authorization_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Authorization::Open,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                        '02' => [
                            'amazon_authorization_id' => '01',
                            'authorization_reference_id' => '01',
                            'authorization_amount' => ['amount' => 100],
                            'capture_amount' => ['amount' => 0],
                            'authorization_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'expiration_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'soft_decline' => false,
                            'authorization_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Authorization::Open,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                    'capture_results' => [
                        '01' => [
                            'amazon_capture_id' => '11',
                            'capture_reference_id' => '11',
                            'capture_amount' => ['amount' => 2000],
                            'refund_amount' => ['amount' => 0],
                            'capture_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'capture_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Capture::Completed,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                        '02' => [
                            'amazon_capture_id' => '12',
                            'capture_reference_id' => '12',
                            'capture_amount' => ['amount' => 100],
                            'refund_amount' => ['amount' => 0],
                            'capture_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'capture_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Capture::Completed,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
                'expected' => [
                    'authorizations' => [
                        ['amazon_authorization_id' => '01', 'status' => \App\Enums\AmazonPay\Status\Authorization::Closed],
                        ['amazon_authorization_id' => '02', 'status' => \App\Enums\AmazonPay\Status\Authorization::Closed],
                    ],
                    'captures' => [
                        ['authorization_reference_id' => '01', 'amount' => 2000],
                        ['authorization_reference_id' => '02', 'amount' => 100],
                    ],
                    'close_authorize_params' => [],
                ],
            ],
            'オーソリ2件 (商品追加後に最初にキャンセル)' => [
                'params' => [
                    'authorizations' => [
                        ['amount' => 2000, 'capturing_amount' => 2000, 'authorization_reference_id' => '01', 'amazon_authorization_id' => '01'],
                        ['amount' => 100, 'capturing_amount' => 0, 'authorization_reference_id' => '02', 'amazon_authorization_id' => '02'],
                    ],
                    'get_authorization_details_results' => [
                        '01' => [
                            'amazon_authorization_id' => '01',
                            'authorization_reference_id' => '01',
                            'authorization_amount' => ['amount' => 2000],
                            'capture_amount' => ['amount' => 0],
                            'authorization_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'expiration_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'soft_decline' => false,
                            'authorization_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Authorization::Open,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                        '02' => [
                            'amazon_authorization_id' => '01',
                            'authorization_reference_id' => '01',
                            'authorization_amount' => ['amount' => 100],
                            'capture_amount' => ['amount' => 0],
                            'authorization_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'expiration_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'soft_decline' => false,
                            'authorization_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Authorization::Open,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                    'capture_results' => [
                        '01' => [
                            'amazon_capture_id' => '11',
                            'capture_reference_id' => '11',
                            'capture_amount' => ['amount' => 2000],
                            'refund_amount' => ['amount' => 0],
                            'capture_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'capture_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Capture::Completed,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
                'expected' => [
                    'authorizations' => [
                        ['amazon_authorization_id' => '01', 'status' => \App\Enums\AmazonPay\Status\Authorization::Closed],
                        ['amazon_authorization_id' => '02', 'status' => \App\Enums\AmazonPay\Status\Authorization::Closed],
                    ],
                    'captures' => [
                        ['authorization_reference_id' => '01', 'amount' => 2000],
                    ],
                    'close_authorize_params' => ['02'],
                ],
            ],
            'オーソリ2件 (商品追加後に最初にキャンセルクローズ済み)' => [
                'params' => [
                    'authorizations' => [
                        ['amount' => 2000, 'capturing_amount' => 2000, 'authorization_reference_id' => '01', 'amazon_authorization_id' => '01'],
                        ['amount' => 100, 'capturing_amount' => 0, 'authorization_reference_id' => '02', 'amazon_authorization_id' => '02', 'status' => \App\Enums\AmazonPay\Status\Authorization::Closed],
                    ],
                    'get_authorization_details_results' => [
                        '01' => [
                            'amazon_authorization_id' => '01',
                            'authorization_reference_id' => '01',
                            'authorization_amount' => ['amount' => 2000],
                            'capture_amount' => ['amount' => 0],
                            'authorization_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'expiration_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'soft_decline' => false,
                            'authorization_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Authorization::Open,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                        '02' => [
                            'amazon_authorization_id' => '01',
                            'authorization_reference_id' => '01',
                            'authorization_amount' => ['amount' => 100],
                            'capture_amount' => ['amount' => 0],
                            'authorization_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'expiration_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'soft_decline' => false,
                            'authorization_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Authorization::Closed,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                    'capture_results' => [
                        '01' => [
                            'amazon_capture_id' => '11',
                            'capture_reference_id' => '11',
                            'capture_amount' => ['amount' => 2000],
                            'refund_amount' => ['amount' => 0],
                            'capture_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'capture_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Capture::Completed,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
                'expected' => [
                    'authorizations' => [
                        ['amazon_authorization_id' => '01', 'status' => \App\Enums\AmazonPay\Status\Authorization::Closed],
                        ['amazon_authorization_id' => '02', 'status' => \App\Enums\AmazonPay\Status\Authorization::Closed],
                    ],
                    'captures' => [
                        ['authorization_reference_id' => '01', 'amount' => 2000],
                    ],
                    'close_authorize_params' => [],
                ],
            ],
            'オーソリ2件 (1件キャプチャ済み)' => [
                'params' => [
                    'authorizations' => [
                        ['amount' => 1000, 'capturing_amount' => 2000, 'authorization_reference_id' => '01', 'amazon_authorization_id' => '01', 'status' => \App\Enums\AmazonPay\Status\Authorization::Closed],
                        ['amount' => 1000, 'capturing_amount' => 1000, 'authorization_reference_id' => '02', 'amazon_authorization_id' => '02'],
                    ],
                    'captures' => [
                        ['amazon_pay_authorization_id' => 1, 'amount' => 1000],
                    ],
                    'get_authorization_details_results' => [
                        '01' => [
                            'amazon_authorization_id' => '01',
                            'authorization_reference_id' => '01',
                            'authorization_amount' => ['amount' => 1000],
                            'capture_amount' => ['amount' => 0],
                            'authorization_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'expiration_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'soft_decline' => false,
                            'authorization_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Authorization::Closed,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                        '02' => [
                            'amazon_authorization_id' => '01',
                            'authorization_reference_id' => '01',
                            'authorization_amount' => ['amount' => 1000],
                            'capture_amount' => ['amount' => 0],
                            'authorization_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'expiration_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'soft_decline' => false,
                            'authorization_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Authorization::Open,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                    'capture_results' => [
                        '02' => [
                            'amazon_capture_id' => '12',
                            'capture_reference_id' => '12',
                            'capture_amount' => ['amount' => 1000],
                            'refund_amount' => ['amount' => 0],
                            'capture_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'capture_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Capture::Completed,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
                'expected' => [
                    'authorizations' => [
                        ['amazon_authorization_id' => '01', 'status' => \App\Enums\AmazonPay\Status\Authorization::Closed],
                        ['amazon_authorization_id' => '02', 'status' => \App\Enums\AmazonPay\Status\Authorization::Closed],
                    ],
                    'captures' => [
                        ['authorization_reference_id' => '02', 'amount' => 1000],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array $params
     * @param array $expected
     *
     * @return void
     *
     * @dataProvider provideTestCapture
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testCapture(array $params, array $expected)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $amazonPayOrder = \App\Models\AmazonPayOrder::create([
            'order_id' => 1,
            'order_reference_id' => '21',
            'status' => \App\Enums\AmazonPay\Status\OrderReference::Open,
            'amount' => 2000,
            'expiration_at' => Carbon::now()->addDays(7),
        ], $params['order'] ?? []);

        foreach ((array) $params['authorizations'] as $authorization) {
            \App\Models\AmazonPayAuthorization::create(array_merge([
                'amazon_pay_order_id' => $amazonPayOrder->id,
                'status' => \App\Enums\AmazonPay\Status\Authorization::Open,
                'amount' => 2000,
                'capturing_amount' => 2000,
                'fee' => 0,
                'expiration_at' => Carbon::now()->addDays(7),
            ], $authorization));
        }

        foreach (($params['captures'] ?? []) as $capture) {
            \App\Models\AmazonPayCapture::create(array_merge([
                'amazon_pay_authorization_id' => $capture['amazon_pay_authorization_id'],
                'capture_reference_id' => str_replace('-', '', Uuid::generate(4)),
                'amazon_capture_id' => str_replace('-', '', Uuid::generate(4)),
                'status' => \App\Enums\AmazonPay\Status\Capture::Completed,
                'amount' => 2000,
                'capturing_amount' => 2000,
                'fee' => 0,
            ], $capture));
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $adapter = new AmazonPayAdapter();
        $adapter->getAuthorizationDetailsResults = $params['get_authorization_details_results'] ?? [];
        $adapter->captureResults = $params['capture_results'] ?? [];
        $adapter->authorizeResults = $params['authorize_results'] ?? [];

        $amazonPay = new AmazonPay(
            $adapter,
            resolve(AmazonPayOrderRepository::class),
            resolve(AuthorizationRepository::class),
            resolve(CaptureRepository::class),
            resolve(RefundRepository::class)
        );

        $results = $amazonPay->capture(1);

        if (!empty($results['failed_reports'])) {
            throw $results['failed_reports'][0]['exception'];
        }

        $this->assertEquals(0, count($results['failed_reports']));
        $this->assertEquals(count($expected['captures']), count($results['stored']));

        foreach ($expected['captures'] as $i => $p) {
            $result = $results['stored'][$i];
            $amazonPayAuthorization = \App\Models\AmazonPayAuthorization::withTrashed()->find($result->amazon_pay_authorization_id);
            $this->assertEquals($p['authorization_reference_id'], $amazonPayAuthorization->authorization_reference_id);
            $this->assertEquals($p['amount'], $result->amount);

            $amazonPayAuthorization = \App\Models\AmazonPayAuthorization::withTrashed()->where('authorization_reference_id', $p['authorization_reference_id'])->first();
            $amazonPayCapture = \App\Models\AmazonPayCapture::where('amazon_pay_authorization_id', $amazonPayAuthorization->id)->first();

            $this->assertTrue(isset($amazonPayCapture));
            $this->assertEquals($p['amount'], $amazonPayCapture->amount);

            [$amazonAuthorizationId, $captureReferenceId, $captureAmount] = $adapter->captureRequestParams[$i];

            $this->assertEquals($p['authorization_reference_id'], $amazonAuthorizationId);
            $this->assertEquals($p['amount'], $captureAmount);
        }

        foreach ($expected['authorizations'] as $i => $p) {
            $amazonPayAuthorization = \App\Models\AmazonPayAuthorization::withTrashed()->where('amazon_authorization_id', $p['amazon_authorization_id'])->first();
            $this->assertEquals($p['status'], $amazonPayAuthorization->status);
        }

        $this->assertEquals(count($expected['close_authorize_params'] ?? []), count($adapter->closeAuthorizationRequestParams));

        foreach (($expected['close_authorize_params'] ?? []) as $closedId) {
            foreach ($adapter->closeAuthorizationRequestParams as $requestParams) {
                if ($closedId === $requestParams[0]) {
                    continue 2;
                }
            }
            $this->fail();
        }
    }

    public function provideTestChangeAuthorizationAmount()
    {
        return [
            '金額加算 (追加オーソリ1)' => [
                'params' => [
                    'price' => 2100,
                    'order' => ['amount' => 2000, 'order_reference_id' => '21'],
                    'authorizations' => [
                        ['amount' => 2000, 'capturing_amount' => 2000, 'authorization_reference_id' => '01', 'amazon_authorization_id' => '01'],
                    ],
                    'authorize_results' => [
                        '21' => [
                            'amazon_authorization_id' => '02',
                            'authorization_reference_id' => '02',
                            'authorization_amount' => ['amount' => 100],
                            'capture_amount' => ['amount' => 0],
                            'authorization_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'expiration_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'soft_decline' => false,
                            'authorization_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Authorization::Open,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
                'expected' => [
                    'authorizations' => [
                        ['amazon_authorization_id' => '01', 'status' => \App\Enums\AmazonPay\Status\Authorization::Open, 'capturing_amount' => 2000, 'amount' => 2000],
                        ['amazon_authorization_id' => '02', 'status' => \App\Enums\AmazonPay\Status\Authorization::Open, 'capturing_amount' => 100, 'amount' => 100],
                    ],
                    'authorize_requests' => [
                        ['order_reference_id' => '21', 'amount' => 100],
                    ],
                ],
            ],
            '金額加算 (追加オーソリ + 金額加算)' => [
                'params' => [
                    'price' => 2100,
                    'order' => ['amount' => 2000, 'order_reference_id' => '21'],
                    'authorizations' => [
                        ['amount' => 2000, 'capturing_amount' => 1800, 'authorization_reference_id' => '01', 'amazon_authorization_id' => '01'],
                    ],
                    'authorize_results' => [
                        '21' => [
                            'amazon_authorization_id' => '02',
                            'authorization_reference_id' => '02',
                            'authorization_amount' => ['amount' => 100],
                            'capture_amount' => ['amount' => 0],
                            'authorization_fee' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'expiration_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'soft_decline' => false,
                            'authorization_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Authorization::Open,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
                'expected' => [
                    'authorizations' => [
                        ['amazon_authorization_id' => '01', 'status' => \App\Enums\AmazonPay\Status\Authorization::Open, 'capturing_amount' => 2000, 'amount' => 2000],
                        ['amazon_authorization_id' => '02', 'status' => \App\Enums\AmazonPay\Status\Authorization::Open, 'capturing_amount' => 100, 'amount' => 100],
                    ],
                    'authorize_requests' => [
                        ['order_reference_id' => '21', 'amount' => 100],
                    ],
                ],
            ],
            '金額加算 (金額加算)' => [
                'params' => [
                    'price' => 3900,
                    'order' => ['amount' => 4000, 'order_reference_id' => '21'],
                    'authorizations' => [
                        ['amount' => 2000, 'capturing_amount' => 1800, 'authorization_reference_id' => '01', 'amazon_authorization_id' => '01'],
                        ['amount' => 2000, 'capturing_amount' => 1800, 'authorization_reference_id' => '02', 'amazon_authorization_id' => '02'],
                    ],
                ],
                'expected' => [
                    'authorizations' => [
                        ['amazon_authorization_id' => '01', 'status' => \App\Enums\AmazonPay\Status\Authorization::Open, 'capturing_amount' => 2000, 'amount' => 2000],
                        ['amazon_authorization_id' => '02', 'status' => \App\Enums\AmazonPay\Status\Authorization::Open, 'capturing_amount' => 1900, 'amount' => 2000],
                    ],
                ],
            ],
            '金額加算 (金額減算)' => [
                'params' => [
                    'price' => 1800,
                    'order' => ['amount' => 2000, 'order_reference_id' => '21'],
                    'authorizations' => [
                        ['amount' => 2000, 'capturing_amount' => 2000, 'authorization_reference_id' => '01', 'amazon_authorization_id' => '01'],
                    ],
                ],
                'expected' => [
                    'authorizations' => [
                        ['amazon_authorization_id' => '01', 'status' => \App\Enums\AmazonPay\Status\Authorization::Open, 'capturing_amount' => 1800, 'amount' => 2000],
                    ],
                ],
            ],
            '金額加算 (金額減算 複数件)' => [
                'params' => [
                    'price' => 1800,
                    'order' => ['amount' => 4000, 'order_reference_id' => '21'],
                    'authorizations' => [
                        ['amount' => 2000, 'capturing_amount' => 2000, 'authorization_reference_id' => '01', 'amazon_authorization_id' => '01'],
                        ['amount' => 2000, 'capturing_amount' => 2000, 'authorization_reference_id' => '02', 'amazon_authorization_id' => '02'],
                    ],
                ],
                'expected' => [
                    'authorizations' => [
                        ['amazon_authorization_id' => '01', 'status' => \App\Enums\AmazonPay\Status\Authorization::Open, 'capturing_amount' => 0, 'amount' => 2000],
                        ['amazon_authorization_id' => '02', 'status' => \App\Enums\AmazonPay\Status\Authorization::Open, 'capturing_amount' => 1800, 'amount' => 2000],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array $params
     * @param array $expected
     *
     * @return void
     *
     * @dataProvider provideTestChangeAuthorizationAmount
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testChangeAuthorizationAmount(array $params, array $expected)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $amazonPayOrder = \App\Models\AmazonPayOrder::create([
            'order_id' => 1,
            'order_reference_id' => '21',
            'status' => \App\Enums\AmazonPay\Status\OrderReference::Open,
            'amount' => 2000,
            'expiration_at' => Carbon::now()->addDays(7),
        ], $params['order'] ?? []);

        foreach ((array) $params['authorizations'] as $authorization) {
            \App\Models\AmazonPayAuthorization::create(array_merge([
                'amazon_pay_order_id' => $amazonPayOrder->id,
                'status' => \App\Enums\AmazonPay\Status\Authorization::Open,
                'amount' => 2000,
                'capturing_amount' => 2000,
                'fee' => 0,
                'expiration_at' => Carbon::now()->addDays(7),
            ], $authorization));
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $adapter = new AmazonPayAdapter();
        $adapter->authorizeResults = $params['authorize_results'] ?? [];

        $amazonPay = new AmazonPay(
            $adapter,
            resolve(AmazonPayOrderRepository::class),
            resolve(AuthorizationRepository::class),
            resolve(CaptureRepository::class),
            resolve(RefundRepository::class)
        );

        $amazonPay->changeAuthorizationAmount(1, $params['price']);

        foreach ($expected['authorizations'] as $i => $p) {
            $amazonPayAuthorization = \App\Models\AmazonPayAuthorization::withTrashed()->where('amazon_authorization_id', $p['amazon_authorization_id'])->firstOrFail();
            $this->assertEquals($p['status'], $amazonPayAuthorization->status);
            $this->assertEquals($p['amount'], $amazonPayAuthorization->amount);
            $this->assertEquals($p['capturing_amount'], $amazonPayAuthorization->capturing_amount);
        }

        $this->assertEquals(count($expected['authorize_requests'] ?? []), count($adapter->authorizeRequestParams));

        foreach (($expected['authorize_requests'] ?? []) as $i => $p) {
            [$orderReferenceId, $authorizationReferenceId, $amount] = $adapter->authorizeRequestParams[$i];
            $this->assertEquals($p['order_reference_id'], $orderReferenceId);
            $this->assertEquals($p['amount'], $amount);
        }
    }

    public function provideTestRefund()
    {
        return [
            '1. 売上1件 全額返金' => [
                'params' => [
                    'refund_amount' => 2000,
                    'order' => ['amount' => 2000, 'order_reference_id' => '21'],
                    'authorizations' => [
                        ['amount' => 2000, 'capturing_amount' => 2000, 'authorization_reference_id' => '01', 'amazon_authorization_id' => '01'],
                    ],
                    'captures' => [
                        ['amount' => 2000, 'amazon_pay_authorization_id' => 1, 'capture_reference_id' => '11', 'amazon_capture_id' => '11'],
                    ],
                    'refund_results' => [
                        '11' => [
                            'amazon_refund_id' => '31',
                            'refund_reference_id' => '31',
                            'refund_amount' => ['amount' => 2000],
                            'fee_refunded' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'refund_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Refund::Pending,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
                'expected' => [
                    'refunds' => [
                        ['amazon_refund_id' => '31', 'status' => \App\Enums\AmazonPay\Status\Refund::Pending, 'amount' => 2000],
                    ],
                    'refund_requests' => [
                        ['amazon_capture_id' => '11', 'amount' => 2000],
                    ],
                ],
            ],
            '2. 売上1件 一部返金' => [
                'params' => [
                    'refund_amount' => 1000,
                    'order' => ['amount' => 2000, 'order_reference_id' => '21'],
                    'authorizations' => [
                        ['amount' => 2000, 'capturing_amount' => 2000, 'authorization_reference_id' => '01', 'amazon_authorization_id' => '01'],
                    ],
                    'captures' => [
                        ['amount' => 2000, 'amazon_pay_authorization_id' => 1, 'capture_reference_id' => '11', 'amazon_capture_id' => '11'],
                    ],
                    'refund_results' => [
                        '11' => [
                            'amazon_refund_id' => '31',
                            'refund_reference_id' => '31',
                            'refund_amount' => ['amount' => 1000],
                            'fee_refunded' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'refund_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Refund::Pending,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
                'expected' => [
                    'refunds' => [
                        ['amazon_refund_id' => '31', 'status' => \App\Enums\AmazonPay\Status\Refund::Pending, 'amount' => 1000],
                    ],
                    'refund_requests' => [
                        ['amazon_capture_id' => '11', 'amount' => 1000],
                    ],
                ],
            ],
            '3. 売上2件 全返金' => [
                'params' => [
                    'refund_amount' => 2200,
                    'order' => ['amount' => 2000, 'order_reference_id' => '21'],
                    'authorizations' => [
                        ['amount' => 2000, 'capturing_amount' => 2000, 'authorization_reference_id' => '01', 'amazon_authorization_id' => '01'],
                        ['amount' => 200, 'capturing_amount' => 200, 'authorization_reference_id' => '02', 'amazon_authorization_id' => '02'],
                    ],
                    'captures' => [
                        ['amount' => 2000, 'amazon_pay_authorization_id' => 1, 'capture_reference_id' => '11', 'amazon_capture_id' => '11'],
                        ['amount' => 200, 'amazon_pay_authorization_id' => 2, 'capture_reference_id' => '12', 'amazon_capture_id' => '12'],
                    ],
                    'refund_results' => [
                        '11' => [
                            'amazon_refund_id' => '31',
                            'refund_reference_id' => '31',
                            'refund_amount' => ['amount' => 2000],
                            'fee_refunded' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'refund_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Refund::Pending,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                        '12' => [
                            'amazon_refund_id' => '32',
                            'refund_reference_id' => '32',
                            'refund_amount' => ['amount' => 200],
                            'fee_refunded' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'refund_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Refund::Pending,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
                'expected' => [
                    'refunds' => [
                        ['amazon_refund_id' => '31', 'status' => \App\Enums\AmazonPay\Status\Refund::Pending, 'amount' => 2000],
                        ['amazon_refund_id' => '32', 'status' => \App\Enums\AmazonPay\Status\Refund::Pending, 'amount' => 200],
                    ],
                    'refund_requests' => [
                        ['amazon_capture_id' => '11', 'amount' => 2000],
                        ['amazon_capture_id' => '12', 'amount' => 200],
                    ],
                ],
            ],
            '4. 売上2件 一部返金' => [
                'params' => [
                    'refund_amount' => 2100,
                    'order' => ['amount' => 2000, 'order_reference_id' => '21'],
                    'authorizations' => [
                        ['amount' => 2000, 'capturing_amount' => 2000, 'authorization_reference_id' => '01', 'amazon_authorization_id' => '01'],
                        ['amount' => 200, 'capturing_amount' => 200, 'authorization_reference_id' => '02', 'amazon_authorization_id' => '02'],
                    ],
                    'captures' => [
                        ['amount' => 2000, 'amazon_pay_authorization_id' => 1, 'capture_reference_id' => '11', 'amazon_capture_id' => '11'],
                        ['amount' => 200, 'amazon_pay_authorization_id' => 2, 'capture_reference_id' => '12', 'amazon_capture_id' => '12'],
                    ],
                    'refund_results' => [
                        '11' => [
                            'amazon_refund_id' => '31',
                            'refund_reference_id' => '31',
                            'refund_amount' => ['amount' => 2000],
                            'fee_refunded' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'refund_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Refund::Pending,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                        '12' => [
                            'amazon_refund_id' => '32',
                            'refund_reference_id' => '32',
                            'refund_amount' => ['amount' => 100],
                            'fee_refunded' => ['amount' => 0],
                            'creation_timestamp' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                            'refund_status' => [
                                'state' => \App\Enums\AmazonPay\Status\Refund::Pending,
                                'last_update_timestamp' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
                'expected' => [
                    'refunds' => [
                        ['amazon_refund_id' => '31', 'status' => \App\Enums\AmazonPay\Status\Refund::Pending, 'amount' => 2000],
                        ['amazon_refund_id' => '32', 'status' => \App\Enums\AmazonPay\Status\Refund::Pending, 'amount' => 100],
                    ],
                    'refund_requests' => [
                        ['amazon_capture_id' => '11', 'amount' => 2000],
                        ['amazon_capture_id' => '12', 'amount' => 100],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array $params
     * @param array $expected
     *
     * @return void
     *
     * @dataProvider provideTestRefund
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testRefund(array $params, array $expected)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $amazonPayOrder = \App\Models\AmazonPayOrder::create([
            'order_id' => 1,
            'order_reference_id' => '21',
            'status' => \App\Enums\AmazonPay\Status\OrderReference::Open,
            'amount' => 2000,
            'expiration_at' => Carbon::now()->addDays(7),
        ], $params['order'] ?? []);

        foreach ((array) $params['authorizations'] as $authorization) {
            \App\Models\AmazonPayAuthorization::create(array_merge([
                'amazon_pay_order_id' => $amazonPayOrder->id,
                'status' => \App\Enums\AmazonPay\Status\Authorization::Open,
                'amount' => 2000,
                'capturing_amount' => 2000,
                'fee' => 0,
                'expiration_at' => Carbon::now()->addDays(7),
            ], $authorization));
        }

        foreach (($params['captures'] ?? []) as $capture) {
            \App\Models\AmazonPayCapture::create(array_merge([
                'status' => \App\Enums\AmazonPay\Status\Capture::Completed,
                'amount' => 2000,
                'capturing_amount' => 2000,
                'fee' => 0,
            ], $capture));
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $adapter = new AmazonPayAdapter();
        $adapter->refundResults = $params['refund_results'] ?? [];

        $amazonPay = new AmazonPay(
            $adapter,
            resolve(AmazonPayOrderRepository::class),
            resolve(AuthorizationRepository::class),
            resolve(CaptureRepository::class),
            resolve(RefundRepository::class)
        );

        $amazonPay->refund(1, $params['refund_amount']);

        foreach ($expected['refunds'] as $i => $p) {
            $amazonPayRefund = \App\Models\AmazonPayRefund::withTrashed()->where('amazon_refund_id', $p['amazon_refund_id'])->firstOrFail();
            $this->assertEquals($p['status'], $amazonPayRefund->status);
            $this->assertEquals($p['amount'], $amazonPayRefund->amount);
        }

        $this->assertEquals(count($expected['authorize_requests'] ?? []), count($adapter->authorizeRequestParams));

        foreach (($expected['refund_requests'] ?? []) as $i => $p) {
            [$amazonCaptureId, $refundReferenceId, $amount] = $adapter->refundRequestParams[$i];
            $this->assertEquals($p['amazon_capture_id'], $amazonCaptureId);
            $this->assertEquals($p['amount'], $amount);
        }
    }
}
