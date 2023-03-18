<?php

namespace App\Http\Controllers\Api\Product;

use App\Models\Tag;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('index', 'show');
        $this->middleware('can:create')->only('store');
        $this->middleware('can:edit')->only('update', 'status');
        $this->middleware('can:delete')->only('destroy');
    }

    public function index()
    {
        $products = Product::with(['category', 'reviews', 'images', 'tags'])->tags()->filter()->sort()->getOrPaginate();
        
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
            'discount' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'size' => 'nullable|numeric',
            'dimensions' => 'nullable|string',
            'status' => 'nullable|numeric',
            'new' => 'nullable|numeric',
            'tags' => 'nullable',
        ]);
        
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'specifications' => $request->specifications,
            'price' => $request->price,
            'price_base' => $request->price / 1.21,
            'stock' => $request->stock,
            'barcode' => $request->barcode,
            'category_id' => $request->category_id,
            'slug' => $request->slug,
            'discount' => $request->discount,
            'weight' => $request->weight,
            'size' => $request->size,
            'dimensions' => $request->dimensions,
            'price_discount' => $request->price - ($request->price * $request->discount / 100),
            'status' => Product::BORRADOR,
            'new' => Product::NUEVO,
        ]);

        if ($request->has('tags')) {
            $tags = json_decode($request->tags);
            foreach ($tags as $tag) {
                DB::table('product_tag')->insert([
                    'product_id' => $product->id,
                    'tag_id' => $tag,
                    'name' => Tag::where('id', $tag)->first()->name,
                    'slug' => Tag::where('id', $tag)->first()->slug,
                ]);
            }
        }

        if($request->has('images')){
            $files = $request->images;
            foreach ($files as $key => $value) {
                $file_name = time().$key. '-' . $value->getClientOriginalName();
                $value->move(public_path('images'), $file_name);
                Image::create([
                    'name' => $file_name,
                    'path' => "images/$file_name",
                    'product_id' => $product->id,
                ]);
            }
        }
        
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

    public function update(Product $product, Request $request)
    {

        
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'specifications' => $request->specifications,
            'price' => $request->price,
            'price_base' => $request->price / 1.21,
            'stock' => $request->stock,
            'barcode' => $request->barcode,
            'category_id' => $request->category_id,
            'slug' => $request->slug,
            'discount' => $request->discount,
            'weight' => $request->weight,
            'size' => $request->size,
            'dimensions' => $request->dimensions,
            'price_discount' => $request->price - ($request->price * $request->discount / 100),
        ]);

        if ($request->has('tags')) {
            $tags = json_decode($request->tags);
            foreach ($tags as $tag) {
                DB::table('product_tag')->insert([
                    'product_id' => $product->id,
                    'tag_id' => $tag,
                    'name' => Tag::where('id', $tag)->first()->name,
                    'slug' => Tag::where('id', $tag)->first()->slug,
                ]);
            }
        }
        
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
        
        return response()->json([
            'message' => 'Producto eliminado correctamente'
        ], 204);
    }

    public function status(Product $product)
    {
        if ($product->status == Product::BORRADOR) {
            $product->status = Product::PUBLICADO;
        } else {
            $product->status = Product::BORRADOR;
        }
        $product->save();

        return response()->json(['data' => $product]);
    }
}
