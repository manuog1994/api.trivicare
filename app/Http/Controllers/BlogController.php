<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use App\Http\Resources\BlogResource;

class BlogController extends Controller
{
    public function index()
    {
        return BlogResource::collection(Blog::all());
    }

    public function show(Blog $blog)
    {
        return new BlogResource($blog);
    }

    public function store(Request $request)
    {
        $blog = Blog::create($request->all());

        return new BlogResource($blog);
    }

    public function update(Request $request, Blog $blog)
    {
        $blog->update($request->all());

        return new BlogResource($blog);
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();

        return response()->json();
    }
}
