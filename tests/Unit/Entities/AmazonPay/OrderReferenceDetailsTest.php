<?php

namespace Tests\Unit\AmazonPay;

use App\Entities\AmazonPay\OrderReferenceDetails;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrderReferenceDetailsTest extends TestCase
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
        \App\Models\Pref::truncate();
    }

    public function testConstruct()
    {
        Artisan::call('db:seed', ['--class' => 'PrefsTableSeeder']);

        $now = Carbon::now();
        $pref = \App\Models\Pref::where('name', '北海道')->get()->first();

        $o = new OrderReferenceDetails([
            'AmazonOrderReferenceId' => 'AmazonOrderReferenceId',
            'Buyer' => [
                'Name' => 'BuyerName',
                'Email' => 'BuyerEmail',
                'Phone' => 'BuyerPhone',
            ],
            'OrderTotal' => [
                'CurrencyCode' => 'JPY',
                'Amount' => '1100',
            ],
            'SellerNote' => 'SellerNote',
            'Destination' => [
                'DestinationType' => 'DestinationType',
                'PhysicalDestination' => [
                    'Name' => 'Name',
                    'AddressLine1' => 'AddressLine1',
                    'StateOrRegion' => $pref->name,
                ],
            ],
            'SellerOrderAttributes' => [
                'SellerOrderId' => 'SellerOrderId',
                'StoreName' => 'StoreName',
                'CustomInformation' => 'CustomInformation',
            ],
            'OrderReferenceStatus' => [
                'state' => 'state',
                'LastUpdateTimestamp' => $now->subDays(1)->timestamp,
                'ReasonCode' => 'ReasonCode',
                'ReasonDescription' => 'ReasonDescription',
            ],
            'Constraints' => [
                [
                    'ConstraintId' => 'ConstraintId',
                    'Description' => 'Description',
                ],
            ],
            'CreationTimestamp' => $now->timestamp,
            'ExpirationTimestamp' => $now->addDays(1)->timestamp,
        ]);

        $this->assertEquals('AmazonOrderReferenceId', $o->amazon_order_reference_id);
        $this->assertEquals('BuyerName', $o->buyer->name);
        $this->assertEquals('BuyerEmail', $o->buyer->email);
        $this->assertEquals('BuyerPhone', $o->buyer->phone);
        $this->assertEquals('JPY', $o->order_total->currency_code);
        $this->assertTrue(1100 === $o->order_total->amount);
        $this->assertEquals('SellerNote', $o->seller_note);
        $this->assertEquals('DestinationType', $o->destination->destination_type);
        $this->assertEquals('Name', $o->destination->physical_destination->name);
        $this->assertEquals('AddressLine1', $o->destination->physical_destination->address_line1);
        $this->assertEquals($pref->name, $o->destination->physical_destination->state_or_region);
        $this->assertEquals($pref->name, $o->destination->physical_destination->pref_name);
        $this->assertEquals($pref->id, $o->destination->physical_destination->pref_id);
        $this->assertEquals('SellerOrderId', $o->seller_order_attributes->seller_order_id);
        $this->assertEquals('StoreName', $o->seller_order_attributes->store_name);
        $this->assertEquals('CustomInformation', $o->seller_order_attributes->custom_information);
        $this->assertEquals('state', $o->order_reference_status->state);
        $this->assertTrue($o->order_reference_status->last_update_timestamp instanceof \Carbon\Carbon);
        $this->assertEquals($now->subDays(1)->timestamp, $o->order_reference_status->last_update_timestamp->timestamp);
        $this->assertEquals('ReasonCode', $o->order_reference_status->reason_code);
        $this->assertEquals('ReasonDescription', $o->order_reference_status->reason_description);
        $this->assertTrue($o->constraints instanceof \App\Entities\Collection);
        $this->assertEquals(1, $o->constraints->count());
        $this->assertEquals('ConstraintId', $o->constraints->first()->constraint_id);
        $this->assertEquals('Description', $o->constraints->first()->description);
        $this->assertTrue($o->creation_timestamp instanceof \Carbon\Carbon);
        $this->assertEquals($now->timestamp, $o->creation_timestamp->timestamp);
        $this->assertTrue($o->expiration_timestamp instanceof \Carbon\Carbon);
        $this->assertEquals($now->addDays(1)->timestamp, $o->expiration_timestamp->timestamp);
    }
}
