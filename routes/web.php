<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthAdminController;



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


Route::get('/cancel', function () {
    return view('cancel');
})->name('cancel');

//success Redsys
Route::get('/success', function () {
    return view('RecepcionaPet');
})->name('success');

//success Paypal
Route::get('/success-paypal', function () {
    return view('SuccessPaypal');
})->name('successPaypal');

// // View email !! No Borrar

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
//         'urlTrack' => 'https://www.ordertracker.com/es/track/123456789',
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
//         'discount' => '10',
//         'code'=> 'ABCDEFGH',
//     ];

//     $mailConfirm = [
//         'name' => 'John Doe',
//         'email' => 'jonhdoe@jonhdoe',
//         'phone' => '123456789',
//         'address' => '1234 Main St',
//         'city' => 'New York',
//         'state' => 'NY',
//         'zipcode' => '12345',
//         'country' => 'USA',
//         'urlTrack' => 'https://www.ordertracker.com/es/track/123456789',
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
//         'discount' => '10',
//         'content' => 'Si aún no ha realizado el pago por Bizum, puede hacerlo enviando el total del importe indicado en su pedido al número de teléfono 613 03 60 42, indicando como concepto el número de pedido.'
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

//     return view('emails.subscribe', compact('mailData', 'mailConfirm', 'dataOne'));
// });

