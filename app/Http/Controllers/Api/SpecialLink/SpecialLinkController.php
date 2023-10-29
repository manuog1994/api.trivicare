<?php

namespace App\Http\Controllers\Api\SpecialLink;

use App\Models\SpecialLink;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SpecialLinkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except(['show']);
    }

    public function index()
    {
        return response()->json([
            'message' => 'SpecialLinkController@index'
        ]);
    }

    public function store(Request $request)
    {
        $special_link = \App\Models\SpecialLink::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'product_id' => $request->product_id,
            'param' => 'exclusive=',
            'discount' => $request->discount,
            'max_uses' => $request->max_uses,
            'is_active' => $request->is_active,
        ]);
        return response()->json([
            'data' => $special_link,
            'message' => 'SpecialLink created successfully',
        ]);
    }

    public function show($uuid)
    {
        $special_link = SpecialLink::where('id', $uuid)->firstOrFail();

        if ($special_link->max_uses > 0 && $special_link->is_active == true) {
            return response()->json($special_link, 200);
        }
    }

    public function update(Request $request, $uuid)
    {
        return response()->json([
            'message' => 'SpecialLinkController@update'
        ]);
    }

    public function destroy($uuid)
    {
        return response()->json([
            'message' => 'SpecialLinkController@destroy'
        ]);
    }
}
