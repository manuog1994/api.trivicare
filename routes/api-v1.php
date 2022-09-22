<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Order\OrderController;

use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Review\ReviewController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Category\CategoryController;



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
// Autentication User

Route::middleware('auth:sanctum')->get('user', function (Request $request) {
    return $request->user();
});


// User

Route::post('update-email/{id}', [RegisterController::class, 'updateEmail']);
Route::post('update-password/{id}', [RegisterController::class, 'updatePassword']);

// User Profile
Route::get('show-profile/{userId}', [RegisterController::class, 'showProfile'])->middleware('auth:sanctum')->name('show-profile');

Route::post('register-profile', [RegisterController::class, 'registerUserProfile'])->middleware('auth:sanctum')->name('register-profile');

Route::delete('delete-profile/{user_profile}', [RegisterController::class, 'deleteProfile'])->middleware('auth:sanctum')->name('delete-profile');

Route::delete('destroy/{user}', [RegisterController::class, 'destroy'])->middleware('auth:sanctum')->name('destroy');



// Categories
Route::apiResource('categories', CategoryController::class)->names('categories');

// Products
Route::apiResource('products', ProductController::class )->names('products');

// Reviews
Route::apiResource('reviews', ReviewController::class)->names('reviews');

// Orders
//Route::post('orders/{id}', [OrderController::class, 'orderItems'])->name('orders.items');


