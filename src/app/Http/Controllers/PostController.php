<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Rules\PostRules;
use App\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * The post service instance.
     *
     * @var \App\Services\PostService
     */
    protected $postService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\PostService  $postService
     * @return void
     */
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Create a new post.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!PostRules::authorizeCreate($user)) {
            return response()->json([
                'message' => 'Only verified users can create posts'
            ], 403);
        }

        $validator = PostRules::validate($request->all());

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $post = $this->postService->createPost($user, $request->all());

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post
        ], 201);
    }
}
