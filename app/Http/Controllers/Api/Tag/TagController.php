<?php

namespace App\Http\Controllers\Api\Tag;

use App\Models\Tag;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TagResource;
use App\Http\Controllers\Controller;

class TagController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('index', 'show');
        $this->middleware('can:create')->only('store');
        $this->middleware('can:delete')->only('destroy', 'delete');
    }

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

    public function delete(Product $product, Tag $tag)
    {
        DB::table('product_tag')->where('product_id', $product->id)->where('tag_id', $tag->id)->delete();
        
        return response()->json([
            'message' => 'Tag deleted successfully'
        ], 200);
    }
}
