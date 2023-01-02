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
    Route::post('refresh', 'refresh')->name('refresh');
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


// View email !! No Borrar

// Route::get('/email', function () {
//     $products = [
//         [
//             'name' => 'Product 1',
//             'price' => '10',
//             'cartQuantity' => '1',
//             'discount' => 0,
//             'subTotal' => 10.50,
//         ],
//         [
//             'name' => 'Product 2',
//             'price' => '20',
//             'cartQuantity' => '2',
//             'discount' => 0,
//             'subTotal' => 40.50,
//         ],
//         [
//             'name' => 'Product 3',
//             'price' => '30',
//             'cartQuantity' => '3',
//             'discount' => 0,
//             'subTotal' => 90.50,
//         ],
//     ];

//     $encode = json_encode($products);
//     $decode = json_decode($encode);




//     $mailData = [
//         'name' => 'John Doe',
//         'email' => 'jonhdoe@jonhdoe',
//         'phone' => '123456789',
//         'address' => '1234 Main St',
//         'city' => 'New York',
//         'state' => 'NY',
//         'zipcode' => '12345',
//         'country' => 'USA',
//         'urlTrack' => 'https://trivicare.com',
//         'track' => '123456789',
//         'order' => '123456789',
//         'date' => '2021-01-01',
//         'products' => $decode,
//         'subTotal' => '100.00',
//         'shipping' => '10.00',
//         'total' => '110.00',
//         'user' => 'John Doe',
//         'shippingMethod' => 'gls',
//         'password' => '12223112',

//     ];

//     $cupon = [
//         'code' => 'ORDERFIRST2113',
//         'discount' => '10',
//         'validity' => '2021-01-01',
//         'status' => '1',
//     ];

//     $dataOne = [
//         'title' => 'Gracias por tu primer pedido',
//         'body' => 'Te damos la bienvenida a la familia Trivicare. Te adjuntamos un cupón de descuento del 10% para tu próxima compra.',
//         'cupon' => $cupon['code'],
//     ];

//     return view('emails.unsubscribe', compact('mailData', 'dataOne'));
// });

