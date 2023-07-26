<?php

namespace App\Http\Controllers\Api\Order;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Cupon;
use App\Models\Guest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Reserve;
use App\Mail\OrderModMail;
use App\Mail\SendOrderMail;
use App\Models\PickupPoint;
use App\Models\UserProfile;
use App\Models\InvoiceOrder;
use Illuminate\Http\Request;
use App\Mail\CancelOrderMail;
use App\Models\Notifications;
use App\Mail\CompleteOrderMail;
use LaravelDaily\Invoices\Invoice;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Storage;
use LaravelDaily\Invoices\Classes\Party;
use App\Http\Resources\UserProfileResource;
use LaravelDaily\Invoices\Classes\InvoiceItem;

class OrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin')->except('store', 'verifyEmail');
    }

    public function index()
    {
        $orders = Order::with(['user', 'user_profile', 'guest', 'invoice'])->sort()->filter()->status()->history()->orderBy('created_at', 'desc')->getOrPaginate();

        return OrderResource::collection($orders);
    }

    public function store(Request $request)
    {

        $request->validate([
            'products' => 'required',
            'total' => 'required',
        ]);

        $pickupPoint = '';
        
        if($request->pickup_point != ''){
            // Tranformar el la respuesta en numero
            $resP = intval($request->pickup_point);
            //si exiten puntos de recogida
            $pickupPoint = PickupPoint::where('id', $resP)->first();
        }

        $order = Order::create([
            'guest_id' => $request->guest_id,
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
            'token_reserve' => $request->token_reserve,
            'payment_method' => $request->payment_method,
        ]);

        if($pickupPoint != ''){
            $order->pickup_point = $pickupPoint->name;
            $order->save();
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

        $userProfile = UserProfile::all();
        $userProfile->load('user');


        return UserProfileResource::collection($userProfile);
    }

    public function status(Order $order, Request $request)
    {
        $order->status = $request->status;
        $order->save();

        if($request->track){
            $order->track = $request->track;
            $order->save();
        }

        
        $products = json_decode($order->products);
        
        $invoice_number = InvoiceOrder::where('order_id', $order->id)->first();
        
        $urlTrack = 'https://www.ordertracker.com/es/track/' . $request->track;
        

        if($order->guest_id == null){
            $user = User::where('id', $order->user_id)->first();
            $user_profile = UserProfile::where('id', $order->user_profile_id)->first();
        }else{
            $user = Guest::where('id', $order->guest_id)->first();
            $user_profile = Guest::where('id', $order->guest_id)->first();
        }

        if($request->status == 3){
            $mailData = [
                'title' => 'Confirmación de pedido',
                'body' => 'Gracias por tu pedido. Te adjuntamos la factura de tu pedido.',
                'date' => $order->order_date,
                'order' => $invoice_number->invoice_number,
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
                'track' => $request->track,
                'urlTrack' => $urlTrack,
                'shippingMethod' => $order->shipping_method,
            ];
             
            Mail::to($user->email)->send(new SendOrderMail($mailData));

            $notification = Notifications::create([
                'user_id' => $order->user_id,
                'type' => 'send',
                'title' => 'Pedido enviado',
                'message' => 'Tu pedido ha sido enviado. Puedes ver el estado de tu pedido en la sección Mis Pedidos.',
                'read' => 0,
            ]);

        }
        
        if($request->status == 4){
            $mailData = [
                'title' => 'Confirmación de pedido',
                'body' => 'Gracias por tu pedido. Te adjuntamos la factura de tu pedido.',
                'date' => $order->order_date,
                'order' => $invoice_number->invoice_number,
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

            $notification = Notifications::create([
                'user_id' => $order->user_id,
                'type' => 'complete',
                'title' => 'Pedido Entregado',
                'message' => 'Tu pedido ha sido entregado, esperamos que lo disfrutes.',
                'read' => 0,
            ]);

            $notification_review = Notifications::create([
                'user_id' => $order->user_id,
                'type' => 'review',
                'title' => '¿Qué te ha parecido tu pedido?',
                'message' => 'Queremos saber tu opinión sobre los productos. Pulsa en el botón para valorarlos.',
                'url' => '/review?token=' . $order->token_id,
                'read' => 0,
            ]);
        }

        if($request->status == 5){
            foreach($products as $item){
                $product = Product::where('id', $item->id)->first();
                $product->stock = $product->stock + $item->cartQuantity;
                $product->save();
            }
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

        if($order->token_reserve != null){
            $reserve = Reserve::where('token_reserve', $order->token_reserve)->first();
            $reserve->delete();
        }

        $order->status = 1;
        $order->paid = 'PAGADO';
        $order->save();
           
        return response()->json([
            'success' => true,
            'message' => 'Pedido realizado correctamente',
            'token' => $token_id , strlen($token_id),
        ]);
    }

    public function paidPaypal($token_id, Request $request)
    {
        $order = Order::where('id', $request->order_id)->first();
        $order->token_id = $request->token_id;
        $order->status = 1;
        $order->paid = 'PROCESANDO';
        $order->save();
        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Token guardado correctamente'
        ]);
    }

    public function orderToken($token_id)
    {
        $order = Order::where('token_id', $token_id)->first();
        return response()->json([
            'data' => $order,
        ]);
    }


    public function verifyEmail(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if($user){
            return response()->json([
                'success' => true,
                'message' => 'El correo electrónico ya esta registrado. Usa el botón de "Ya tengo cuenta" para acceder a tu cuenta.',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'El correo electrónico no esta registrado. Usa el botón de "Crear cuenta" para crear una nueva cuenta.',
            ]);
        }
    }

    public function updatePaid(Order $order, Request $request)
    {
        $order->paid = $request->paid;
        $order->save();
        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }


    public function modificationOrder(Request $request)
    {
        // Recuperamos el pedido
        $order = Order::where('id', $request->id)->first();

        // Recuperamos los productos del pedido
        $products = json_decode($order->products);

        // iteramos los productos
        foreach($products as $item){
            // Recuperamos el producto
            $product = Product::where('id', $item->id)->first();
            // Devolvemos el stock
            $product->stock = $product->stock + $item->cartQuantity;
            $product->save();
        }

        // decodificar los productos del request
        $newProducts = json_decode($request->products);
        // iteramos los productos del request
        foreach($newProducts as $item){
            // Recuperamos el producto
            $product = Product::where('id', $item->id)->first();
            // Actualizamos el stock
            $product->stock = $product->stock - $item->cartQuantity;
            $product->save();
        }

        // Actualizamos el pedido
        $order->products = $newProducts;
        $order->shipping = $request->shipping;
        $order->total = $request->total;
        $order->save();


        // Generamos la nueva factura si el pedido ya esta pagado
        if($order->paid == 'PAGADO'){
            // comprobamos si el usuario es invitado o no
            if($order->guest_id == null){
                $user = User::where('id', $order->user_id)->first();
                $user_profile = UserProfile::where('id', $order->user_profile_id)->first();
            }else{
                $user = Guest::where('id', $order->guest_id)->first();
                $user_profile = Guest::where('id', $order->guest_id)->first();
            }

            // rellenamos los datos de la factura
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

            // Iteramos los productos del pedido y los añadimos a la factura
            foreach($products as $item) {
                $items[] = (new InvoiceItem())->title($item->name)->pricePerUnit($item->price_base)->quantity($item->cartQuantity)->discountByPercent($item->discount->discount)->taxByPercent(21);
            }

            // Recuperamos el cupón       
            if($order->coupon == null) {
                $discnt = null; 
            } else {
                $coupon = Cupon::where('code', $order->coupon)->first();
                $discnt = $coupon->discount;
            }

            // Recuperamos el número de factura
            $invoice_number = InvoiceOrder::where('order_id', $order->id)->first();

            $dateInv= date('dmY');
    
            $invoice = Invoice::make('receipt')
                //->series('#TNC'. strval($year)) 
                //->sequence(number_format(substr($invoice_number, -5)))
                ->serialNumberFormat($invoice_number->invoice_number)
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
                ->filename($dateInv . '_' . substr($invoice_number->invoice_number, 7, 5))
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
         
            // Actualizamos la factura
            $invoice_number->filename = $filename;
            $invoice_number->url = $link;
            $invoice_number->save();

    
            //send email
    
            $mailData = [
                'title' => 'Confirmación de pedido',
                'body' => 'Gracias por tu pedido. Te adjuntamos la factura de tu pedido.',
                'date' => $order->order_date,
                'order' => $invoice_number->invoice_number,
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
            
            Mail::to($user->email)->send(new OrderModMail($mailData));

            $order->email_sent = true;
            $order->save();

            // Buscamos la factura anterior en el storage
            $oldInvoice = InvoiceOrder::where('order_id', $order->id)->first();
            // Eliminamos la factura anterior
            Storage::delete('public/' . $oldInvoice->filename);
        }




        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }
}
