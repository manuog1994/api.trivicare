<?php

namespace App\Http\Controllers\Api\State;

use App\Models\State;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except('index');
    }

    public function index()
    {
        $states = State::with('pickupPoints')->get();

        return response()->json([
            'states' => $states,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'city' => 'required',
            'state' => 'required',
        ]);

        $state = State::create($request->all());

        return response()->json([
            'message' => 'State created successfully',
            'state' => $state,
        ]);
    }
}
