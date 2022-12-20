<?php

namespace App\Http\Controllers\Api\Order;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Cupon;
use App\Models\Order;
use App\Mail\OrderMail;
use App\Models\Product;
use App\Mail\SendOrderMail;
use App\Models\UserProfile;
use App\Mail\FirstOrderMail;
use App\Models\InvoiceOrder;
use Illuminate\Http\Request;
use App\Mail\CancelOrderMail;
use App\Mail\CompleteOrderMail;
use LaravelDaily\Invoices\Invoice;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\OrderResource;
use LaravelDaily\Invoices\Classes\Party;
use App\Http\Resources\UserProfileResource;
use LaravelDaily\Invoices\Classes\InvoiceItem;


class OrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum');
        //$this->middleware('can:create')->only('getUser');

    }

    public function index()
    {
        $orders = Order::with(['user', 'user.user_profile', 'invoice'])->sort()->filter()->status()->history()->getOrPaginate();

        return OrderResource::collection($orders);
    }

    public function store(Request $request)
    {

        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'user_profile_id' => 'required|integer|exists:user_profiles,id',
            'products' => 'required',
            'total' => 'required',
         ]);


        $order = Order::create([
            'user_id' => $request->user_id,
            'user_profile_id' => $request->user_profile_id,
            'products' => $request->products,
            'subTotal' => $request->subTotal,
            'total' => $request->total,
            'coupon' => $request->coupon,
            'order_date' => Carbon::now()->format('d-m-Y' . ' ' . 'H:i'),
            'paid' => Order::PENDIENTE,
            'status' => Order::RECIBIDO,
            'shipping' => $request->shipping,
            'shipping_method' => $request->shipping_method,
            'invoice_paper' => $request->invoice_paper,
            'note' => $request->note,
        ]);

        $couponFirst = 'ORDERFIRST';

        if(strpos($request->coupon, $couponFirst) !== false){
            $coupon = Cupon::where('code', $request->coupon)->first();
            $coupon->delete();
        }


        return response()->json([
            'message' => 'Pedido creado correctamente',
            'order' => $order,
        ]);
    }

    public function show(Order $order)
    {
        return OrderResource::make($order);
    }

    public function getUser()
    {
        //$user = auth('api')->user();
        
        $userProfile = UserProfile::all();
        $userProfile->load('user');


        return UserProfileResource::collection($userProfile);
    }

    public function status(Order $order, Request $request)
    {
        $order->status = $request->status;
        $order->save();

        $user = User::where('id', $order->user_id)->first();
        $user_profile = UserProfile::where('id', $order->user_profile_id)->first();

        $products = json_decode($order->products);

        if($request->status == 3){
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
            ];
             
            Mail::to($user->email)->send(new SendOrderMail($mailData));
        }
        
        if($request->status == 4){
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
            ];
             
            Mail::to($user->email)->send(new CompleteOrderMail($mailData));
        }

        if($request->status == 5){
            $mailData = [
                'title' => 'Confirmación de pedido',
                'body' => 'Gracias por tu pedido. Te adjuntamos la factura de tu pedido.',

            ];
             
            Mail::to($user->email)->send(new CancelOrderMail($mailData));
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function paid($token_id)
    {

        $order = Order::where('token_id', $token_id)->first();

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
            'name' => 'Trivicare Natural Cosmetics',
            'custom_fields' => [
                'name' => 'Cristina Triviño Cortés',
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
            $items[] = (new InvoiceItem())->title($item->name)->pricePerUnit($item->price_base)->quantity($item->cartQuantity)->discountByPercent($item->discount)->taxByPercent(21);
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
        $last = substr($invt->last()->invoice_number, 0, -6);
        $headerInv = '#TNC' . $year;

        
        if($invt->count() == 0 || $last != $headerInv){
            $invoice_number = '#TNC'. $year . '/' . str_pad(1, 5, '0', STR_PAD_LEFT);
        }else {
            $last_invoice = $invt->last();
            $invoice_number = str_replace('#TNC' . $year . '/', '', $last_invoice->invoice_number);
            $invoice_number += 1;
            $invoice_number = '#TNC'. $year . '/' . str_pad($invoice_number, 5, '0', STR_PAD_LEFT);
        }

        $invoice = Invoice::make('receipt')
            //->series('#TNC'. strval($year)) 
            //->sequence(number_format(substr($invoice_number, -5)))
            ->serialNumberFormat($invoice_number)
            ->status(__('invoices::invoice.paid'))
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
            'invoice_number' => $invoice_number,
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
            
           
        return response()->json([
            'success' => true,
            'message' => 'Pedido realizado correctamente'
        ]);
    }

    public function paidPaypal($token_id, Request $request)
    {
        $order = Order::where('id', $request->order_id)->first();
        $order->token_id = $token_id;
        $order->status = 1;
        $order->paid = 2;
        $order->save();
        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Token guardado correctamente'
        ]);
    }


}
