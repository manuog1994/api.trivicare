<?php

namespace App\Http\Controllers\Api\Category;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
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
        $categories = Category::included()
                        ->filter()
                        ->sort()
                        ->getOrPaginate(); // Score creado para que el cliente decida si quiere paginar o no
        //retornamos toda la coleccion de categorias a traves de la clase CategoryResource
        return CategoryResource::collection($categories);
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
            'slug' => 'required|string',
        ]);
        
        $category = Category::create($request->all());
        
        return CategoryResource::make($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return CategoryResource::make(Category::find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Category $category)
    {
        $category->update(request()->all());
        return CategoryResource::make($category);
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Category::destroy($id);
        return response(null, 204);
    }
}
