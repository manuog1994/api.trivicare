<?php

namespace App\Http\Controllers\Api\Order;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Cupon;
use App\Models\Order;
use App\Mail\SendOrderMail;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Mail\CancelOrderMail;
use App\Mail\CompleteOrderMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserProfileResource;
//use Haruncpi\LaravelIdGenerator\IdGenerator;

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
            'token_id' => 'required',
        ]);

        //$id = IdGenerator::generate(['table' => 'orders', 'length' => 10, 'prefix' =>'F']);

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
            'token_id' => $request->token_id,
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

}
