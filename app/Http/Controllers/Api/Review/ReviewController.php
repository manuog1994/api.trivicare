<?php

namespace App\Http\Controllers\Api\Review;

use App\Models\Review;
use App\Events\MyEvent;
use App\Models\Product;
use App\Models\EventNot;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //valoraciones con relación a productos
        $reviews = Review::with('product')->get()->sortByDesc('created_at');

        return response()->json([
            'status' => 'success',
            'reviews' => ReviewResource::collection($reviews),
        ], 200);
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
            'user_id' => 'required',
            'product_id' => 'required',
            'message' => 'required',
            'rating' => 'required',
            'user_profile_id' => 'required',
        ]);
    
        if($request->user_name && $request->user_lastname) {
            $review = Review::create([
                'user_id' => $request->user_id,
                'user_profile_id' => $request->user_profile_id,
                'product_id' => $request->product_id,
                'message' => $request->message,
                'rating' => $request->rating,
                'user_name' => $request->user_name,
                'user_lastname' => $request->user_lastname,
            ]);
        } else {
            $user_profile = UserProfile::where('id', $request->user_profile_id)->first();
            
            $review = Review::create([
                'user_id' => $request->user_id,
                'user_profile_id' => $request->user_profile_id,
                'product_id' => $request->product_id,
                'message' => $request->message,
                'rating' => $request->rating,
                'user_name' => $user_profile->name,
                'user_lastname' => $user_profile->lastname,
            ]);
        }
        
        $event = EventNot::create([
            'title' => 'Se ha creado una nueva valoración',
            'description' => 'Se ha creado una nueva valoración para el producto ' . $review->product->name
        ]);
        
        event(new MyEvent($event));

        $product = Product::findOrFail($request->product_id);

        $totalRating = 0;

        foreach($product->reviews as $review) {
            $totalRating += $review->rating; 
        }

        $totalRating = $totalRating / $product->reviews->count();

        $product->rating = $totalRating;
        $product->total_reviews = $product->reviews->count();
        $product->save();

        return ReviewResource::make($review);     
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $reviews = Review::where('product_id', $id)->get();

        return ReviewResource::collection($reviews);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Review $review)
    {   
        //obtenemos el producto
        $product = Product::findOrFail($review->product_id);

        //eliminamos la valoración
        $review->delete();

        //inicializamos la variable
        $totalRating = 0;

        //recorremos las valoraciones del producto
        foreach($product->reviews as $item) {
            if(!$item){
                $totalRating = 0;
            } else {
                $totalRating += $item->rating; 
            }
        }

        //calculamos la media
        if($product->reviews->count() == 0) {
            $totalRating = 0;
        } else {
            $totalRating = $totalRating / $product->reviews->count();
        }

        //actualizamos el producto
        $product->rating = $totalRating;
        $product->total_reviews = $product->reviews->count();
        $product->save();

        //devolvemos la respuesta
        return response()->json([
            'status' => 'success',
            'message' => 'Review deleted successfully',
        ], 200);
    }
}
