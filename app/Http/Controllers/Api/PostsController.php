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
        $posts = $this->post
                ->when($this->currentUser->hasRole('regular-user'), function ($query) {
                    $query->where('user_id', $this->currentUser->id);
                })->orderBy('id', 'DESC')->paginate(10);

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
            $request['user_id'] = $request->user_id ?? $this->currentUser->id;
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
        $post = $this->post
                ->with('author:id,name,email')
                ->when($this->currentUser->hasRole('regular-user'), function ($query) {
                    $query->where('user_id', $this->currentUser->id);
                })->where('slug', $slug)->first();

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
        $post = $this->post
                ->when($this->currentUser->hasRole('regular-user'), function ($query) {
                    $query->where('user_id', $this->currentUser->id);
                })->find($post);

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
        $post = $this->post
                ->when($this->currentUser->hasRole('regular-user'), function ($query) {
                    $query->where('user_id', $this->currentUser->id);
                })->find($id);

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
