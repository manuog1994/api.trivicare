<?php

use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\Api\Tag\TagController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\GoogleController;
use App\Http\Controllers\Api\Cupon\CuponController;
use App\Http\Controllers\Api\Error\ErrorController;
use App\Http\Controllers\Api\Guest\GuestController;
use App\Http\Controllers\Api\Image\ImageController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Review\ReviewController;
use App\Http\Controllers\VerificationEmailController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Reserve\ReserveController;
use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\Contact\ContactFormController;
use App\Http\Controllers\Api\Invoices\InvoiceOrderController;



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
    return $request->user()->load(['user_profile', 'roles']);
});

// User

Route::post('update-email/{id}', [RegisterController::class, 'updateEmail']);
Route::post('update-password/{id}', [RegisterController::class, 'updatePassword']);

// User Profile
Route::get('show-profile/{userId}', [RegisterController::class, 'showProfile'])->name('show-profile');

Route::post('register-profile', [RegisterController::class, 'registerUserProfile'])->middleware('auth:sanctum')->name('register-profile');

Route::delete('delete-profile/{user_profile}', [RegisterController::class, 'deleteProfile'])->middleware('auth:sanctum')->name('delete-profile');

Route::delete('destroy/{user}', [RegisterController::class, 'destroy'])->middleware('auth:sanctum')->name('destroy');



// Categories
Route::apiResource('categories', CategoryController::class)->names('categories');

// Products
Route::apiResource('products', ProductController::class )->names('products');
Route::put('products/status/{product}', [ProductController::class, 'status'])->name('products.status');

// Reviews
Route::apiResource('reviews', ReviewController::class)->names('reviews');

// Tags
Route::apiResource('tags', TagController::class)->names('tags');

// Cupons
Route::apiResource('cupons', CuponController::class)->names('cupons');

// Images
Route::apiResource('images', ImageController::class)->names('images');

// Products-Tags
Route::delete('products/{product}/tags/{tag}', [TagController::class, 'delete'])->name('products.tags.delete');

// Orders
Route::apiResource('orders', OrderController::class)->names('orders');
Route::get('users', [OrderController::class, 'getUser'])->name('orders.getUser');
Route::put('orders/status/{order}', [OrderController::class, 'status'])->name('orders.status');
Route::post('order-paid/{token_id}', [OrderController::class, 'paid'])->name('order-paid');
Route::post('order-paid-paypal/{token_id}', [OrderController::class, 'paidPaypal'])->name('order-paid-paypal');

// Invoice
Route::apiResource('invoice', InvoiceOrderController::class)->names('invoice');
Route::get('invoices/{id}', [InvoiceOrderController::class, 'downloadFile'])->name('invoices.downloadFile');

// Resend Email Verification
Route::post('resend-email/{id}', [ VerificationEmailController::class, 'resendEmail' ])->name('resend.email');

// Send Email Forgot Password
Route::post('forgot-password', [ ForgotPasswordController::class, 'forgotPassword' ])->name('forgot.password');

// Newsletter
Route::post('newsletter', [ NewsletterController::class, 'subscribe' ])->name('newsletter');
Route::post('unsubscribe-newsletter', [ NewsletterController::class, 'unsubscribe' ])->name('unsubscribe-newsletter');

// Stripe
Route::post('stripe', [StripeController::class, 'stripePost'])->name('stripe.post');

//Google auth

Route::get('auth/url', [GoogleController::class, 'getAuthUrl']);
Route::post('auth/code', [GoogleController::class, 'postLogin']);

Route::middleware('auth:sanctum')->get('auth/user', function (Request $request) {
    return $request->user()->load(['user_profile', 'roles']);
});


// Contact Form
Route::post('contact', [ContactFormController::class, 'contactPost'])->name('contact.post');

// Guest
Route::post('guest-store', [GuestController::class, 'store'])->name('guests.store');
Route::get('guests-show/{$id}', [GuestController::class, 'show'])->name('guests.show');
Route::delete('guests-delete/{$id}', [GuestController::class, 'destroy'])->name('guests.destroy');

// Reserve

Route::post('reserve', [ReserveController::class, 'store'])->name('reserve.store');

//Error message

Route::post('error-message', [ErrorController::class, 'sendError'])->name('error-message');


