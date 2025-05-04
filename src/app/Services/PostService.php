<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;

class PostService
{
    /**
     * Create a new post.
     *
     * @param  \App\Models\User  $user
     * @param  array  $data
     * @return \App\Models\Post
     */
    public function createPost(User $user, array $data)
    {
        $post = new Post();
        $post->user_id = $user->id;
        $post->title = $data['title'] ?? null;
        $post->content = $data['content'] ?? null;
        $post->status = $data['status'] ?? 'draft';
        $post->allow_comments = isset($data['allow_comments']) ? $data['allow_comments'] : true;
        $post->save();

        return $post;
    }
}
