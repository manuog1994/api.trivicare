<?php

namespace App\Http\Controllers\Api\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with(['category', 'reviews', 'tags'])->tags()->filter()->sort()->getOrPaginate();
        
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
            'discount' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'size' => 'nullable|numeric',
            'dimensions' => 'nullable|string',
            'rating' => 'nullable|numeric',
            'total_reviews' => 'nullable|numeric',
        ]);
        
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'specifications' => $request->specifications,
            'price' => $request->price,
            'stock' => $request->stock,
            'barcode' => $request->barcode,
            'category_id' => $request->category_id,
            'slug' => $request->slug,
            'sold' => $request->sold,
            'review' => $request->review,
            'discount' => $request->discount,
            'weight' => $request->weight,
            'size' => $request->size,
            'dimensions' => $request->dimensions,
            'rating' => $request->rating,
            'total_reviews' => $request->total_reviews,
            'price_discount' => $request->price - ($request->price * $request->discount / 100),
        ]);
        
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
            'discount' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'size' => 'nullable|numeric',
            'dimensions' => 'nullable|string',
            'rating' => 'nullable|numeric',
            'total_reviews' => 'nullable|numeric',
         ]);
        
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'specifications' => $request->specifications,
            'price' => $request->price,
            'stock' => $request->stock,
            'barcode' => $request->barcode,
            'category_id' => $request->category_id,
            'slug' => $request->slug,
            'sold' => $request->sold,
            'review' => $request->review,
            'discount' => $request->discount,
            'weight' => $request->weight,
            'size' => $request->size,
            'dimensions' => $request->dimensions,
            'rating' => $request->rating,
            'total_reviews' => $request->total_reviews,
            'price_discount' => $request->price - ($request->price * $request->discount / 100),
        ]);
        
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
