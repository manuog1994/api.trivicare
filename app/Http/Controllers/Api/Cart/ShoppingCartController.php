<?php

namespace App\Http\Controllers\Api\Cart;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Gloudemans\Shoppingcart\Facades\Cart;

class ShoppingCartController extends Controller
{

    public function __construct()
    {
        $this->middleware('sessions');
        $this->middleware('auth:api')->only('cartDetails');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $carts = Cart::instance('shopping')->content();

        return $carts;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'qty' => 'required|integer',
        ]);

        $product = Product::findOrFail($request->id);

        $stock = $product->stock - $request->qty;

        $product->update(['stock' => $stock]);

        $cart = Cart::instance('shopping')->add([
            'id' => $product->id, 
            'name' => $product->name, 
            'qty' => $request->qty, 
            'price' => $product->price, 
            'weight' => $product->weight, 
            ]);
        

        return $cart;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cart = Cart::get($id);

        return $cart;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'qty' => 'required|integer',
        ]);

        
        $cart = Cart::instance('shopping')->get($id);

        $product = Product::findOrFail($cart->id);

        $stock = $product->stock + ($cart->qty - $request->qty);
        $product->update(['stock' => $stock]);


        $cart = Cart::instance('shopping')->update($id, $request->qty);

        return $cart;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cart = Cart::instance('shopping')->get($id);

        $product = Product::findOrFail($cart->id);

        $stock = $product->stock + $cart->qty;
        $product->update(['stock' => $stock]);

        $cart = Cart::instance('shopping')->remove($id);

        return response()->json([
            'message' => 'Item removed from cart'
        ]);
    }
    
    public function destroyAll()
    {   $cart = Cart::instance('shopping')->content();

        foreach ($cart as $item) {
            $product = Product::findOrFail($item->id);

            $stock = $product->stock + $item->qty;
            $product->update(['stock' => $stock]);
        }

        $cart = Cart::instance('shopping')->destroy();

        $user = auth('api')->user();

        if($user){
            DB::table('shoppingcart')->where('identifier', $user->email)->delete();
            
        }
        return response()->json([
            'message' => 'Cart has been deleted'
        ]);

    }

    public function cartDetails()
    {
        $user = auth('api')->user();

        $cart = Cart::instance('shopping')->content();

        $arr = array_combine($cart->pluck('id')->toArray(), $cart->pluck('qty')->toArray());

        $products = json_encode($arr);

        $total = Cart::instance('shopping')->total();

        $count = Cart::instance('shopping')->count();

        $order = Order::create([
            'user_id' => $user->id,
            'product_collection' => $products,
            'total' => $total,
            'count' => $count,
            'order_date' => now(),
        ]);

        //$cart = Cart::instance('shopping')->destroy();

        $user = auth('api')->user();

        // if($user){
        //     DB::table('shoppingcart')->where('identifier', $user->email)->delete();
            
        // }

        return response()->json([
            'message' => 'Order has been placed',
            'order' => $order
        ]);
    }

}
