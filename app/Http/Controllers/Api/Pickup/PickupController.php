<?php

namespace App\Http\Controllers\Api\Pickup;

use App\Models\PickupPoint;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PickupController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create', ['only' => ['store']]);
    }

    public function index()
    {
        $pickupPoints = PickupPoint::with('state')->filter()
                                                    ->get();

        return response()->json([
            'pickupPoints' => $pickupPoints,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'state_id' => 'required',
            'phone' => 'required',
            'hours' => 'required',
        ]);

        $pickupPoint = PickupPoint::create($request->all());

        return response()->json([
            'message' => 'Pickup point created successfully',
            'pickupPoint' => $pickupPoint,
        ]);
    }
}
