<?php

namespace App\Http\Controllers\Api\Order;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\UserProfile;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('can:create')->only('index', 'getUser');

    }

    public function index()
    {
        $orders = Order::with(['user', 'user.user_profile'])->get();

        return OrderResource::collection($orders);
    }

    public function store(Request $request)
    {
        $this->middleware('auth:sanctum');

        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'user_profile_id' => 'required|integer|exists:user_profiles,id',
            'products' => 'required',
            'total' => 'required'
        ]);

        $order = Order::create([
            'user_id' => $request->user_id,
            'user_profile_id' => $request->user_profile_id,
            'products' => $request->products,
            'total' => $request->total,
            'coupon' => $request->coupon,
            'order_date' => Carbon::now()->format('d-m-Y' . ' ' . 'H:i'),
            'paid' => Order::PENDIENTE,
            'status' => Order::RECIBIDO,
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

        return response()->json([
            'data' => $userProfile,
        ]);
    }

    public function status(Order $order, Request $request)
    {
        $order->status = $request->status;
        $order->save();

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

}
