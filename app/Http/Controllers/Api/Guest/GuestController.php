<?php

namespace App\Http\Controllers\Api\Guest;

use App\Models\Guest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GuestController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'lastname' => 'required',
            'address' => 'required',
            'optional_address' => 'nullable',
            'city' => 'required',
            'state' => 'required',
            'zipcode' => 'required',
            'country' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'dni' => 'required',

        ]);

        $guest = Guest::create($request->all());

        return response()->json([
            'message' => 'Guest created successfully',
            'data' => $guest
        ], 201);
    }

    public function show($id)
    {
        $this->middleware('auth:sanctum');
        
        $guest = Guest::findOrFail($id);

        return response()->json([
            'message' => 'Guest found successfully',
            'data' => $guest
        ], 200);
    }

    public function destroy($id)
    {
        $guest = Guest::findOrFail($id);
        $guest->name = 'Guest deleted';
        $guest->lastname = 'Guest deleted';
        $guest->address = 'Guest deleted';
        $guest->optional_address = 'Guest deleted';
        $guest->city = 'Guest deleted';
        $guest->state = 'Guest deleted';
        $guest->zipcode = '00000';
        $guest->country = 'Guest deleted';
        $guest->email = 'guest' . $guest->id . '@deleted.com';
        $guest->phone = '000000000';
        $guest->dni = '00000000A';
        $guest->save();


        return response()->json([
            'message' => 'Guest deleted successfully',
            'data' => $guest
        ], 200);
    }
}
