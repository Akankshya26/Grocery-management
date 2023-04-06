<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\OrderController;
use App\Http\Controllers\V1\CouponController;
use App\Http\Controllers\V1\PartnerController;
use App\Http\Controllers\V1\ProductController;
use App\Http\Controllers\V1\CartItemController;
use App\Http\Controllers\V1\CategoryController;
use App\Http\Controllers\V1\WishlistController;
use App\Http\Controllers\V1\AddressTypeController;
use App\Http\Controllers\V1\SubCategoryController;
use App\Http\Controllers\V1\UserAddressController;
use App\Http\Controllers\V1\ProductRatingController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgetPassword', [AuthController::class, 'forgotPassword']);
    Route::post('resetPassword/{token}', [AuthController::class, 'resetPassword']);
    //open api(listing)
    Route::post('Category_list', [CategoryController::class, 'list']);
    Route::post('subCategoy_list/{id}', [SubCategoryController::class, 'list']);
    Route::post('product_list', [ProductController::class, 'list']);

    //order accept /decline
    Route::get('approve/{id}', [OrderController::class, 'approve'])->name('admin.approve');
    Route::get('decline/{id}', [OrderController::class, 'decline'])->name('admin.decline');

    Route::get('invoice/download/{id}', [OrderController::class, 'downloadInvoice'])->name('invoice.download');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::post('update/{id}', [AuthController::class, 'update']);
        Route::post('change/password', [AuthController::class, 'updatePassword']);
        Route::post('user/view', [AuthController::class, 'view']);


        //Route Group For Admin
        Route::group(['prefix' => 'admin', 'middleware' => 'checkAccess:admin'], function () {
            Route::controller(CategoryController::class)->prefix('category')->group(function () {
                Route::post('create', 'create');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });

            Route::controller(PartnerController::class)->prefix('partner')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });

            Route::controller(SubCategoryController::class)->prefix('subCategory')->group(function () {
                Route::post('create', 'create');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });
            Route::controller(AddressTypeController::class)->prefix('AddressType')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });
            Route::controller(OrderController::class)->prefix('order')->group(function () {
                Route::post('list',  'list');
                Route::post('order/status/{order_num}', 'orderStatus');
                Route::get('invoice',  'downloadInvoice');
                Route::post('cancel/orderList',  'cancelOrder');
                Route::post('chnageStatus', 'updateStatus');
            });
            Route::controller(CouponController::class)->prefix('coupon')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');
            });
        });

        //Route Group For partner
        Route::group(['prefix' => 'partner', 'middleware' => 'checkAccess:partner'], function () {
            Route::post('user/view', [AuthController::class, 'view']);
            Route::controller(ProductController::class)->prefix('Product')->group(function () {
                Route::post('create', 'create');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
                Route::post('partner/product/list', 'partnerProduct');
            });
            Route::controller(OrderController::class)->prefix('order')->group(function () {
                Route::post('order/status/{order_num}', 'orderStatus');
                Route::post('invoice/list',  'invoiceList');
                Route::post('chnageStatus', 'updateStatus');
            });
            Route::controller(CouponController::class)->prefix('coupon')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');
            });
        });
        //Route Group For customer
        Route::group(['prefix' => 'customer', 'middleware' => 'checkAccess:customer'], function () {
            Route::controller(UserAddressController::class)->prefix('UserAddress')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });
            Route::controller(AddressTypeController::class)->prefix('AddressType')->group(function () {
                Route::post('list',  'list');
            });
            Route::controller(ProductRatingController::class)->prefix('rating')->group(function () {
                Route::post('create', 'create');
            });
            Route::controller(WishlistController::class)->prefix('wishlist')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');;
                Route::post('delete', 'delete');
            });
            Route::controller(CartItemController::class)->prefix('cart')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');
                Route::post('update/{id}', 'update'); //only product quantity
                Route::post('delete', 'delete');
            });
            Route::controller(OrderController::class)->prefix('order')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');
                Route::get('get/{id}',  'get');
                Route::post('order/status/{order_num}', 'orderStatus');
                Route::get('invoice',  'downloadInvoice');
                Route::post('cancel/order',  'customerCancelOrder');
                Route::get('invoice/download/{id}/generate',  'downloadInvoice');
                Route::post('invoice/list',  'invoiceList');
                Route::post('chnageStatus', 'updateStatus');
            });
            Route::controller(CouponController::class)->prefix('coupon')->group(function () {
                Route::post('list',  'list');
            });
        });
    });
});
