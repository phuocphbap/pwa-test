<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatsController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\StoresController;
use App\Http\Controllers\Api\ServicesController;
use App\Http\Controllers\Api\VerifySMSController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\Bonus\BonusesController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\Coupon\CouponsController;
use App\Http\Controllers\Api\Store\StoreGetController;
use App\Http\Controllers\Api\Comment\CommentsController;
use App\Http\Controllers\Api\Contact\ContactsController;
use App\Http\Controllers\Api\Payment\PaymentsController;
use App\Http\Controllers\Api\Firebase\FirebaseController;
use App\Http\Controllers\Api\WithDraw\WithDrawController;
use App\Http\Controllers\Api\Question\QuestionsController;
use App\Http\Controllers\Api\RequestConsultingsController;
use App\Http\Controllers\Api\Webhook\WebhookController;
use App\Http\Controllers\Api\StorePost\StorePostsController;
use App\Http\Controllers\Api\StoreImage\StoreImagesController;
use App\Http\Controllers\Api\Advertising\AdvertisingController;
use App\Http\Controllers\Api\BankAccount\BankAccountsController;
use App\Http\Controllers\Api\CompanyTerms\CompanyTermsController;
use App\Http\Controllers\Api\StoreArticle\StoreArticlesController;
use App\Http\Controllers\Api\ServiceReview\ServiceReviewsController;
use App\Http\Controllers\Api\StoreIntroduction\StoreIntroductionsController;

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
Route::group(['middleware' => ['web']], function () {
    Route::get('/redirect/{provider}', [SocialAuthController::class, 'redirect']);
    Route::get('/callback/{provider}', [SocialAuthController::class, 'callback']);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'loginProcess']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/verify-register/{token}', [AuthController::class, 'verifyRegister']);
    Route::post('/send-reset-password', [ResetPasswordController::class, 'sendMail']);
    Route::post('/change-password', [ResetPasswordController::class, 'resetPassword']);
    Route::post('/sms/verification', [AuthController::class, 'smsVerification']);
    Route::post('/sms-resend/verification', [AuthController::class, 'smsResendVerification']);
});

Route::group(['prefix' => 'v1', 'middleware' => ['api']], function () { //Prefix of version api
    Route::group(['prefix' => 'payment'], function () {
        Route::post('/webhook', [WebhookController::class, 'checkout']);
    });
    //categories
    Route::get('/categories', [CommonController::class, 'getAllCategories']);
    Route::get('/sub-categories', [CommonController::class, 'getSubCategories']);
    //regions
    Route::get('/regions', [CommonController::class, 'getAllRegions']);
    //services
    Route::get('/user-services', [ServicesController::class, 'listServicesOfUser']);
    Route::resource('services', ServicesController::class, ['only' => ['index', 'show']]);
    Route::get('related-services', [ServicesController::class, 'relatedServices']);
    Route::get('service-suggests', [ServicesController::class, 'listServiceSuggests']);

    //stores
    Route::resource('stores', StoresController::class);
    Route::group(['prefix' => 'store'], function () {
        Route::get('info-map', [StoresController::class, 'getMapDetailStore']);
        Route::get('spot', [StoresController::class, 'getSpotStore']);

        // show store introduction
        Route::get('introductions/{storeId}', [StoreIntroductionsController::class, 'show']);
        Route::get('introductions/detail/{id}', [StoreIntroductionsController::class, 'getDetail']);
        Route::delete('introductions/{id}', [StoreIntroductionsController::class, 'delete']);

        // show store images
        Route::get('images/{storeId}', [StoreImagesController::class, 'index']);
        Route::get('images/detail/{id}', [StoreImagesController::class, 'show']);
    });

    Route::get('store-services', [StoresController::class, 'listServiceOfStore']);
    Route::get('customer-review', [StoresController::class, 'customerReviews']);
    Route::get('owner-review', [StoresController::class, 'ownerReviews']);

    // use google cloud search place
    Route::get('place-search', [StoreGetController::class, 'getPlaceSearch']);

    //service comments
    Route::resource('/comments', CommentsController::class, ['only' => ['index', 'show']]);

    //store posts
    Route::resource('store-posts', StorePostsController::class);

    // store articles
    Route::group(['prefix' => 'store-articles'], function () {
        Route::get('{id}', [StoreArticlesController::class, 'show']);
        Route::get('list/{storeId}', [StoreArticlesController::class, 'getArticles']);
        Route::get('images/{storeId}', [StoreArticlesController::class, 'getImagesArticles']);
        Route::delete('{id}', [StoreArticlesController::class, 'delete']);
    });

    //contact
    Route::resource('contacts', ContactsController::class);

    //contact
    Route::resource('questions', QuestionsController::class);

    // advertising
    Route::get('advertising', [AdvertisingController::class, 'getAdvertising']);

    // company terms
    Route::get('company-terms', [CompanyTermsController::class, 'getCompanyTerms']);
    Route::get('privacy-terms', [CompanyTermsController::class, 'getAllCompanyTerms']);

    //service-reviews
    Route::resource('service-reviews', ServiceReviewsController::class, ['only' => ['index']]);

    // show bonus
    Route::get('referral-bonus', [CommonController::class, 'getReferralBonus']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'getUserAuth']);

        Route::get('chat/list', [ChatsController::class, 'getListChat']);

        //------Firebase-------//
        Route::post('chats', [FirebaseController::class, 'store']);
        Route::post('chatbots', [FirebaseController::class, 'sendTextToChatbots']);

        //Coupon
        Route::group(['prefix' => 'coupon'], function () {
            Route::post('', [CouponsController::class, 'createCoupon']);
            Route::get('/check-coupon', [CouponsController::class, 'checkCodeCoupon']);
        });

        //Payment
        Route::group(['prefix' => 'payment'], function () {
            Route::post('', [PaymentsController::class, 'paymentService']);
            Route::post('check-point', [PaymentsController::class, 'checkInputPoint']);
            Route::post('create-payment-intent', [PaymentsController::class, 'createPaymentIntent']);
            Route::post('confirm-payment-intent', [PaymentsController::class, 'comfirmPaymentIntent']);
            Route::post('create-payment-method', [PaymentsController::class, 'createPaymentMethod']);
            Route::get('list-payment-method', [PaymentsController::class, 'listCustomerPaymentMethods']);
            Route::post('attach-method', [PaymentsController::class, 'attachPaymentMethodForUser']);
            Route::post('detach-method', [PaymentsController::class, 'detachPaymentMethod']);
            Route::post('create-session', [PaymentsController::class, 'createSessionCheckout']);

            // history payment
            Route::get('history', [PaymentsController::class, 'historyPayment']);
        });

        //request-consultings
        Route::resource('/request-consultings', RequestConsultingsController::class);
        Route::group(['prefix' => 'request-consulting'], function () {
            Route::post('confirm-request/{id}', [RequestConsultingsController::class, 'confirmRequestConsulting']);
            Route::post('confirm-payment/{id}', [RequestConsultingsController::class, 'confirmPayment']);
            Route::post('confirm-review/{id}', [RequestConsultingsController::class, 'confirmReview']);
            Route::post('cancel-request/{id}', [RequestConsultingsController::class, 'cancelRequestConsulting']);
            Route::post('owner-cancel-request/{id}', [RequestConsultingsController::class, 'ownerCancelRequestConsulting']);
        });

        //service
        Route::resource('services', ServicesController::class, ['only' => ['store', 'destroy']]);
        Route::group(['prefix' => 'service'], function () {
            Route::post('like', [ServicesController::class, 'likeService']);
            Route::post('update-service/{id}', [ServicesController::class, 'update']);
            Route::put('block/{serviceId}', [ServicesController::class, 'block']);
            Route::put('unlock/{serviceId}', [ServicesController::class, 'unlock']);
        });
        Route::get('liked-services', [ServicesController::class, 'listServiceLiked']);

        //service-reviews
        Route::group(['prefix' => 'service-review'], function () {
            Route::post('confirm', [ServiceReviewsController::class, 'createServiceReview']);
            Route::post('cancel', [ServiceReviewsController::class, 'cancelServiceReview']);
        });

        //service comments
        Route::resource('/comments', CommentsController::class, ['only' => ['store', 'update', 'destroy']]);

        // store after loggin
        Route::group(['prefix' => 'store'], function () {
            Route::post('like', [StoresController::class, 'likeStore']);

            // register phone & address
            Route::post('update/info', [StoresController::class, 'updateInfoStore']);

            // article of store
            Route::resource('articles', StoreArticlesController::class, ['only' => ['store', 'update']]);

            // store introduction
            Route::resource('introductions', StoreIntroductionsController::class, ['only' => ['store', 'update']]);

            // store images
            Route::resource('images', StoreImagesController::class, ['only' => ['store', 'update', 'destroy']]);
        });

        //users
        Route::get('/show-referral-code', [UsersController::class, 'isShowReferralCode']);
        Route::resource('users', UsersController::class);
        Route::get('point-wallet', [UsersController::class, 'getPointInWallet']);
        Route::group(['prefix' => 'users'], function () {
            Route::post('identify-card', [UsersController::class, 'updateIdentityCard']);
            Route::post('update-profile', [UsersController::class, 'updateProfile']);
            Route::post('request-withdraw', [UsersController::class, 'requestWithDraw']);
            Route::post('update-two-fa', [UsersController::class, 'updateTwoFa']);
            Route::post('reset-phone', [UsersController::class, 'resetPhone']);
            Route::post('send-sms', [VerifySMSController::class, 'sendVerifyCode']);
            Route::post('confirm-sms', [VerifySMSController::class, 'confirmVerifyCode']);
            Route::post('reset-avatar', [UsersController::class, 'resetAvatar']);
        });
        Route::get('liked-stores', [StoresController::class, 'listStoreLiked']);

        // bank account
        Route::post('bank-account', [BankAccountsController::class, 'createAccountBank']);
        Route::get('bank-account/category', [BankAccountsController::class, 'getListCategoryBankAccount']);
        Route::get('bank-account', [BankAccountsController::class, 'show']);
        Route::put('bank-account', [BankAccountsController::class, 'update']);

        // leave group
        Route::post('leave-group', [UsersController::class, 'leaveGroup']);

        //bonuses
        Route::get('history-bonus', [BonusesController::class, 'getListHistory']);

        // history withdraw
        Route::get('history-withdraw', [WithDrawController::class, 'getHistoryWithDraw']);

        // switch notification progress
        Route::post('user/switch-notifications', [FirebaseController::class, 'switchNoticesProgress']);
    });
});
