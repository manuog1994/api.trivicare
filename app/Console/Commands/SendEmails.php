<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Cupon;
use App\Models\Guest;
use App\Models\Order;
use App\Mail\NewOrder;
use App\Mail\OrderMail;
use App\Models\Product;
use App\Models\UserProfile;
use App\Mail\FirstOrderMail;
use App\Models\InvoiceOrder;
use App\Mail\ConfirmOrderMail;
use Illuminate\Console\Command;
use LaravelDaily\Invoices\Invoice;
use Illuminate\Support\Facades\Mail;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send emails to users for confirmation of orders';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       // enviar email de confirmacion de la ordenes que no han sido enviadas y que hayan sido creadas hace un máximo de 10 minutos
        $orders = Order::where('email_sent', false)->get();

        foreach ($orders as $order) {
            // si el usuario es invitado o no
            if($order->guest_id == null){
                $user = User::where('id', $order->user_id)->first();
                $user_profile = UserProfile::where('id', $order->user_profile_id)->first();
            }else{
                $user = Guest::where('id', $order->guest_id)->first();
                $user_profile = Guest::where('id', $order->guest_id)->first();
            }

            // decode the products
            $products = json_decode($order->products);

            // get the products from the database
            foreach ($products as $product) {
                $product = Product::where('id', $product->id)->first();
            } 

            // si el pago es por paypal o redsys y el estado es pagado
            if ($order->payment_method == 'paypal' && $order->paid == 'PAGADO' || $order->payment_method == 'redsys' && $order->paid == 'PAGADO' || $order->payment_method == 'transfer_bank' && $order->paid == 'PAGADO' || $order->payment_method == 'bizum' && $order->paid == 'PAGADO' || $order->payment_method == 'paylater' && $order->paid == 'PAGADO') {

                // fill the data for the invoice
                $client = new Party([
                    'name' => 'Trivicare Natural Cosmetics',
                    'custom_fields' => [
                        'Nombre' => 'Cristina Triviño Cortés',
                        'DNI' => '45923103S',
                        'email' => 'info@trivicare.com',
                        'teléfono' => '613036942',
                    ],
                ]);
        
                $customer = new Party([
                    'name'          =>  $user_profile->name . ' ' . $user_profile->lastname,
                    'address'       => $user_profile->address,
                    'postal_code'   => $user_profile->zipcode,
                    'city'          => $user_profile->city,
                    'state'         => $user_profile->state,
                    'country'       => $user_profile->country,
                    'custom_fields' => [
                        'DNI' => $user_profile->dni,
                        'email' => $user->email,
                        'teléfono' => $user_profile->phone,
                    ],
                ]);

                foreach($products as $item) {
                    if(isset($item->variation)) {
                        $item->name = $item->name . ' -- ' . $item->variation;
                    }

                    // if(isset($item->discount)) {
                    //     foreach($item->discount as $discount) {
                    //         $item->discount = $discount->discount;
                    //     }
                    // }
                    
                    $items[] = (new InvoiceItem())->title($item->name)->pricePerUnit($item->price_base)->quantity($item->cartQuantity)->discountByPercent( $item->discount === null ? 0 : round($item->discount->discount))->taxByPercent(21);
                }

                   
                if($order->coupon == null) {
                    $discnt = null; 
                } else {
                    $coupon = Cupon::where('code', $order->coupon)->first();
                    $discnt = $coupon->discount;
                }
                //make a generator number for the invoice
                $invt = InvoiceOrder::all();
                $year = Carbon::now()->format('y');

                if($invt->count() == 0){

                    $invoice_number = '#TNC'. $year . '/' . str_pad(1, 5, '0', STR_PAD_LEFT);
        
                }else {
        
                    $last = substr($invt->last()->invoice_number, 0, -6);
                    $headerInv = '#TNC' . $year;
        
                    if($last != $headerInv) {
                        $invoice_number = '#TNC'. $year . '/' . str_pad(1, 5, '0', STR_PAD_LEFT);
                    } else {
                        $last_invoice = $invt->last();
                        $invoice_number = str_replace('#TNC' . $year . '/', '', $last_invoice->invoice_number);
                        $invoice_number += 1;
                        $invoice_number = '#TNC'. $year . '/' . str_pad($invoice_number, 5, '0', STR_PAD_LEFT);
                    } 
        
                }
        
                $dateInv= date('dmY');
        
                $invoice = Invoice::make('receipt')
                    //->series('#TNC'. strval($year)) 
                    //->sequence(number_format(substr($invoice_number, -5)))
                    ->serialNumberFormat($invoice_number)
                    ->status(__('invoices::invoice.paid'))
                    ->seller($client)
                    ->buyer($customer)
                    ->date(now())
                    ->dateFormat('d/m/Y')
                    ->payUntilDays(14)
                    ->currencySymbol('€')
                    ->currencyCode('EUR')
                    ->currencyFormat('{SYMBOL}{VALUE}')
                    ->currencyThousandsSeparator('.')
                    ->currencyDecimalPoint(',')
                    ->filename($dateInv . '_' . substr($invoice_number, 7, 5))
                    ->addItems($items)
                    ->setCustomData($discnt)
                    ->taxableAmount($order->subTotal)
                    ->totalAmount($order->total)
                    ->shipping($order->shipping)
                    ->logo(public_path('img/logofactura.png'))
                    // You can additionally save generated invoice to configured disk
                    ->save('public');
        
                $link = $invoice->url();
                // eliminar los '/' y '-'del nombre del archivo
                $filename = $invoice->filename;
         
                // Then send email to party with link
                $inv = InvoiceOrder::create([
                    'user_profile_id' => $order->user_profile->id,
                    'order_id' => $order->id,
                    'filename' => $filename,
                    'url' => $link,
                    'invoice_number' => $invoice_number,
                ]);
        
                //send email
        
                $mailData = [
                    'title' => 'Confirmación de pedido',
                    'body' => 'Gracias por tu pedido. Te adjuntamos la factura de tu pedido.',
                    'date' => $order->order_date,
                    'order' => $invoice_number,
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
                    'invoice' => $filename,
                    'discount' => $order->coupon == null ? 0 : $order->coupon,
                ];
                
                sleep(10);
                
                Mail::to($user->email)->send(new OrderMail($mailData));

                $order->email_sent = true;
                $order->save();
        
                //send email if is the first order
        
                if($user->orders()->count() == 1 && $order->guest_id == null){
                    $cupon = Cupon::create([
                        'code' => 'ORDERFIRST' . $user->id . $user_profile->id,
                        'discount' => 10,
                        'validity' => Carbon::now()->addDays(30)->format('Y-m-d'),
                        'status' => 2,
                        'unique' => true,
                    ]);
                    $dataOne = [
                        'title' => 'Gracias por tu primer pedido',
                        'body' => 'Te damos la bienvenida a la familia Trivicare. Te adjuntamos un cupón de descuento del 10% para tu próxima compra.',
                        'cupon' => $cupon->code,
                    ];
                    sleep(5);
                    Mail::to($user->email)->send(new FirstOrderMail($dataOne));
                } 
        
                //extraer el cupón a través del código
                $coupon = Cupon::where('code', $order->coupon)->first();

                //verifica si es único y eliminar en caso afirmativo
                if(isset($coupon->unique)) {
                    if($coupon->unique == true) {
                        $coupon->delete();
                    }
                }
        
                $orderToMail = [
                    'name' => $user_profile->name . ' ' . $user_profile->lastname,
                    'state' => $user_profile->state,
                ];
                sleep(5);

                if ($order->payment_method != 'bizum' || $order->payment_method != 'transfer_bank') {
                    Mail::to(config('services.mailorders.email'))->send(new NewOrder($orderToMail));
                }
                
            } 

            if ($order->payment_method == 'bizum' && $order->paid == 'PENDIENTE' && $order->confirmation_sent == false) {
                //send email
    
                $mailConfirm = [
                    'title' => 'Confirmación de pedido',
                    'body' => 'Gracias por tu pedido. Quedamos a la espera de que nos confirmes el pago.',
                    'content' => 'Si aún no ha realizado el pago por Bizum, puede hacerlo enviando el total del importe indicado en su pedido al número de teléfono 613 03 60 42, indicando como concepto su número de DNI, NIF o NIE.',
                    'date' => $order->order_date,
                    'order' => '#' . $order->id,
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
                    'discount' => $order->coupon == null ? 0 : $order->coupon,
                ];

                // descansar 5 segundos 
                sleep(5);
                
                Mail::to($user->email)->send(new ConfirmOrderMail($mailConfirm));

                
                $order->confirmation_sent = true;
                $order->save();

                $orderToMail = [
                    'name' => $user_profile->name . ' ' . $user_profile->lastname,
                    'state' => $user_profile->state,
                ];
                sleep(5);
                Mail::to(config('services.mailorders.email'))->send(new NewOrder($orderToMail));

            } else if ($order->payment_method == 'transfer_bank' && $order->paid == 'PENDIENTE' && $order->confirmation_sent == false) {
                //send email
    
                $mailConfirm = [
                    'title' => 'Confirmación de pedido',
                    'body' => 'Gracias por tu pedido. Quedamos a la espera de que nos confirmes el pago.',
                    'content' => 'Puede realizar el pago haciendo una transferencia bancaria con el total del importe indicado en su pedido a la siguiente cuenta bancaria: ES61 0049 4398 0328 1008 8938 indicando como concepto su número de DNI, NIF o NIE.',
                    'date' => $order->order_date,
                    'order' => '#' . $order->id,
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
                    'discount' => $order->coupon == null ? 0 : $order->coupon,
                ];

                // descansar 5 segundos 
                sleep(5);
                
                Mail::to($user->email)->send(new ConfirmOrderMail($mailConfirm));
                $order->confirmation_sent = true;
                $order->save();

                $orderToMail = [
                    'name' => $user_profile->name . ' ' . $user_profile->lastname,
                    'state' => $user_profile->state,
                ];
                sleep(5);
                Mail::to(config('services.mailorders.email'))->send(new NewOrder($orderToMail));
            }
        }


    }
}
