<?php

namespace App\Http\Controllers\Api\Search;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('can:edit')->only('index');
    }

    public function index(Request $request)
    {
        // Crear un busqueda de pedidos, usuarios y perfiles de usuario
        $orders = Order::with(['user', 'user_profile', 'guest', 'invoice'])
            ->where('order_date', 'LIKE', '%' . $request->search . '%')
            ->orWhereHas('user_profile', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('lastname', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('dni', 'LIKE', '%' . $request->search . '%');
            })
            ->orWhereHas('guest', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('lastname', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('dni', 'LIKE', '%' . $request->search . '%');
            })
            ->orderBy('order_date', 'DESC')
            ->get();

            // Cual seria la url para hacer la busqueda?
            // http://localhost:8000/api/search?search=pedro
        return response()->json($orders);
    }
}
