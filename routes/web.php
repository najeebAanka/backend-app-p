<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\AuctionController;
use App\Http\Controllers\Dashboard\HorsesController;
use App\Http\Controllers\Dashboard\NewsController;
use App\Http\Controllers\Dashboard\UsersController;
use App\Http\Controllers\Dashboard\StaticContentController;
use App\Http\Controllers\Api\Account\AccountController;
use App\Http\Controllers\Dashboard\BannerController;
use App\Http\Controllers\Dashboard\InvoicesController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('lot-tv-banner/{id}', function () {
    return view('web.lot-tv-banner');
});

Route::get('tv-banner/{auction}', [AuctionController::class, 'getTvBanner']);
Route::get('tv-banner-and-results/{auction}', [AuctionController::class, 'getTvBannerAndResults']);

Route::get('login', function () {
    return view('dashboard.pages.login');
})->name('login');


Route::get('auth/seller/login-via-token/{token}', [AuthController::class, 'loginViaToken']);
Route::post('admin-auth/login', [AuthController::class, 'login']);
Route::get('admin-auth/logout', [AuthController::class, 'logout']);
Route::get('remote-operations/verification/email/{otp}', [AccountController::class, 'verifyEmail']);
Route::get('remote-operations/password-reset/{otp}', [AccountController::class, 'resetPassword']);
Route::get('remote-operations/pdf/catalogs/generate-auction-catalog/{id}', [AuctionController::class, 'generateCatalogPdf']);
Route::get('remote-operations/invoices/view/{id}', [InvoicesController::class, 'showInvoice']);
Route::post('operations/payment-callback', [AccountController::class, 'paymentCallback']);


Route::get('p', function () {
    return view('payment.payment_form');
});
Route::post('pc', function () {
    return view('payment.payment_confirmation');
});


Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return view('dashboard.pages.index');
    });
    Route::get('home', function () {
        return view('dashboard.pages.index');
    });

    Route::get('sellers', function () {
        return view('dashboard.pages.sellers');
    });
    Route::get('news', function () {
        return view('dashboard.pages.news');
    });
    Route::get('news/{id}', function () {
        return view('dashboard.pages.news-single');
    });
    Route::get('users', function () {
        return view('dashboard.pages.users');
    });
    Route::get('hall-bidders/{auction_id}', function () {
        return view('dashboard.pages.hall-bidders');
    });
    Route::get('lot-edit/{id}', function () {
        return view('dashboard.pages.lot-edit');
    });
    Route::get('users/{id}', function () {
        return view('dashboard.pages.user-details');
    });

    Route::get('horses', function () {
        return view('dashboard.pages.horses');
    });
    Route::get('firebase-messages', function () {
        return view('dashboard.pages.fcm-messages');
    });
    Route::get('auctions', function () {
        return view('dashboard.pages.auctions');
    });
    Route::get('auctions/{id}', function () {
        return view('dashboard.pages.auctions-single');
    });
    Route::get('auction-report/{id}', function () {
        return view('dashboard.pages.auction-report');
    })->middleware('can:edit-auctions');;
    Route::get('horse-details/{id}', function () {
        return view('dashboard.pages.horses-single');
    });
    Route::get('horse-pedigree/{id}', function () {
        return view('dashboard.pages.horses-pedigree-v3');
    });
    Route::get('horse-timeline/{id}', function () {
        return view('dashboard.pages.horses-timeline');
    });
    Route::get('lot/{id}', function () {
        return view('dashboard.pages.lot');
    });
    Route::get('static-contents', function () {
        return view('dashboard.pages.static-contents');
    });
    Route::get('roles-and-permissions', function () {
        return view('dashboard.pages.roles-and-permissions');
    });
    Route::get('wallet', function () {
        return view('dashboard.pages.wallet');
    });
    Route::get('profile', function () {
        return view('dashboard.pages.profile');
    });

    Route::get('all-invoices', function () {
        return view('dashboard.pages.all-invoices');
    })->middleware('can:view-invoices');

    Route::get('tracking-records', function () {
        return view('dashboard.pages.tracking-records');
    })->middleware('can:view-tracking-log');

    Route::get('recharge-records', function () {
        return view('dashboard.pages.recharge-records');
    })->middleware('can:view-invoices');

    Route::get('recharge-records/{id}', function ($id) {
        return view('invoices.recharge-success')->with('data', \App\Models\WalletRechargeRecord::find($id));
    })->middleware('can:view-invoices');

    Route::get('remote-operations/invoices/generate-lots-winner-invoice/{user_id}/{user_type}/{auction_id}', [InvoicesController::class, 'generateLotsWonInvoice'])
        ->middleware('can:generate-invoices');
    Route::post('/remote-operations/invoices/email-lots-winner-invoice/{invoice_id}', [InvoicesController::class, 'emailInvoice'])
        ->middleware('can:generate-invoices');
    Route::post('remote-operations/wallet-recharge-records/set-as-refunded/{id}', [InvoicesController::class, 'refundRechargeWalletRequest'])
        ->middleware('can:generate-invoices');
    Route::get('remote-operations/wallet-recharge-records/generate-refund-form/{id}', [InvoicesController::class, 'generateRefundForm'])
        ->middleware('can:generate-invoices');

    Route::post('operations/roles-and-permission/change-single', [App\Http\Controllers\RolesAndPermissionsController::class, 'linkPermissionToRole'])
        ->middleware('can:modify-roles-permissions');
    Route::post('operations/lots/reorder', [AuctionController::class, 'reorderLots'])->middleware('can:edit-lots');
    Route::post('operations/lots/update-times', [AuctionController::class, 'updateLotTimes'])->middleware('can:edit-lots');
    Route::post('operations/lots/submitBid', [AuctionController::class, 'submitBid'])->middleware('can:e-bid-in-lot');;
    Route::post('operations/auction/lots/add-bulk', [AuctionController::class, 'addLotsBulk'])->middleware('can:create-lots');
    Route::post('operations/auction/accept-lot-join-request', [AuctionController::class, 'acceptRegRequest'])->middleware('can:create-lots');
    Route::post('operations/auction/lots/add-reg-request', [AuctionController::class, 'sendRegRequest']);
    Route::post('operations/auction/edit', [AuctionController::class, 'edit'])->middleware('can:edit-auctions');
    Route::post('operations/auction/start-lot-manually', [AuctionController::class, 'startOnlineLotManually'])->middleware('can:start-stop-lot');
    Route::post('operations/auction/publish', [AuctionController::class, 'publish'])->middleware('can:edit-lots');
    Route::post('operations/lot/edit-lot', [AuctionController::class, 'editLot'])->middleware('can:edit-lots');
    Route::post('operations/auction/lots/change-outbid-permission', [AuctionController::class, 'changeOutbidStatus'])->middleware('can:edit-lots');
    Route::post('operations/auction/bids/cancel', [AuctionController::class, 'cancelBid'])->middleware('can:cancel-bid-of-user');
    Route::get('operations/ajax/horses', [HorsesController::class, 'getHorsesAjax']);
    Route::get('operations/ajax/horses-details/{id}', [HorsesController::class, 'getHorseHistory']);
    Route::post('operations/auction/finish-lot', [AuctionController::class, 'finishLot'])->middleware('can:start-stop-lot');
    Route::post('operations/auction/start-silent-lot', [AuctionController::class, 'startSilentLot'])->middleware('can:start-stop-lot');
    Route::post('operations/auction/pause-silent-lot', [AuctionController::class, 'pauseSilentLot'])->middleware('can:start-stop-lot');
    Route::post('operations/static-content/edit', [StaticContentController::class, 'editContent'])->middleware('can:edit-static-content');
    Route::post('operations/auctions/create', [AuctionController::class, 'createAuction'])->middleware('can:create-auctions');
    Route::post('operations/auctions/hall-bidders/add', [AuctionController::class, 'addHallBidder'])->middleware('can:edit-auctions');
    Route::post('operations/auction/lots/extend', [AuctionController::class, 'extendLot'])->middleware('can:extend-lot-time');
    Route::post('operations/blogs/create', [NewsController::class, 'createArticle'])->middleware('can:create-blog-posts');
    Route::post('operations/blogs/edit', [NewsController::class, 'editArticle'])->middleware('can:edit-blog-posts');
    Route::post('operations/banners', [BannerController::class, 'update'])->middleware('can:edit-banner');
    Route::post('operations/fcm-messages/create', [UsersController::class, 'sendFCM'])->middleware('can:send-fcm-messages');
    Route::post('operations/users/block', [UsersController::class, 'blockUser'])->middleware('can:block-unblock-users');
    Route::post('operations/users/unblock', [UsersController::class, 'unblockUser'])->middleware('can:block-unblock-users');
    Route::post('operations/users/delete', [UsersController::class, 'deleteUser'])->middleware('can:delete-users');
    Route::post('operations/users/wallet-recharge-by-admin', [UsersController::class, 'editWalletAmount'])->middleware('can:delete-users');
    Route::post('operations/users/edit-user-admin', [UsersController::class, 'editUserByAdmin'])->middleware('can:delete-users');
    Route::post('operations/users/change-role', [UsersController::class, 'changeRole'])->middleware('can:modify-roles-permissions');
    Route::post('operations/horses/create', [HorsesController::class, 'createHorse']);
    Route::post('operations/horses/update', [HorsesController::class, 'updateHorse']);
    Route::post('operations/horses/update-pedigree', [HorsesController::class, 'updateHorsePedigree']);
    Route::post('operations/horses-gallery/delete', [HorsesController::class, 'deleteImges']);
    Route::post('operations/horses/performance/add', [HorsesController::class, 'addPerformance']);
    Route::post('operations/horses/performance/delete', [HorsesController::class, 'deletePerformance']);
    Route::post('operations/auction/lots/send-update', [AuctionController::class, 'sendUpdate'])->middleware('can:send-announcement-in-lot');
    Route::post('operations/profile/edit', [UsersController::class, 'editProfile']);
    Route::get('operations/auction-log-generator/{id}', [AuctionController::class, 'generateLog']);
    Route::get('recharge-recepits/view/{id}', function ($id) {
        return view('emails.recharge-success')->with('data', \App\Models\WalletRechargeRecord::find($id));
    });

    Route::get('banners', [BannerController::class, 'show'])->middleware('can:edit-banner');
});