<?php

namespace App\Http\Controllers\Api\VisitConter;

use App\Http\Controllers\Controller;
use App\Models\VisitCounter;
use Illuminate\Http\Request;

class VisitController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin')->except('store');
    }

    public function index()
    {
        return response()->json(VisitCounter::all());
    }

    public function store(Request $request)
    {
        $visit = VisitCounter::create($request->all());

        return response()->json($visit, 201);
    }
}
