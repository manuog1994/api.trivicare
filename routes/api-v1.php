<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthAdminController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\Api\Pdf\PdfController;
use App\Http\Controllers\Api\Tag\TagController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\GoogleController;
use App\Http\Controllers\Api\Cupon\CuponController;
use App\Http\Controllers\Api\Error\ErrorController;
use App\Http\Controllers\Api\Guest\GuestController;
use App\Http\Controllers\Api\Image\ImageController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\State\StateController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Pickup\PickupController;
use App\Http\Controllers\Api\Review\ReviewController;
use App\Http\Controllers\Api\Search\SearchController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Reserve\ReserveController;
use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\VisitConter\VisitController;
use App\Http\Controllers\Api\Contact\ContactFormController;
use App\Http\Controllers\Api\RedsysPay\RedsysPayController;
use App\Http\Controllers\Api\Invoices\InvoiceOrderController;
use App\Http\Controllers\Api\Suggestion\SuggestionController;
use App\Http\Controllers\Api\Notification\NotificationController;



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


Route::middleware('auth:sanctum')->get('user', function (Request $request) {
    return $request->user()->load(['user_profile', 'roles', 'notifications']);
});

Route::post('/auth/login', [AuthAdminController::class, 'login']);

Route::group([

    'middleware' => 'auth:admin',
    'prefix' => 'auth'

], function ($router) {

    Route::post('logout', [AuthAdminController::class, 'logout']);
    Route::post('refresh', [AuthAdminController::class, 'refresh']);
    Route::get('me', [AuthAdminController::class, 'me']);

});

// User

Route::post('update-email/{id}', [RegisterController::class, 'updateEmail']);
Route::post('update-password/{id}', [RegisterController::class, 'updatePassword']);

// User Profile
Route::get('user-profile', [RegisterController::class, 'indexUserProfile'])->name('user-profile');
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
Route::get('review/{token_id}', [OrderController::class, 'orderToken'])->name('review.token');

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
Route::put('orders/paid/{order}', [OrderController::class, 'updatePaid'])->name('orders.update.paid');
Route::post('order-paid/{token_id}', [OrderController::class, 'paid'])->name('order-paid');
Route::post('order-paid-paypal/{token_id}', [OrderController::class, 'paidPaypal'])->name('order-paid-paypal');
Route::post('verify-email', [OrderController::class, 'verifyEmail'])->name('verify-email');

// Invoice
Route::apiResource('invoice', InvoiceOrderController::class)->names('invoice');
Route::get('invoices/{id}', [InvoiceOrderController::class, 'downloadFile'])->name('invoices.downloadFile');
Route::get('multiples-invoices', [InvoiceOrderController::class, 'multipleDownloads'])->name('invoices.multipleDownloads');

// Pdf
Route::get('invoice-pdf/{id}', [PdfController::class, 'show'])->name('pdf.filename');

// Send Email Forgot Password
Route::post('forgot-password', [ ForgotPasswordController::class, 'forgotPassword' ])->name('forgot.password');

// Newsletter
Route::post('newsletter', [ NewsletterController::class, 'subscribe' ])->name('newsletter');
Route::post('unsubscribe-newsletter', [ NewsletterController::class, 'unsubscribe' ])->name('unsubscribe-newsletter');
Route::post('send-newsletter', [ NewsletterController::class, 'sendNewsletter' ])->name('send-newsletter');

//Google auth

Route::get('auth/url', [GoogleController::class, 'getAuthUrl']);
Route::post('auth/code', [GoogleController::class, 'postLogin']);

Route::middleware('auth:sanctum')->get('auth/user', function (Request $request) {
    return $request->user()->load(['user_profile', 'roles']);
});


// Contact Form
Route::post('contact', [ContactFormController::class, 'contactPost'])->name('contact.post');

// Guest
Route::get('guests', [GuestController::class, 'index'])->name('guests.index');
Route::post('guest-store', [GuestController::class, 'store'])->name('guests.store');
Route::get('guests-show/{$id}', [GuestController::class, 'show'])->name('guests.show');
Route::delete('guests-delete/{$id}', [GuestController::class, 'destroy'])->name('guests.destroy');

// Reserve

Route::post('reserve', [ReserveController::class, 'store'])->name('reserve.store');

//Error message

Route::post('error-message', [ErrorController::class, 'sendError'])->name('error-message');

// Suggestions Mailbox
Route::post('suggestions', [SuggestionController::class, 'sendSuggestion'])->name('suggestions.post');

//Notifications
Route::get('notifications/{user}', [NotificationController::class, 'show'])->name('notifications.show');
Route::put('notifications/{notification}', [NotificationController::class, 'read'])->name('notifications.read');
Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
Route::post('notification-delete', [NotificationController::class, 'delete'])->name('notification.delete');


// Invoices
Route::post('new-invoice', [InvoiceOrderController::class, 'newInvoice'])->name('new-invoice');

// Visit Counter
Route::post('visit', [VisitController::class, 'store'])->name('visit-counter');
Route::get('visits', [VisitController::class, 'index'])->name('visits');

// Pickup Points
Route::apiResource('pickup-points', PickupController::class)->names('pickup-points');

// Locations
Route::apiResource('locations', StateController::class)->names('locations');

// Redsys Payment
Route::post('redsys', [RedsysPayController::class, 'payment'])->name('redsys.post');

// Search
Route::get('search', [SearchController::class, 'index'])->name('search');




