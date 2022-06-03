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
     * Post instance holder.
     *
     * @var \App\Models\Post
     */
    private $post;

    /**
     * Authenticated User instance holder.
     *
     * @var \App\Models\User
     */
    public $currentUser;

    /**
     * Instantiate the post model for further operations.
     *
     * @param \App\Models\Post  $post
     */
    public function __construct(Post $post)
    {
        $this->post = $post;

        $this->middleware(function ($request, $next) {
            $this->currentUser = auth()->user();
            return $next($request);
        });
    }

    /**
     * Fetch all the posts.
     *
     * @return \App\Http\Resources\PostResource
     */
    public function index()
    {
        if ($this->currentUser->hasRole('regular-user')) {
            $posts = $this->post->where('user_id', auth()->id())->orderBy('id', 'DESC')->paginate(10);
        } else {
            $posts = $this->post->orderBy('id', 'DESC')->paginate(10);
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
        if ($this->currentUser->hasRole('admin-user') || $this->currentUser->hasRole('manager-user')) {
            $request['user_id'] = $request->user_id;
        } else if ($this->currentUser->hasRole('regular-user')) {
            $request['user_id'] = $this->currentUser->id;
        }

        $post = $this->post->create($request->all());

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
        if ($this->currentUser->hasRole('regular-user')) {
            $post = $this->post->with('author:id,name,email')->where('user_id', auth()->id())->where('slug', $slug)->first();
        } else {
            $post = $this->post->with('author:id,name,email')->where('slug', $slug)->first();
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
        if ($this->currentUser->hasRole('regular-user')) {
            $post = $this->post->where('user_id', auth()->id())->find($post);
        } else {
            $post = $this->post->find($post);
        }

        if (! $post) {
            return response()->json([
                'status' => 'not_found',
                'message' => "Post not found.",
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
        if ($this->currentUser->hasRole('regular-user')) {
            $post = $this->post->where('user_id', auth()->id())->find($id);
        } else {
            $post = $this->post->find($id);
        }

        if (! $post) {
            return response()->json([
                'status' => 'not_found',
                'message' => "Post not found.",
            ], 404);
        }

        $post->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Post deleted successfully.",
        ], 201);
    }
}
