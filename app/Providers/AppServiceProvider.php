<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RepositoryServiceProvider::class);
        $this->app->register(HttpClientServiceProvider::class);
        $this->app->register(HttpCommunicationServiceProvider::class);
        $this->app->register(MacroServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Admin/Services
        $this->app->bind(\App\Services\Admin\ItemDetailServiceInterface::class, \App\Services\Admin\ItemDetailService::class);
        $this->app->bind(\App\Services\Admin\EventItemServiceInterface::class, \App\Services\Admin\EventItemService::class);
        $this->app->bind(\App\Services\Admin\EventUserServiceInterface::class, \App\Services\Admin\EventUserService::class);
        $this->app->bind(\App\Services\Admin\EventServiceInterface::class, \App\Services\Admin\EventService::class);
        $this->app->bind(\App\Services\Admin\ItemServiceInterface::class, \App\Services\Admin\ItemService::class);
        $this->app->bind(\App\Services\Admin\InformationPreviewServiceInterface::class, \App\Services\Admin\InformationPreviewService::class);
        $this->app->bind(\App\Services\Admin\ItemPreviewServiceInterface::class, \App\Services\Admin\ItemPreviewService::class);
        $this->app->bind(\App\Services\Admin\AuthServiceInterface::class, \App\Services\Admin\AuthService::class);
        $this->app->bind(\App\Services\Admin\ClosedMarketsServiceInterface::class, \App\Services\Admin\ClosedMarketsService::class);
        $this->app->bind(\App\Services\Admin\OrderDetailServiceInterface::class, \App\Services\Admin\OrderDetailService::class);
        $this->app->bind(\App\Services\Admin\OrderServiceInterface::class, \App\Services\Admin\OrderService::class);
        $this->app->bind(\App\Services\Admin\ItemBulkUploadServiceInterface::class, \App\Services\Admin\ItemBulkUploadService::class);
        $this->app->bind(\App\Services\Admin\ItemReserveServiceInterface::class, \App\Services\Admin\ItemReserveService::class);
        $this->app->bind(\App\Services\Admin\MemberServiceInterface::class, \App\Services\Admin\MemberService::class);
        $this->app->bind(\App\Services\Admin\PageServiceInterface::class, \App\Services\Admin\PageService::class);
        $this->app->bind(\App\Services\Admin\SalesTypeServiceInterface::class, \App\Services\Admin\SalesTypeService::class);
        $this->app->bind(\App\Services\Admin\HelpServiceInterface::class, \App\Services\Admin\HelpService::class);
        $this->app->bind(\App\Services\Admin\DeliverySettingServiceInterface::class, \App\Services\Admin\DeliverySettingService::class);
        $this->app->bind(\App\Services\Admin\SalesAggregationServiceInterface::class, \App\Services\Admin\SalesAggregationService::class);
        $this->app->bind(\App\Services\Admin\TopContentServiceInterface::class, \App\Services\Admin\TopContentService::class);
        $this->app->bind(\App\Services\Admin\BrandServiceInterface::class, \App\Services\Admin\BrandService::class);
        $this->app->bind(\App\Services\Admin\PlanServiceInterface::class, \App\Services\Admin\PlanService::class);
        $this->app->bind(\App\Services\Admin\StylingServiceInterface::class, \App\Services\Admin\StylingService::class);
        $this->app->bind(\App\Services\Admin\OrderCsvServiceInterface::class, \App\Services\Admin\OrderCsvService::class);
        $this->app->bind(\App\Services\Admin\PastItemServiceInterface::class, \App\Services\Admin\PastItemService::class);

        // Front/Services
        $this->app->bind(\App\Services\Front\MemberServiceInterface::class, \App\Services\Front\MemberService::class);
        $this->app->bind(\App\Services\Front\ItemServiceInterface::class, \App\Services\Front\ItemService::class);
        $this->app->bind(\App\Services\Front\ItemDetailServiceInterface::class, \App\Services\Front\ItemDetailService::class);
        $this->app->bind(\App\Services\Front\AuthServiceInterface::class, \App\Services\Front\AuthService::class);
        $this->app->bind(\App\Services\Front\CartServiceInterface::class, \App\Services\Front\CartService::class);
        $this->app->bind(\App\Services\Front\PointServiceInterface::class, \App\Services\Front\PointService::class);
        $this->app->bind(\App\Services\Front\DestinationServiceInterface::class, \App\Services\Front\DestinationService::class);
        $this->app->bind(\App\Services\Front\PurchaseServiceInterface::class, \App\Services\Front\PurchaseService::class);
        $this->app->bind(\App\Services\Front\OrderServiceInterface::class, \App\Services\Front\OrderService::class);
        $this->app->bind(\App\Services\Front\StylingServiceInterface::class, \App\Services\Front\StylingService::class);
        $this->app->bind(\App\Services\Front\RedisplayRequestServiceInterface::class, \App\Services\Front\RedisplayRequestService::class);
        $this->app->bind(\App\Services\Front\OldMemberServiceInterface::class, \App\Services\Front\OldMemberService::class);
        $this->app->bind(\App\Services\Front\ContactServiceInterface::class, \App\Services\Front\ContactService::class);
        $this->app->bind(\App\Services\Front\TopContentServiceInterface::class, \App\Services\Front\TopContentService::class);
        $this->app->bind(\App\Services\Front\AmazonLoginServiceInterface::class, \App\Services\Front\AmazonLoginService::class);
        $this->app->bind(\App\Services\Front\GuestPurchaseServiceInterface::class, \App\Services\Front\GuestPurchaseService::class);

        // Domain
        $this->app->bind(\App\Domain\AdminLogInterface::class, \App\Domain\AdminLog::class);
        $this->app->bind(\App\Domain\ItemPriceInterface::class, \App\Domain\ItemPrice::class);
        $this->app->bind(\App\Domain\CouponInterface::class, \App\Domain\Coupon::class);
        $this->app->bind(\App\Domain\MemberInterface::class, \App\Domain\Member::class);
        $this->app->bind(\App\Domain\ItemOrderDiscountInterface::class, \App\Domain\ItemOrderDiscount::class);
        $this->app->bind(\App\Domain\OrderInterface::class, \App\Domain\Order::class);
        $this->app->bind(\App\Domain\OrderChangeHistoryInterface::class, \App\Domain\OrderChangeHistory::class);
        $this->app->bind(\App\Domain\MemberAuthInterface::class, \App\Domain\MemberAuth::class);
        $this->app->bind(\App\Domain\StoreInterface::class, \App\Domain\Store::class);
        $this->app->bind(\App\Domain\StockInterface::class, \App\Domain\Stock::class);
        $this->app->bind(\App\Domain\ItemInterface::class, \App\Domain\Item::class);
        $this->app->bind(\App\Domain\ItemImageInterface::class, \App\Domain\ItemImage::class);
        $this->app->bind(\App\Domain\ItemPreviewInterface::class, \App\Domain\ItemPreview::class);
        $this->app->bind(\App\Domain\InformationPreviewInterface::class, \App\Domain\InformationPreview::class);
        $this->app->bind(\App\Domain\AmazonPayInterface::class, \App\Domain\AmazonPay::class);
        $this->app->bind(\App\Domain\CreditCardInterface::class, \App\Domain\CreditCard::class);
        $this->app->bind(\App\Domain\NpPaymentInterface::class, \App\Domain\NpPayment::class);
        $this->app->bind(\App\Domain\OrderPortionInterface::class, \App\Domain\OrderPortion::class);
        $this->app->bind(\App\Domain\PaymentInterface::class, \App\Domain\Payment::class);
        $this->app->bind(\App\Domain\Adapters\Ymdy\MemberPurchaseInterface::class, \App\Domain\Adapters\Ymdy\MemberPurchase::class);
        $this->app->bind(\App\Domain\Adapters\Ymdy\CartMemberPurchaseInterface::class, \App\Domain\Adapters\Ymdy\CartMemberPurchase::class);
        $this->app->bind(\App\Domain\Adapters\AmazonPayAdapterInterface::class, \App\Domain\Adapters\AmazonPayAdapter::class);
        $this->app->bind(\App\Domain\Adapters\FRegiAdapterInterface::class, \App\Domain\Adapters\FRegiAdapter::class);

        // Utils
        $this->app->bind(\App\Utils\Csv\ImportCsvInterface::class, \App\Utils\Csv\ImportCsv::class);
        $this->app->bind(\App\Utils\Csv\ExportCsvInterface::class, \App\Utils\Csv\ExportCsv::class);
        $this->app->bind(\App\Utils\ZipInterface::class, \App\Utils\Zip::class);
    }
}
