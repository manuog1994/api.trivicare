<?php

namespace App\Http\Controllers\Api\Image;

use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('index', 'show');
        $this->middleware('can:create')->only('store');
        $this->middleware('can:delete')->only('destroy');
    }

    public function index()
    {
        $images = Image::all();
        return response()->json($images);
    }

    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required',
            'product_id' => 'required|exists:products,id'
        ]);

        try{
            if($request->has('images')){
                $files = $request->images;
                foreach ($files as $key => $value) {
                    $file_name = time().$key. '-' . $value->getClientOriginalName();
                    $value->move(storage_path('app/public/images'), $file_name);
                    Image::create([
                        'name' => $file_name,
                        'path' => "storage/images/$file_name",
                        'product_id' => $request->product_id,
                    ]);
                }
            }
            return response()->json([
                'message' => 'Images uploaded successfully'
            ]);
        }catch(\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function show(Image $image)
    {
        return response()->json($image);
    }

    public function destroy(Image $image)
    {
        $image->delete();
        return response()->json([
            'message' => 'Image deleted successfully'
        ], 200);
    }


}
