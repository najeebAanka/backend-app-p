<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuctionController;
use App\Http\Controllers\Api\Auth\GoogleLoginController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Dashboard\StaticContentController;
use App\Http\Controllers\Api\Account\AccountController;

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

Route::group(
    [
        'prefix' => 'v1/',
        //'middleware' => ['localization', 'cors']
    ],
    function () {



        Route::post('support-messages/submit', [AuthController::class, 'sendSupportMessage']);

        Route::post('signup', [AuthController::class, 'store']);
        Route::post('otp/request', [AuthController::class, 'sendOtpToVerify']);
        Route::post('otp/verify', [AuthController::class, 'checkOtp']);

        Route::post('login', [AuthController::class, 'login']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);


        Route::post('social-login/via-provider', [AuthController::class, 'socialLogin']);
        //-------

        Route::get('app-home-widgets', [HomeController::class, 'homeWidgets']);
        Route::get('app-texts/{key}', [StaticContentController::class, 'fetch']);
        Route::get('get-auctions', [AuctionController::class, 'getAuctions']);
        Route::get('get-auction-by-id/{id}', [AuctionController::class, 'getAuctionById']);
        Route::get('get-lot-by-id/{id}', [AuctionController::class, 'getLotById']);
        Route::get('get-horse-by-id/{id}', [AuctionController::class, 'getHorseDetailsById']);
        Route::get('get-active-lot/{auction}', [AuctionController::class, 'getActiveLot']);

        Route::get('news', [HomeController::class, 'getNews']);
        Route::get('news/{id}', [HomeController::class, 'getNewsById']);
        Route::get('search', [HomeController::class, 'searchItems']);
        Route::get('operations/recharge-wallet-requests/pay/{code}', [AccountController::class, 'payRechargeRequest']);
        Route::get('operations/recharge-wallet-requests/callback/{code}', [AccountController::class, 'payRechargeCallback']);
    }
);

Route::group(['prefix' => 'v1/', 'middleware' => [
    'auth:api'
    //    , 'localization', 'cors'
]], function () {





    Route::get('account', [AuthController::class, 'details']);
    Route::get('wallet', [AccountController::class, 'getMyWallet']);
    Route::get('won-lots', [AccountController::class, 'getWonLots']);
    Route::get('account/delete', [AuthController::class, 'deleteAccount']);
    Route::get('account/logout', [AuthController::class, 'logout']);
    Route::post('account/update', [AuthController::class, 'update']);

    Route::post('place-bid', [AuctionController::class, 'submitBid']);
    Route::get('my-bids', [AuctionController::class, 'getMyBids']);
    Route::post('handle-favourites', [AccountController::class, 'handleFavourite']);
    Route::get('get-favourites', [AccountController::class, 'getFavourites']);
    Route::get('get-notifications', [HomeController::class, 'getNotifications']);
    Route::post('operations/recharge-wallet-requests/create', [AccountController::class, 'rechargeWallet']);
    Route::get('operations/recharge-wallet-requests/request-refund/{id}', [AccountController::class, 'sendRefundRequest']);
    Route::get('operations/generate-login-token', [AuthController::class, 'generateLoginToken']);
});
