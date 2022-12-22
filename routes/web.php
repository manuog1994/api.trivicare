<?php

use App\Models\User;
//use App\Http\Controllers\StrapiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\AuthController;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\VerificationEmailController;
use Illuminate\Auth\AuthenticationException;


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

Route::get('/', function () {
    return redirect('https://trivicare.com');
});

Route::get('/error', function () {
    return view('errors.404');
})->name('error.404');

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login')->name('login');
    Route::post('register', 'register')->name('register');
    Route::post('logout', 'logout')->name('logout');
});

//Route::get('/payment/{token_id}', [StrapiController::class, 'payment'])->name('payment');
Route::get('stripe/{token_id}', [StripeController::class, 'stripe']);

Route::get('/cancel', function () {
    return view('cancel');
})->name('cancel');

//Verify Email
Route::get('/verify-email/{token}', [ VerificationEmailController::class, 'verify' ])->name('verify.email');


//Auth::routes(['verify' => true]);

//Google auth

