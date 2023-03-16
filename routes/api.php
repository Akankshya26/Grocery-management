<?php

use App\Http\Controllers\V1\OrderItemsController;
use App\Http\Controllers\V1\OrderController;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\UserController;
use App\Http\Controllers\V1\ImageController;
use App\Http\Controllers\V1\PartnerController;
use App\Http\Controllers\V1\ProductController;
use App\Http\Controllers\V1\CategoryController;
use App\Http\Controllers\V1\AddressTypeController;
use App\Http\Controllers\V1\CartItemController;
use App\Http\Controllers\V1\ProductRatingController;
use App\Http\Controllers\V1\SubCategoryController;
use App\Http\Controllers\V1\UserAddressController;
use App\Http\Controllers\V1\WishlistController;

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

Route::prefix('V1')->group(function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('logout', [UserController::class, 'logout']);
        Route::post('update/{id}', [UserController::class, 'update']);
        Route::controller(CategoryController::class)->prefix('category')->group(function () {
            Route::post('list',  'list');
            Route::post('create', 'create');
            Route::get('get/{id}',  'get');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
        });
        Route::controller(PartnerController::class)->prefix('partner')->group(function () {
            Route::post('list',  'list')->middleware('check:type,admin');
            Route::post('create', 'create')->middleware('check:type,admin');
            Route::get('get/{id}',  'get')->middleware('check:type,admin');
            Route::post('update/{id}', 'update')->middleware('check:type,admin');
            Route::post('delete/{id}', 'delete')->middleware('check:type,admin');
        });
        Route::controller(SubCategoryController::class)->prefix('subCategory')->group(function () {
            Route::post('list',  'list');
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
        Route::controller(UserAddressController::class)->prefix('UserAddress')->group(function () {
            Route::post('list',  'list');
            Route::post('create', 'create');
            Route::get('get/{id}',  'get');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
        });
        Route::controller(ProductController::class)->prefix('Product')->group(function () {
            Route::post('list',  'list')->middleware('check:type,partner');
            Route::post('create', 'create')->middleware('check:type,partner');
            Route::get('get/{id}',  'get')->middleware('check:type,partner');
            Route::post('update/{id}', 'update')->middleware('check:type,partner');
            Route::post('delete/{id}', 'delete')->middleware('check:type,partner');
        });
        Route::controller(ImageController::class)->prefix('Image')->group(function () {
            Route::post('list',  'list');
            Route::post('create', 'create');
            Route::get('get/{id}',  'get');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
        });
        Route::controller(ProductRatingController::class)->prefix('rating')->group(function () {
            Route::post('list',  'list');
            Route::post('create', 'create');
            Route::get('get/{id}',  'get');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
        });
        Route::controller(WishlistController::class)->prefix('wishlist')->group(function () {
            Route::post('list',  'list');
            Route::post('create', 'create');
            Route::get('get/{id}',  'get');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
        });
        Route::controller(CartItemController::class)->prefix('cart')->group(function () {
            Route::post('list',  'list');
            Route::post('create', 'create');
            Route::get('get/{id}',  'get');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
        });
        Route::controller(OrderController::class)->prefix('order')->group(function () {
            Route::post('list',  'list');
            Route::post('create', 'create');
            Route::get('get/{id}',  'get');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
        });
        Route::controller(OrderItemsController::class)->prefix('orderItem')->group(function () {
            Route::post('list',  'list');
            Route::post('create', 'create');
            Route::get('get/{id}',  'get');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
        });
    });
});
