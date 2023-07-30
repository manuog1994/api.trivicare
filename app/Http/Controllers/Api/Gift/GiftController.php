<?php

namespace App\Http\Controllers\Api\Gift;

use App\Models\Gift;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GiftResource;

class GiftController extends Controller
{
    public function index()
    {
        $gifts = Gift::all();
        return GiftResource::collection($gifts);
    }

    public function show(Gift $gift)
    {
        return new GiftResource($gift);
    }

    public function store(Request $request)
    {
        $gift = Gift::create($request->all());

        //si se ha subido una imagen
        if ($request->hasFile('image')) {
            $img = $request->file('image');
            $file_name = $img->getClientOriginalName();
            $img->move(storage_path('app/public/gifts'), $file_name);

            $gift->image_path = "storage/gifts/" . $file_name;
            $gift->save();

        }

        return new GiftResource($gift);
    }

    public function destroy(Gift $gift)
    {
        //borrar imagen
        if ($gift->image_path) {
            //eliminar toda la ruta menos el nombre del archivo
            $image_path = str_replace('storage/app/public/gifts', '', $gift->image_path);
            unlink(storage_path('app/public/gifts' . $image_path));
        }

        $gift->delete();
        return response()->json(null, 204);
    }
}
