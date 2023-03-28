<?php

namespace App\Http\Controllers\Api\Order;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Guest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Reserve;
use App\Mail\SendOrderMail;
use App\Models\UserProfile;
use App\Models\InvoiceOrder;
use Illuminate\Http\Request;
use App\Mail\CancelOrderMail;
use App\Models\Notifications;
use App\Mail\CompleteOrderMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserProfileResource;


class OrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin')->except('store', 'verifyEmail');
    }

    public function index()
    {
        $orders = Order::with(['user', 'user_profile', 'guest', 'invoice'])->sort()->filter()->status()->history()->getOrPaginate();

        return OrderResource::collection($orders);
    }

    public function store(Request $request)
    {

        $request->validate([
            'products' => 'required',
            'total' => 'required',
        ]);
        

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
            'pickup_point' => $request->pickup_point,
            'payment_method' => $request->payment_method,
        ]);


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
}
