<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Create a new post.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->email_verified_at) {
            return response()->json([
                'message' => 'Only verified users can create posts'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:50',
            'content' => 'nullable|string|max:2000',
            'status' => 'nullable|in:draft,published,trash',
            'allow_comments' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->status === 'published' && empty($request->title)) {
            return response()->json([
                'message' => 'Title is required for published posts'
            ], 422);
        }

        $post = new Post();
        $post->user_id = $user->id;
        $post->title = $request->title;
        $post->content = $request->content;
        $post->status = $request->status ?? 'draft';
        $post->allow_comments = $request->has('allow_comments') ? $request->allow_comments : true;
        $post->save();

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post
        ], 201);
    }
}
