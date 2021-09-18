<?php

use App\Http\Controllers\Admin\Advertising\AdvertisingMediaController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\Bonus\BonusController;
use App\Http\Controllers\Admin\CompanyTerms\CompanyTermsController;
use App\Http\Controllers\Admin\Contacts\ContactsController;
use App\Http\Controllers\Admin\Dashboard\DashboardController;
use App\Http\Controllers\Admin\Services\CategoryService;
use App\Http\Controllers\Admin\Services\ServicesController;
use App\Http\Controllers\Admin\User\UserController;
use App\Http\Controllers\Admin\Withdraw\WithDrawController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'adminLoginProcess']);
    Route::post('/send-reset-password', [ResetPasswordController::class, 'sendMail']);
    Route::post('/change-password', [ResetPasswordController::class, 'resetPassword']);
});

Route::group(['prefix' => 'v1', 'middleware' => ['admin']], function () { //Prefix of version api
    Route::middleware('auth:admin-api')->group(function () {
        Route::post('/logout', [AuthController::class, 'adminLogout']);
        Route::get('/me', [AuthController::class, 'getAdminAuth']);

        // manager bonus
        Route::get('list-bonuses', [BonusController::class, 'listBonusesInAdmin']);
        Route::post('bonus', [BonusController::class, 'createBonus']);
        Route::get('bonus', [BonusController::class, 'getPointBonus']);
        Route::post('bonus/indicated', [BonusController::class, 'handleBonusIndecated']);

        // user
        Route::get('users', [UserController::class, 'getListUser']);
        Route::put('users/black-list/{userId}', [UserController::class, 'handleBlackListAccount']);
        Route::get('users/detail/{userId}', [UserController::class, 'getDetailUser']);
        Route::get('users/identify-card/{userId}', [UserController::class, 'getIdentifyCard']);
        Route::get('users/bank-account/{userId}', [UserController::class, 'getBankAccount']);
        Route::get('users/point-wallet/{userId}', [UserController::class, 'getPointByUser']);
        Route::get('users/service/{userId}', [UserController::class, 'getServiceByUser']);
        Route::get('users/service/detail/{serviceId}', [UserController::class, 'getDetailService']);
        Route::get('users/service/comment/{serviceId}', [UserController::class, 'getCommentService']);
        Route::get('users/service/review/{serviceId}', [UserController::class, 'getReviewService']);
        Route::get('users/service/related/{serviceId}', [UserController::class, 'getRelatedService']);
        Route::get('users/service/progress/{serviceId}', [UserController::class, 'getProgressService']);
        Route::get('users/progress/{userId}', [UserController::class, 'getProgressListUser']);
        Route::get('users/progress/chat/{consultingId}', [UserController::class, 'getProgressChat']);
        Route::get('users/progress/detail/{consultingId}', [UserController::class, 'getDetailProgress']);
        Route::get('users/chat-direction/{userId}', [UserController::class, 'getListChatExceptRequestConsulting']);
        Route::get('users/users-input-referral', [UserController::class, 'listUserInputReferralCode']);
        Route::put('users/reset-phone/{userId}', [UserController::class, 'handleResetPhone']);
        
        // verify identify card
        Route::put('users/identify-card/{userId}', [BonusController::class, 'verifyIdentifyCard']);
        Route::post('users/cancel-identity-card', [UserController::class, 'cancelAprovedIdentityCard']);

        // advertising
        Route::get('advertising/category', [AdvertisingMediaController::class, 'getListCategoryAds']);
        Route::get('advertising/block/{categoryId}', [AdvertisingMediaController::class, 'getListBlockAdvertising']);
        Route::get('advertising/{blockId}', [AdvertisingMediaController::class, 'index']);
        Route::post('advertising', [AdvertisingMediaController::class, 'createMediaAdvertising']);
        Route::post('advertising/destroy', [AdvertisingMediaController::class, 'destroy']);
        Route::post('advertising/block', [AdvertisingMediaController::class, 'storeBlockContent']);

        // services
        Route::get('services', [ServicesController::class, 'getListServices']);
        Route::post('services/suggest', [ServicesController::class, 'storeServiceSuggest']);
        Route::get('services/suggest', [ServicesController::class, 'getServiceSuggest']);
        // Route::put('services/block/{serviceId}', [ServicesController::class, 'handleBlockService']);  // remove
        Route::delete('services/{serviceId}', [ServicesController::class, 'removeServices']);
        Route::put('services/recover/{serviceId}', [ServicesController::class, 'recoverServices']);

        // service realted suggest
        Route::post('services/related/suggest', [ServicesController::class, 'storeSuggestRelated']);

        // category service
        Route::get('services/category', [CategoryService::class, 'getListCategory']);
        Route::post('services/category', [CategoryService::class, 'createCategory']);
        Route::put('services/category/{id}', [CategoryService::class, 'updateCategory']);
        Route::delete('services/category/{id}', [CategoryService::class, 'deleteCategory']);

        // company terms
        Route::get('company-terms', [CompanyTermsController::class, 'index']);
        Route::post('company-terms', [CompanyTermsController::class, 'createCompanyTerms']);
        Route::post('company-terms/destroy', [CompanyTermsController::class, 'destroy']);

        // contact
        Route::get('contact', [ContactsController::class, 'getListContact']);
        Route::post('contact', [ContactsController::class, 'answerContact']);
        Route::delete('contact/{id}', [ContactsController::class, 'deleteContact']);

        Route::resource('dashboard', DashboardController::class);

        //Request withdraw
        Route::resource('withdraw', WithDrawController::class);
        Route::put('withdraw/update-status/{withdrawId}', [WithDrawController::class, 'updateStatusWithdraw']);
    });
});
