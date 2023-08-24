<?php

namespace App\Http\Controllers;

use App\Models\ImagesPost;
use Illuminate\Http\Request;
use App\Http\Resources\ImagesPostResource;

class ImagesPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ImagesPostResource::collection(ImagesPost::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //almacenar imagenes en el servidor

        $file = $request->file('file');
        $name = $file->getClientOriginalName();
        $file->move(storage_path('app/public/images/posts'), $name);
        $path = "storage/images/posts/" . $name;
        ImagesPost::create([
            'name' => $name,
            'url' => $path,
        ]);

        return response()->json('Imagenes guardadas con exito');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ImagesPost  $imagesPost
     * @return \Illuminate\Http\Response
     */
    public function show(ImagesPost $imagesPost)
    {
        return new ImagesPostResource($imagesPost);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ImagesPost  $imagesPost
     * @return \Illuminate\Http\Response
     */
    public function edit(ImagesPost $imagesPost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ImagesPost  $imagesPost
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ImagesPost $imagesPost)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ImagesPost  $imagesPost
     * @return \Illuminate\Http\Response
     */
    public function destroy(ImagesPost $imagesPost)
    {
        //
    }
}
