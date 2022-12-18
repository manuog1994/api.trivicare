<?php

use Illuminate\Support\Facades\Auth;
//use App\Http\Controllers\StrapiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\VerificationEmailController;

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

// Route::get('/email', function () {
//     $mailData = [
//         'title' => 'Aqui tiene su nueva contraseña.',
//         'body' => 'Hemos generado una contraseña temporal para que acceda a su perfil.',
//         'order' => '#123456',
//         'date' => '2021-01-01',
//         'url' => 'http://localhost:8000',
//         'password' => '123456',
//     ];
//     return view('emails.forgot-password', compact('mailData'));
// })->name('email');
//Auth::routes(['verify' => true]);



