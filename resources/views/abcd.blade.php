Route::prefix('v1')->group(function () {
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('forgetPassword', [UserController::class, 'forgotPassword']);
Route::post('resetPassword/{token}', [UserController::class, 'resetPassword']);
//open api for customer(listing)
Route::post('Category_list', [CategoryController::class, 'list']);
Route::post('subCategoy_list/{id}', [SubCategoryController::class, 'list']);
Route::post('product_list', [ProductController::class, 'list']);

//order accept /decline
Route::get('approve/{id}', [OrderController::class, 'approve'])->name('admin.approve');
Route::get('decline/{id}', [OrderController::class, 'decline'])->name('admin.decline');

Route::middleware('auth:sanctum')->group(function () {
Route::get('logout', [UserController::class, 'logout']);
Route::post('update/{id}', [UserController::class, 'update']);
Route::post('change-password', [UserController::class, 'updatePassword']);
Route::post('user/view', [UserController::class, 'view']);

Route::controller(CategoryController::class)->prefix('category')->group(function () {

Route::post('create', 'create');
Route::get('get/{id}', 'get');
Route::post('update/{id}', 'update');
Route::post('delete/{id}', 'delete');
})->middleware('check:admin');

Route::controller(PartnerController::class)->prefix('partner')->group(function () {
Route::post('list', 'list');
Route::post('create', 'create');
Route::get('get/{id}', 'get');
Route::post('update/{id}', 'update');
Route::post('delete/{id}', 'delete');
})->middleware('check:admin');

Route::controller(SubCategoryController::class)->prefix('subCategory')->group(function () {

Route::post('create', 'create');
Route::get('get/{id}', 'get');
Route::post('update/{id}', 'update');
Route::post('delete/{id}', 'delete');
})->middleware('check:admin');

Route::controller(AddressTypeController::class)->prefix('AddressType')->group(function () {
Route::post('list', 'list');
Route::post('create', 'create');
Route::get('get/{id}', 'get');
Route::post('update/{id}', 'update');
Route::post('delete/{id}', 'delete');
})->middleware('check:admin');

Route::controller(UserAddressController::class)->prefix('UserAddress')->group(function () {
Route::post('list', 'list');
Route::post('create', 'create');
Route::get('get/{id}', 'get');
Route::post('update/{id}', 'update');
Route::post('delete/{id}', 'delete');
})->middleware('check:customer');

Route::controller(ProductController::class)->prefix('Product')->group(function () {
// Route::post('list', 'list')->middleware('check:partner|customer|admin');
Route::post('create', 'create')->middleware('check:partner');
Route::get('get/{id}', 'get')->middleware('check:partner');
Route::post('update/{id}', 'update')->middleware('check:partner');
Route::post('delete/{id}', 'delete')->middleware('check:partner');
Route::post('partner/product/list', 'partnerProduct')->middleware('check:partner');
});

Route::controller(ProductRatingController::class)->prefix('rating')->group(function () {
Route::post('list', 'list')->middleware('check:admin|customer');
Route::post('create', 'create')->middleware('check:admin|customer');
});

Route::controller(WishlistController::class)->prefix('wishlist')->group(function () {
Route::post('list', 'list')->middleware('check:admin|customer');
Route::post('create', 'create')->middleware('check:admin|customer');
Route::post('update/{id}', 'update')->middleware('check:admin|customer');
Route::post('delete', 'delete')->middleware('check:admin|customer');
});

Route::controller(CartItemController::class)->prefix('cart')->group(function () {
Route::post('list', 'list')->middleware('check:admin|customer');
Route::post('create', 'create')->middleware('check:admin|customer');
Route::post('update/{id}', 'update')->middleware('check:admin|customer');
Route::post('delete', 'delete')->middleware('check:admin|customer');
});

Route::controller(OrderController::class)->prefix('order')->group(function () {
Route::post('list', 'list');
Route::post('create', 'create');
Route::get('get/{id}', 'get');
Route::post('order-status/{order_num}', 'orderStatus');
Route::get('invoice', 'downloadInvoice');
Route::post('cancel/orderList', 'cancelOrder')->middleware('check:admin');
Route::get('invoice/download/{id}/generate', 'downloadInvoice');
// Route::get('approve/{id}', 'approve')->name('admin.approve');
// Route::get('decline/{id}', 'decline')->name('admin.decline');
Route::post('invoice/list', 'invoiceList')->middleware('check:admin');
Route::post('partner/list', 'partnerOrder');
});
});
});
