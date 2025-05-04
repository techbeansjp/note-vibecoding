<?php

namespace App\Http\Controllers;

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
        $result = $this->postService->createPost($request->user(), $request->all());

        return response()->json(
            isset($result['errors']) 
                ? ['errors' => $result['errors']] 
                : ['message' => $result['message'], 'post' => $result['post'] ?? null],
            $result['status']
        );
    }
}
