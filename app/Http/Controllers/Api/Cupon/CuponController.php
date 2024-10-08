<?php

namespace App\Http\Controllers\Api\Cupon;

use App\Models\Cupon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CuponResource;

class CuponController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except('index');

    }
    
    public function index()
    {
        return CuponResource::collection(Cupon::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'discount' => 'required|integer',
            'validity' => 'required|date',
            'status' => 'required|integer',
        ]);

        $cupon = Cupon::create($request->all());

        return CuponResource::make($cupon);
    }

    public function destroy($id)
    {
        $cupon = Cupon::find($id);
        $cupon->delete();

        return response()->json([
            'message' => 'Cupon deleted successfully'
        ]);
    }

}
