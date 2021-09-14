<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RepositoryServiceProvider extends ServiceProvider
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
        $this->app->bind(\App\Repositories\ItemRepository::class, \App\Repositories\ItemRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OnlineCategoryRepository::class, \App\Repositories\OnlineCategoryRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ColorRepository::class, \App\Repositories\ColorRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CounterPartyRepository::class, \App\Repositories\CounterPartyRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\BrandRepository::class, \App\Repositories\BrandRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OnlineTagRepository::class, \App\Repositories\OnlineTagRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\SalesTypeRepository::class, \App\Repositories\SalesTypeRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\InformationRepository::class, \App\Repositories\InformationRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ItemDetailRepository::class, \App\Repositories\ItemDetailRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ItemImageRepository::class, \App\Repositories\ItemImageRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ItemOnlineCategoryRepository::class, \App\Repositories\ItemOnlineCategoryRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ItemOnlineTagRepository::class, \App\Repositories\ItemOnlineTagRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ItemRecommendRepository::class, \App\Repositories\ItemRecommendRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ItemSalesTypesRepository::class, \App\Repositories\ItemSalesTypesRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ItemSubBrandRepository::class, \App\Repositories\ItemSubBrandRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ItemsUsedSameStylingRepository::class, \App\Repositories\ItemsUsedSameStylingRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ClosedMarketRepository::class, \App\Repositories\ClosedMarketRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ItemReserveRepository::class, \App\Repositories\ItemReserveRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\EventRepository::class, \App\Repositories\EventRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderRepository::class, \App\Repositories\OrderRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderCreditRepository::class, \App\Repositories\OrderCreditRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Admin\EnumMasterRepositoryConstantInterface::class, \App\Repositories\Admin\EnumMasterRepositoryConstant::class);
        $this->app->bind(\App\Repositories\Front\EnumMasterRepositoryConstantInterface::class, \App\Repositories\Front\EnumMasterRepositoryConstant::class);
        $this->app->bind(\App\Repositories\OrderDetailRepository::class, \App\Repositories\OrderDetailRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\EventItemRepository::class, \App\Repositories\EventItemRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderMessageRepository::class, \App\Repositories\OrderMessageRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\EventUserRepository::class, \App\Repositories\EventUserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\DepartmentGroupRepository::class, \App\Repositories\DepartmentGroupRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\DepartmentRepository::class, \App\Repositories\DepartmentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\SeasonGroupRepository::class, \App\Repositories\SeasonGroupRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\SeasonRepository::class, \App\Repositories\SeasonRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\SizeRepository::class, \App\Repositories\SizeRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\DivisionRepository::class, \App\Repositories\DivisionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\TermRepository::class, \App\Repositories\TermRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\StaffRepository::class, \App\Repositories\StaffRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CartRepository::class, \App\Repositories\CartRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AdminLogRepository::class, \App\Repositories\AdminLogRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ItemBulkUploadRepository::class, \App\Repositories\ItemBulkUploadRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UrgentNoticeRepository::class, \App\Repositories\UrgentNoticeRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PageRepository::class, \App\Repositories\PageRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UserRepository::class, \App\Repositories\UserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PrefRepository::class, \App\Repositories\PrefRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderAddressRepository::class, \App\Repositories\OrderAddressRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\DeliverySettingRepository::class, \App\Repositories\DeliverySettingRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\HelpRepository::class, \App\Repositories\HelpRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\HelpCategoryRelationRepository::class, \App\Repositories\HelpCategoryRelationRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\HelpCategoryRepository::class, \App\Repositories\HelpCategoryRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\HelpRepository::class, \App\Repositories\HelpRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\TopContentRepository::class, \App\Repositories\TopContentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\SalesAggregation\OrderRepository::class, \App\Repositories\SalesAggregation\OrderRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\SalesAggregation\ItemRepository::class, \App\Repositories\SalesAggregation\ItemRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrganizationRepository::class, \App\Repositories\OrganizationRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ItemSortRepository::class, \App\Repositories\ItemSortRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\TopContentAdminRepository::class, \App\Repositories\TopContentAdminRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ItemFavoriteRepository::class, \App\Repositories\ItemFavoriteRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderNpRepository::class, \App\Repositories\OrderNpRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PlanRepository::class, \App\Repositories\PlanRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PlanItemRepository::class, \App\Repositories\PlanItemRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ItemDetailRedisplayRequestRepository::class, \App\Repositories\ItemDetailRedisplayRequestRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\StoreRepository::class, \App\Repositories\StoreRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ItemDetailIdentificationRepository::class, \App\Repositories\ItemDetailIdentificationRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ItemDetailStoreRepository::class, \App\Repositories\ItemDetailStoreRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderDetailUnitRepository::class, \App\Repositories\OrderDetailUnitRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderChangeHistoryRepository::class, \App\Repositories\OrderChangeHistoryRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\EventBundleSaleRepository::class, \App\Repositories\EventBundleSaleRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderUsedCouponRepository::class, \App\Repositories\OrderUsedCouponRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderDiscountRepository::class, \App\Repositories\OrderDiscountRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\TempStockRepository::class, \App\Repositories\TempStockRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CartItemRepository::class, \App\Repositories\CartItemRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\MemberCreditCardRepository::class, \App\Repositories\MemberCreditCardRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\NpRejectedTransactionRepository::class, \App\Repositories\NpRejectedTransactionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AmazonPay\AuthorizationRepository::class, \App\Repositories\AmazonPay\AuthorizationRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AmazonPay\CaptureRepository::class, \App\Repositories\AmazonPay\CaptureRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AmazonPay\NotificationRepository::class, \App\Repositories\AmazonPay\NotificationRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AmazonPay\OrderRepository::class, \App\Repositories\AmazonPay\OrderRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AmazonPay\RefundRepository::class, \App\Repositories\AmazonPay\RefundRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\RefundDetailRepository::class, \App\Repositories\RefundDetailRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PastItemRepository::class, \App\Repositories\PastItemRepositoryEloquent::class);
    }
}
