<?php

namespace App\Http\Controllers\Api\Image;

use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic;



class ImageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except('index', 'show');

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
                    $file_name = $value->getClientOriginalName();
                    $file_name_out_ext = pathinfo($file_name, PATHINFO_FILENAME);
                    $ext = $value->getClientOriginalExtension();
                    $value->move(storage_path('app/public/images'), $file_name);
                    Image::create([
                        'name' => $file_name_out_ext,
                        'ext' => $ext,
                        'path' => "storage/images/",
                        'product_id' => $request->product_id,
                    ]);

                    //find image
                    $imagePath = storage_path('app/public/images').'/'.$file_name;
                    $utf8ImagePath = mb_convert_encoding($imagePath, 'UTF-8', 'auto');

                    //resize image of min
                    $img = ImageManagerStatic::make($utf8ImagePath)->resize(280, 280);
                    $img->save(storage_path('app/public/images/280x280').'/'.$file_name_out_ext.'.'.$ext);

                    //resize image of mid
                    $imgMid = ImageManagerStatic::make($utf8ImagePath)->resize(800, 800);
                    $imgMid->save(storage_path('app/public/images/800x800').'/'.$file_name_out_ext.'.'.$ext);
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
        $imagePath = storage_path('app/public/images').'/'.$image->name.'.'.$image->ext;
        $imagePath280 = storage_path('app/public/images/280x280').'/'.$image->name.'.'.$image->ext;
        $imagePath800 = storage_path('app/public/images/800x800').'/'.$image->name.'.'.$image->ext;

        if(file_exists($imagePath)){
            unlink($imagePath);
        }

        if(file_exists($imagePath280)){
            unlink($imagePath280);
        }

        if(file_exists($imagePath800)){
            unlink($imagePath800);
        }

        $image->delete();
        return response()->json([
            'message' => 'Image deleted successfully'
        ], 200);
    }


}
