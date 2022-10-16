<?php

namespace App\Http\Controllers\Api\Tag;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TagResource;
use App\Http\Controllers\Controller;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();

        return TagResource::collection($tags);
    }

    public function show($id)
    {
        $tagId = DB::table('product_tag')->where('product_id', $id)->get();

        $tags = Tag::findOrFail($tagId->pluck('tag_id'));

        return TagResource::collection($tags);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'tag' => 'required|string',
            'slug' => 'required|string',
            'color' => 'nullable|string',
        ]);
        
        $tag = Tag::create($request->all());
        
        return TagResource::make($tag);
    }

    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();
        
        return response()->json(null, 204);
    }
}
