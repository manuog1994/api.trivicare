<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Order\OrderController;

use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Cart\ShoppingCartController;
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
Route::post('login', [LoginController::class, 'login'])->name('login');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('index', [RegisterController::class, 'index'])->name('index');
Route::get('index-profile', [RegisterController::class, 'indexProfile'])->name('index-profile');

Route::get('show/{user}', [RegisterController::class, 'show'])->name('show');
Route::get('show-profile/{userProfile}', [RegisterController::class, 'showProfile'])->name('show-profile');

Route::post('register', [RegisterController::class, 'register'])->name('register');
Route::post('register-profile', [RegisterController::class, 'registerUserProfile'])->middleware('auth:api')->name('register-profile');

Route::put('update', [RegisterController::class, 'update'])->middleware('auth:api')->name('update');
Route::put('update-profile/{user_profile}', [RegisterController::class, 'updateUserProfile'])->middleware('auth:api')->name('update-profile');

Route::delete('destroy/{user}/{user_profile}', [RegisterController::class, 'destroy'])->middleware('auth:api')->name('destroy');



// Categories
Route::apiResource('categories', CategoryController::class)->names('categories');

// Products
Route::apiResource('products', ProductController::class )->names('products');

// Carts
Route::apiResource('carts', ShoppingCartController::class)->names('carts');

Route::post('cart/delete', [ShoppingCartController::class, 'destroyAll'])->name('cart.delete.all');
Route::post('cart/details', [ShoppingCartController::class, 'cartDetails'])->name('cart.details');

// Orders
Route::post('orders/{id}', [OrderController::class, 'orderItems'])->name('orders.items');