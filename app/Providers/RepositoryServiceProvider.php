<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        $this->app->bind(\App\Repositories\UserRepository::class, \App\Repositories\UserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ServiceRepository::class, \App\Repositories\ServiceRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\StoreRepository::class, \App\Repositories\StoreRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ChatRepository::class, \App\Repositories\ChatRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\RequestConsultingRepository::class, \App\Repositories\RequestConsultingRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CouponRepository::class, \App\Repositories\CouponRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PaymentRepository::class, \App\Repositories\PaymentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ServiceReviewRepository::class, \App\Repositories\ServiceReviewRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CommentRepository::class, \App\Repositories\CommentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\StoreArticleRepository::class, \App\Repositories\StoreArticleRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\StorePostRepository::class, \App\Repositories\StorePostRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\BankAccountRepository::class, \App\Repositories\BankAccountRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ContactRepository::class, \App\Repositories\ContactRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\WithdrawRequestRepository::class, \App\Repositories\WithdrawRequestRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\WalletRepository::class, \App\Repositories\WalletRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\WalletTransactionRepository::class, \App\Repositories\WalletTransactionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\BillRepository::class, \App\Repositories\BillRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\BonusRepository::class, \App\Repositories\BonusRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\StoreIntroductionRepository::class, \App\Repositories\StoreIntroductionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\QuestionRepository::class, \App\Repositories\QuestionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\StoreImageRepository::class, \App\Repositories\StoreImageRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\FeePaymentRepository::class, \App\Repositories\FeePaymentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AdminUserRepository::class, \App\Repositories\AdminUserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ReferralBonusRepository::class, \App\Repositories\ReferralBonusRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\WalletExpireRepository::class, \App\Repositories\WalletExpireRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PeriodExpireRepository::class, \App\Repositories\PeriodExpireRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\IdentityCardRepository::class, \App\Repositories\IdentityCardRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AdvertisingCategoryRepository::class, \App\Repositories\AdvertisingCategoryRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AdvertisingBlockRepository::class, \App\Repositories\AdvertisingBlockRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AdvertisingMediaRepository::class, \App\Repositories\AdvertisingMediaRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CategoryRepository::class, \App\Repositories\CategoryRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CompanyTermsRepository::class, \App\Repositories\CompanyTermsRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ServiceSuggestRepository::class, \App\Repositories\ServiceSuggestRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ServiceRegionRepository::class, \App\Repositories\ServiceRegionRepositoryEloquent::class);
        //:end-bindings:
    }
}
