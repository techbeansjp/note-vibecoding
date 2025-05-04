<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use App\Rules\PostTitleRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PostService
{
    /**
     * Create a new post.
     *
     * @param  \App\Models\User  $user
     * @param  array  $data
     * @return array
     */
    public function createPost(User $user, array $data)
    {
        if (!$user->email_verified_at) {
            return [
                'success' => false,
                'message' => 'Only verified users can create posts',
                'status' => 403
            ];
        }

        $validator = Validator::make($data, [
            'title' => ['nullable', 'string', 'max:50', new PostTitleRule],
            'content' => ['nullable', 'string', 'max:2000'],
            'status' => ['nullable', Rule::in(['draft', 'published', 'trash'])],
            'allow_comments' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors(),
                'status' => 422
            ];
        }


        $post = new Post();
        $post->user_id = $user->id;
        $post->title = $data['title'] ?? null;
        $post->content = $data['content'] ?? null;
        $post->status = $data['status'] ?? 'draft';
        $post->allow_comments = isset($data['allow_comments']) ? $data['allow_comments'] : true;
        $post->save();

        return [
            'success' => true,
            'message' => 'Post created successfully',
            'post' => $post,
            'status' => 201
        ];
    }
}
