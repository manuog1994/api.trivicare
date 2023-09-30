<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use App\Http\Resources\BlogResource;

class BlogController extends Controller
{
/**
 * The index function returns a collection of Blog resources.
 * 
 * @return The index function is returning a collection of Blog resources.
 */
    public function index()
    {
        return BlogResource::collection(Blog::all());
    }

/**
 * The show function returns a new instance of the BlogResource class for a given Blog object.
 * 
 * @param Blog blog The "blog" parameter is an instance of the Blog model. It is passed to the "show"
 * method to retrieve the details of a specific blog post.
 * 
 * @return An instance of the BlogResource class is being returned.
 */
    public function show(Blog $blog)
    {
        return new BlogResource($blog);
    }

/**
 * The store function creates a new blog record using the data from the request and returns a resource
 * representation of the created blog.
 * 
 * @param Request request The  parameter is an instance of the Request class, which represents
 * the HTTP request made to the server. It contains information about the request, such as the request
 * method, headers, and any data sent in the request body. In this case, it is used to retrieve the
 * data sent in the
 * 
 * @return The code is returning a new instance of the BlogResource class, passing in the 
 * variable as a parameter.
 */
    public function store(Request $request)
    {
        $blog = Blog::create($request->all());

        return new BlogResource($blog);
    }

/**
 * The function updates a blog using the data from the request and returns the updated blog as a
 * resource.
 * 
 * @param Request request The  parameter is an instance of the Request class, which represents
 * an HTTP request made to the server. It contains information about the request, such as the request
 * method, headers, and any data sent with the request.
 * @param Blog blog The `` parameter is an instance of the `Blog` model. It represents the blog
 * post that needs to be updated.
 * 
 * @return The method is returning a new instance of the BlogResource class, passing in the 
 * object as a parameter.
 */
    public function update(Request $request, Blog $blog)
    {
        $blog->update($request->all());

        return new BlogResource($blog);
    }

/**
 * The destroy function deletes a blog and returns a JSON response.
 * 
 * @param Blog blog The "blog" parameter is an instance of the Blog model. It represents a specific
 * blog post that needs to be deleted.
 * 
 * @return A JSON response is being returned.
 */
    public function destroy(Blog $blog)
    {
        $blog->delete();

        return response()->json();
    }

/**
 * The status function updates the status of a blog post from "Publicado" to "Borrador" or vice versa.
 * 
 * @param Blog blog The parameter `` is an instance of the `Blog` model. It represents a blog post
 * and contains information about the post, such as its title, content, and status.
 * 
 * @return a JSON response with the message "Status actualizado con exito" (Status updated
 * successfully).
 */
    public function status(Blog $blog)
    {
        $blog->status = $blog->status == 'Publicado' ? 'Borrador' : 'Publicado';
        $blog->save();

        return response()->json('Status actualizado con exito');
    }
}
