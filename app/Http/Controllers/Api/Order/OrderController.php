<?php

namespace App\Http\Controllers\Api\Order;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function orderItems($id)
    {
        $order = Order::findOrFail($id);
        
        $obj = json_decode($order->product_collection);

//        dd($obj);
        
        foreach ($obj as $product) {
            return $product;
        }

        dd($product);

        // $orderUser = $order->user;

        // $user = User::findOrFail($orderUser->id);

        // return response()->json([
        //     'order' => $order,
        //     'products' => $product,
        //     'user' => $user,
        // ]);
    }
}
