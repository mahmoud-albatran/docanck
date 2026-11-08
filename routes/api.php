<?php

use App\Http\Controllers\Api\Dashboard\CategoryController;
use App\Http\Controllers\Api\Dashboard\DiscountCodeController;
use App\Http\Controllers\Api\Dashboard\PermissionController;
use App\Http\Controllers\Api\Dashboard\ProductController;
use App\Http\Controllers\Api\Dashboard\RoleController;
use App\Http\Controllers\Api\Dashboard\RolePermissionController;
use App\Http\Controllers\Api\Dashboard\SubCategoryController;
use App\Http\Controllers\Api\Dashboard\UserController;
use App\Http\Controllers\Api\v1\CheckoutController;
use App\Http\Controllers\Api\v1\SliderController;
use App\Http\Controllers\Api\v1\StoreController;
use App\Http\Controllers\Api\v1\UserAddressController;
use App\Http\Controllers\Api\v2\LoginController as V2LoginController;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/auth-permission', function (Request $request) {
    $user = $request->user();
    return response()->json([
        'auth' => $user,
        'permissions' => $user->getAllPermissions()->pluck('name'), // تأكد من إضافة هذا السطر
    ]);
})->middleware('auth:sanctum');



// authentication start
Route::post('/auth/register',[LoginController::class, 'register'])->middleware('guest:sanctum');
Route::post('device-tokens', [LoginController::class, 'deviceToken'])->middleware('auth:sanctum');

Route::post('/login', [LoginController::class, 'submit']);
Route::post('/login/verify', [LoginController::class, 'verifiy']);

Route::post('/v2/login', [V2LoginController::class, 'submit']);

// application start
Route::post('updateprofile', [LoginController::class, 'updateprofile'])->middleware('auth:sanctum');
Route::post('updateotherprofile', [LoginController::class, 'updatephoto'])->middleware('auth:sanctum');
Route::post('updatepassword', [LoginController::class, 'updatepass'])->middleware('auth:sanctum');
Route::get('/logout', [LoginController::class, 'destroy'])->middleware('auth:sanctum');
Route::get('/notifications', [LoginController::class, 'notifies'])->middleware('auth:sanctum');
Route::put('notifications/read_at', [LoginController::class, 'notificationsread'])->middleware('auth:sanctum');
Route::get('notifications/count', [LoginController::class, 'notificationscount'])->middleware('auth:sanctum');

Route::get('/delete-account', [V2LoginController::class, 'deleteAccount'])->middleware('auth:sanctum');


// no auth
Route::get('categories', [CategoryController::class, 'index']);
Route::get('sub-categories', [SubCategoryController::class, 'index']);
Route::get('products', [ProductController::class, 'index']);
Route::get('sliders', [SliderController::class, 'index']);
Route::get('products/{product}', [ProductController::class, 'show']);


Route::group(['middleware' => 'auth:sanctum'], function(){

    Route::apiResource('categories', CategoryController::class)->except(['index']);
    Route::post('categories/delete-multiple', [CategoryController::class, 'multipleDelete']);

    Route::get('sub-categories/get-select', [SubCategoryController::class, 'getSelect']);
    Route::apiResource('sub-categories', SubCategoryController::class)->except(['index']);
    Route::post('sub-categories/delete-multiple', [SubCategoryController::class, 'multipleDelete']);


    Route::get('products/get-select/{id?}', [ProductController::class, 'getSelect']);
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    Route::post('products/delete-multiple', [ProductController::class, 'multipleDelete']);



    Route::get('users/get-select', [UserController::class, 'getSelect']);
    Route::post('users/import', [UserController::class, 'import']);
    Route::get('users/permissions', [UserController::class, 'permissions']);
    Route::apiResource('users', UserController::class);
    Route::post('users/delete-multiple', [UserController::class, 'multipleDelete']);


    Route::apiResource('addresses', UserAddressController::class);
    Route::post('addresses/delete-multiple', [UserAddressController::class, 'multipleDelete']);


    Route::apiResource('sliders', SliderController::class)->except(['index']);
    Route::post('sliders/delete-multiple', [SliderController::class, 'multipleDelete']);

    Route::apiResource('discount-codes', DiscountCodeController::class);
    Route::get('examination-codes', [DiscountCodeController::class, 'examination']);

    Route::apiResource('orders', CheckoutController::class);
    Route::post('orders/delete-multiple', [CheckoutController::class, 'multipleDelete']);


    Route::apiResource('store', StoreController::class);






    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('role-permissions', RolePermissionController::class);


});
