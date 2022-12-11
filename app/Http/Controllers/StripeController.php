<?php

namespace App\Http\Controllers;

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
use LaravelDaily\Invoices\Invoice;
use Illuminate\Support\Facades\Mail;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Sequence;

class StripeController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripe($token_id)
    {
        $order = Order::where('token_id', $token_id)->first();

        if (!$order) {
            return redirect()->route('errors.404');
        }

        $user = User::where('id', $order->user_id)->first();

        if (auth('api')->user()->id != $user->id) {
            return redirect()->route('errors.404');
        }

        if ($order->paid == 3) {
            return redirect()->route('errors.404');
        }

        $user_profile = UserProfile::where('id', $order->user_profile_id)->first();
        $order->paid = 2;
        $order->save();
        return view('payment', compact('order', 'user_profile'));
    }
   
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePost(Request $request)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        Stripe\Charge::create ([
                "amount" => $request->amount * 100,
                "currency" => "eur",
                "source" => $request->stripeToken,
                "description" => "Test payment from trivicare.com"
        ]);
   
        $order = Order::findOrFail($request->orderId);

        $user_profile = UserProfile::where('id', $order->user_profile_id)->first();

        $user = User::where('id', $order->user_id)->first();

        $products = json_decode($order->products);

        foreach ($products as $item) {
            $product = Product::findOrFail($item->id);
            $product->stock = $product->stock - $item->cartQuantity;
            $product->sold = $product->sold + $item->cartQuantity;
            $product->save();
        }

        $order->status = 1;
        $order->paid = 3;
        $order->token_id = null;
        $order->save();
           
        $client = new Party([
            'name'          => 'Trivicare Natural Cosmetics',
            'phone'         => '(34) 616 64 18 49',
            'custom_fields' => [
                'email' => 'info@trivicare.com',
            ],
        ]);

        $customer = new Party([
            'name'          =>  $user_profile->name . ' ' . $user_profile->lastname,
            'address'       => $user_profile->address,
            'custom_fields' => [
                'email' => $user->email,
                'teléfono' => $user_profile->phone,
            ],
        ]);

        foreach($products as $item) {
            $items[] = (new InvoiceItem())->title($item->name)->pricePerUnit($item->price_base)->quantity($item->cartQuantity)->discountByPercent($item->discount)->taxByPercent(21);
        }

        if($order->coupon == null) {
            $discnt = null; 
        } else {
            $coupon = Cupon::where('code', $order->coupon)->first();
            $discnt = $coupon->discount;
        }


        $invoice = Invoice::make('receipt')
            // ability to include translated invoice status
            // in case it was paid
            ->sequence($order->id)
            ->status(__('invoices::invoice.paid'))
            ->serialNumberFormat('{SEQUENCE}/{SERIES}')
            ->seller($client)
            ->buyer($customer)
            ->date(now()->subWeeks(3))
            ->dateFormat('d/m/Y')
            ->payUntilDays(14)
            ->currencySymbol('€')
            ->currencyCode('EUR')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->filename('TNC' . $order->id . '-' . $user_profile->name . '-' . str_replace(' ', '-', $user_profile->lastname))
            ->addItems($items)
            ->setCustomData($discnt)
            ->taxableAmount($order->subTotal)
            ->totalAmount($order->total)
            ->shipping($order->shipping)
            ->logo(public_path('img/logofactura.png'))
            // You can additionally save generated invoice to configured disk
            ->save('public');

        $link = $invoice->url();
        $filename = $invoice->filename;

        // Then send email to party with link
        $inv = InvoiceOrder::create([
            'filename' => $filename,
            'order_id' => $order->id,
            'user_profile_id' => $user_profile->id,
            'url' => $link,
        ]);

        //send email

        $mailData = [
            'title' => 'Confirmación de pedido',
            'body' => 'Gracias por tu pedido. Te adjuntamos la factura de tu pedido.',
            'date' => $order->order_date,
            'order' => '#TNC' . $order->id,
            'user' => $user_profile->name . ' ' . $user_profile->lastname,
            'address' => $user_profile->address,
            'city' => $user_profile->city,
            'zipcode' => $user_profile->zipcode,
            'state' => $user_profile->state,
            'country' => $user_profile->country,
            'email' => $user->email,
            'products' => $products,
            'subTotal' => round($order->total * 1.21, 2),
            'shipping' => $order->shipping,
            'total' => round(($order->total * 1.21) + $order->shipping, 2),
            'invoice' => $filename
        ];
         
        Mail::to($user->email)->send(new OrderMail($mailData));

        //send email if is the first order

        if($user->orders()->count() == 1){
            $cupon = Cupon::create([
                'code' => 'ORDERFIRST' . $user->id . $user_profile->id,
                'discount' => 10,
                'validity' => Carbon::now()->addDays(30)->format('Y-m-d'),
                'status' => 2,
            ]);
            $dataOne = [
                'title' => 'Gracias por tu primer pedido',
                'body' => 'Te damos la bienvenida a la familia Trivicare. Te adjuntamos un cupón de descuento del 10% para tu próxima compra.',
                'cupon' => $cupon->code,
            ];
            Mail::to($user->email)->send(new FirstOrderMail($dataOne));
        } 
            
           
        return view('success', compact('order'));

    }
}




