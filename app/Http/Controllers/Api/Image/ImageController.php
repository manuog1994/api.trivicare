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

                    //if not exit file /image/270x360 create
                    if(!file_exists(storage_path('app/public/images/270x360'))){
                        mkdir(storage_path('app/public/images/270x360'));
                    }

                    //resize image of min
                    $img = ImageManagerStatic::make($utf8ImagePath)->resize(270, 360);
                    $img->save(storage_path('app/public/images/270x360').'/'.$file_name_out_ext.'.'.$ext);


                    //if not exit file /image/600x800 create
                    if(!file_exists(storage_path('app/public/images/600x800'))){
                        mkdir(storage_path('app/public/images/600x800'));
                    }

                    //resize image of mid
                    $imgMid = ImageManagerStatic::make($utf8ImagePath)->resize(600, 800);
                    $imgMid->save(storage_path('app/public/images/600x800').'/'.$file_name_out_ext.'.'.$ext);

                    //if not exit file /image/450x600 create
                    if(!file_exists(storage_path('app/public/images/450x600'))){
                        mkdir(storage_path('app/public/images/450x600'));
                    }

                    //cut image of max
                    $imgMax = ImageManagerStatic::make($utf8ImagePath)->fit(450, 600);
                    $imgMax->save(storage_path('app/public/images/450x600').'/'.$file_name_out_ext.'.'.$ext);
                    
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
        $imagePathMin = storage_path('app/public/images/270x360').'/'.$image->name.'.'.$image->ext;
        $imagePathMax = storage_path('app/public/images/600x800').'/'.$image->name.'.'.$image->ext;

        if(file_exists($imagePath)){
            unlink($imagePath);
        }

        if(file_exists($imagePathMin)){
            unlink($imagePathMin);
        }

        if(file_exists($imagePathMax)){
            unlink($imagePathMax);
        }

        $image->delete();
        return response()->json([
            'message' => 'Image deleted successfully'
        ], 200);
    }


}
