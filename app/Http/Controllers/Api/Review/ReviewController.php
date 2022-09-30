<?php

namespace App\Http\Controllers\Api\Review;

use App\Models\User;
use App\Models\Review;
use App\Models\Product;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
            'message' => 'required|string',
            'rating' => 'required|integer',
            'user_name' => 'nullable|string',
            'user_lastname' => 'nullable|string',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 400);
        };

        $user = User::findOrFail($request->user_id);

        if($user->user_profile->count() <= 0 && $request->user_name == '' && $request->user_lastname == '') {
            $review = Review::create([
                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
                'message' => $request->message,
                'rating' => $request->rating,
                'user_name' => 'Anónim@',
            ]);

        }else if ($user->user_profile->count() <= 0) {
            $review = Review::create([
                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
                'message' => $request->message,
                'rating' => $request->rating,
                'user_name' => $request->user_name,
                'user_lastname' => $request->user_lastname,
            ]);

        } else {
            $user_profile = UserProfile::where('user_id', $user->id)->first();
            
            $review = Review::create([
                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
                'message' => $request->message,
                'rating' => $request->rating,
                'user_name' => $user_profile->name,
                'user_lastname' => $user_profile->lastname,
            ]);

        }


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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        
        $product = Product::where('id', $review->product_id)->get();

        $review->delete();

        $totalRating = 0;

        foreach($product->reviews as $review) {
            if(!$review){
                $totalRating = 0;
            } else {
                $totalRating += $review->rating; 
            }
        }

        if($product->reviews->count() == 0) {
            $totalRating = 0;
        } else {
            $totalRating = $totalRating / $product->reviews->count();
        }

        $product->rating = $totalRating;
        $product->total_reviews = $product->reviews->count();
        $product->save();


        return response()->json([
            'status' => 'success',
            'message' => 'Review deleted successfully',
        ], 200);
    }
}
