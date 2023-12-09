<?php

namespace App\Http\Controllers\Api\Reserve;

use App\Models\Product;
use App\Models\Reserve;
use App\Models\EventNot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReserveController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required',
            'token_reserve' => 'required'
        ]);

        $reserve = Reserve::create([
            'products' => $request->products,
            'token_reserve' => $request->token_reserve
        ]);

        $json = json_decode($reserve->products);

        foreach ($json as $item) {
            if(!$item->presale){
                $product = Product::where('id', $item->id)->first();
                $product->stock = $product->stock - $item->cartQuantity;
                $product->save();
            }
        }

        $event = EventNot::create([
            'title' => 'Reserva creada',
            'description' => 'Reserva creada desde la web',
        ]);

        event(new \App\Events\MyEvent($event));
        
        return response()->json($reserve, 201);
    }

}
