<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Http\Controllers\Controller;

class PostsController extends Controller
{
    /**
     * Fetch all the posts.
     *
     * @return \App\Http\Resources\PostResource
     */
    public function index()
    {
        if (auth()->user()->getRoleNames()->contains('regular-user')) {
            $posts = auth()->user()->posts()->orderBy('id', 'DESC')->paginate(10);
        } else {
            $posts = Post::orderBy('id', 'DESC')->paginate(10);
        }

        return response()->json(['posts' => $posts]);
    }

    /**
     * Store the new post details.
     *
     * @param  \App\Http\Requests\PostRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PostRequest $request)
    {
        $request['uuid'] = Str::uuid();
        $roleNames = auth()->user()->getRoleNames();
        if ($roleNames->contains('admin-user') || $roleNames->contains('manager-user')) {
            $request['user_id'] = $request->user_id;
        } else if ($roleNames->contains('regular-user')) {
            $request['user_id'] = auth()->id();
        }
        $post = Post::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => "Post created successfully.",
            'post_details' => $post,
        ], 201);
    }

    /**
     * Display the details of the given post slug.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        if (auth()->user()->getRoleNames()->contains('regular-user')) {
            $post = auth()->user()->posts()->with('author:id,name,email')->where('slug', $slug)->first();
        } else {
            $post = Post::with('author:id,name,email')->where('slug', $slug)->first();
        }

        if (! $post) {
            return response()->json([
                'status' => 'not_found',
                'message' => "Post with the {$slug} not found.",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Post found.',
            'post_details' => $post,
        ], 200);
    }

    /**
     * Update the details of the given post id.
     *
     * @param  integer  $id
     * @param  \App\Http\Requests\PostRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($post, PostRequest $request)
    {
        if (auth()->user()->getRoleNames()->contains('regular-user')) {
            $post = auth()->user()->posts()->find($post);
        } else {
            $post = Post::find($post);
        }

        if (! $post) {
            return response()->json([
                'status' => 'not_found',
                'message' => "Post with the id #{$id} not found.",
            ], 404);
        }

        $post->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => "Post updated successfully.",
            'post_details' => $post->fresh(),
        ], 201);
    }

    /**
     * Delete the post of the given post id.
     *
     * @param  integer  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        if (auth()->user()->getRoleNames()->contains('regular-user')) {
            $post = auth()->user()->posts()->find($id);
        } else {
            $post = Post::find($id);
        }

        if (! $post) {
            return response()->json([
                'status' => 'not_found',
                'message' => "Post with the id #{$id} not found.",
            ], 404);
        }

        $post->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Post deleted successfully.",
        ], 201);
    }
}
