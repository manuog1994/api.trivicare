<?php

namespace App\Http\Controllers\Api\Variation;

use App\Models\Product;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\VariationResource;

class VariationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except('index', 'show');

    }

    public function index()
    {
        //valoraciones con relación a productos
        $variations = Variation::with('product', 'image')->get();

        return response()->json(VariationResource::collection($variations));
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
            'product_id' => 'required',
            'model' => 'nullable',
            'color' => 'nullable',
            'size' => 'nullable',
        ]);

        $variation = Variation::create($request->all());

        return VariationResource::make($variation);     
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $variations = Variation::where('id', $id)->with('image')->get();

        return VariationResource::collection($variations);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Variation $variation)
    {   
        //eliminamos la valoración
        $variation->delete();


        //devolvemos la respuesta
        return response()->json([
            'status' => 'success',
            'message' => 'Variation deleted successfully',
        ], 200);
    }
}
