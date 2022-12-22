<?php

namespace App\Http\Controllers;

use Error;
use Stripe;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Cupon;
use App\Models\Order;
use App\Mail\OrderMail;
use App\Models\Product;
use App\Models\UserProfile;
use App\Mail\FirstOrderMail;
use App\Models\InvoiceOrder;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use LaravelDaily\Invoices\Invoice;
use Illuminate\Support\Facades\Mail;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Http;

class StripeController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
   
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePost(Request $request)
    {
        Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        header('Content-Type: application/json');

        try {

            $paymentIntent = Stripe\PaymentIntent::create([
                'amount' => $request->total * 100,
                'currency' => 'eur',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'description' => 'Pago realizado en trivicare.com',
            ]);

            $order = Order::where('id', $request->orderId)->first();
            $order->paid = 'PROCESANDO';
            $order->token_id = $paymentIntent->client_secret;
            $order->save();

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ], 200);

        }catch (Error $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }

    }
}




