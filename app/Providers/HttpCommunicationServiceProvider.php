<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class HttpCommunicationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Ymdy
        if (config('http_communication.use_mock.ymdy', false)) {
            $this->app->bind(\App\HttpCommunication\Ymdy\AdminAuthInterface::class, \App\HttpCommunication\Ymdy\Mock\AdminAuth::class);
            $this->app->bind(\App\HttpCommunication\Ymdy\MemberInterface::class, \App\HttpCommunication\Ymdy\Mock\Member::class);
            $this->app->bind(\App\HttpCommunication\Ymdy\PurchaseInterface::class, \App\HttpCommunication\Ymdy\Mock\Purchase::class);
            $this->app->bind(\App\HttpCommunication\Ymdy\MemberShippingAddressInterface::class, \App\HttpCommunication\Ymdy\Mock\MemberShippingAddress::class);
            $this->app->bind(\App\HttpCommunication\Ymdy\OldMemberInterface::class, \App\HttpCommunication\Ymdy\Mock\OldMember::class);
        } else {
            $this->app->bind(\App\HttpCommunication\Ymdy\AdminAuthInterface::class, \App\HttpCommunication\Ymdy\Concrete\AdminAuth::class);
            $this->app->bind(\App\HttpCommunication\Ymdy\MemberInterface::class, \App\HttpCommunication\Ymdy\Concrete\Member::class);
            $this->app->bind(\App\HttpCommunication\Ymdy\PurchaseInterface::class, \App\HttpCommunication\Ymdy\Concrete\Purchase::class);
            $this->app->bind(\App\HttpCommunication\Ymdy\DestinationInterface::class, \App\HttpCommunication\Ymdy\Concrete\Destination::class);
            $this->app->bind(\App\HttpCommunication\Ymdy\MemberShippingAddressInterface::class, \App\HttpCommunication\Ymdy\Concrete\MemberShippingAddress::class);
            $this->app->bind(\App\HttpCommunication\Ymdy\OldMemberInterface::class, \App\HttpCommunication\Ymdy\Concrete\OldMember::class);
        }

        // Ymdy経営基幹
        if (config('http_communication.use_mock.ymdy_keiei', false)) {
            $this->app->bind(\App\HttpCommunication\Ymdy\KeieiInterface::class, \App\HttpCommunication\Ymdy\Mock\Keiei::class);
        } else {
            $this->app->bind(\App\HttpCommunication\Ymdy\KeieiInterface::class, \App\HttpCommunication\Ymdy\Concrete\Keiei::class);
        }

        // SendGrid
        if (config('http_communication.use_mock.send_grid', false)) {
            $this->app->bind(\App\HttpCommunication\SendGrid\SendGridServiceInterface::class, \App\HttpCommunication\SendGrid\Mock\SendGridService::class);
        } else {
            $this->app->bind(\App\HttpCommunication\SendGrid\SendGridServiceInterface::class, \App\HttpCommunication\SendGrid\Concrete\SendGridService::class);
        }

        // FRegi
        if (config('http_communication.use_mock.f_regi', false)) {
            $this->app->bind(\App\HttpCommunication\FRegi\PurchaseInterface::class, \App\HttpCommunication\FRegi\Mock\Purchase::class);
        } else {
            $this->app->bind(\App\HttpCommunication\FRegi\PurchaseInterface::class, \App\HttpCommunication\FRegi\Concrete\Purchase::class);
        }

        // NP後払い
        if (config('http_communication.use_mock.np', false)) {
            $this->app->bind(\App\HttpCommunication\NP\PurchaseInterface::class, \App\HttpCommunication\NP\Mock\Purchase::class);
        } else {
            $this->app->bind(\App\HttpCommunication\NP\PurchaseInterface::class, \App\HttpCommunication\NP\Concrete\Purchase::class);
        }

        // Shohin
        if (config('http_communication.use_mock.shohin', false)) {
            $this->app->bind(\App\HttpCommunication\Shohin\ItemInterface::class, \App\HttpCommunication\Shohin\Mock\Item::class);
        } else {
            $this->app->bind(\App\HttpCommunication\Shohin\ItemInterface::class, \App\HttpCommunication\Shohin\Concrete\Item::class);
        }

        // スタッフスタート
        if (config('http_communication.use_mock.staff_start', false)) {
            $this->app->bind(\App\HttpCommunication\StaffStart\StaffStartInterface::class, \App\HttpCommunication\StaffStart\Mock\StaffStart::class);
        } else {
            $this->app->bind(\App\HttpCommunication\StaffStart\StaffStartInterface::class, \App\HttpCommunication\StaffStart\Concrete\StaffStart::class);
        }

        // Amazon Pay
        if (config('http_communication.use_mock.amazon_pay', false)) {
            $this->app->bind(\App\HttpCommunication\AmazonPay\HttpCommunication::class, \App\HttpCommunication\AmazonPay\Mock\HttpCommunication::class);
            $this->app->bind(\App\HttpCommunication\AmazonPay\IpnReciever::class, \App\HttpCommunication\AmazonPay\Mock\IpnReciever::class);
        } else {
            $this->app->bind(\App\HttpCommunication\AmazonPay\HttpCommunication::class, \App\HttpCommunication\AmazonPay\Concrete\HttpCommunication::class);
            $this->app->bind(\App\HttpCommunication\AmazonPay\IpnReciever::class, \App\HttpCommunication\AmazonPay\Concrete\IpnReciever::class);
        }
    }
}
