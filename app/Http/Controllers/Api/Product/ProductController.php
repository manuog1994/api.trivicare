<?php

namespace App\Http\Controllers\Api\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('index', 'show');
        $this->middleware(['auth:api', 'scopes:create', 'can:create'])->only('store');
        $this->middleware(['auth:api', 'scopes:update', 'can:update'])->only('update');
        $this->middleware(['auth:api', 'scopes:delete', 'can:delete'])->only('delete');            
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::included()
                        ->filter()
                        ->sort()
                        ->getOrPaginate();
        
        return ProductResource::collection($products);
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
            'name' => 'required|string',
            'description' => 'required|string',
            'specifications' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'barcode' => 'nullable|numeric',
            'category_id' => 'required|numeric|exists:categories,id',
            'slug' => 'required|string',
            'sold' => 'nullable|numeric',
            'review' => 'nullable|string|exists:reviews,id',
            'offer' => 'nullable|numeric',
        ]);
        
        $product = Product::create($request->all());
        
        return ProductResource::make($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ProductResource::make(Product::find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'specifications' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'barcode' => 'nullable|numeric',
            'category_id' => 'required|numeric|exists:categories,id',
            'slug' => 'required|string',
            'sold' => 'nullable|numeric',
            'review' => 'nullable|string|exists:reviews,id',
            'offer' => 'nullable|numeric',
        ]);
        
        $product->update($request->all());
        
        return ProductResource::make($product);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();
        
        return response()->json(null, 204);
    }
}
