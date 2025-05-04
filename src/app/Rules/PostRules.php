<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PostRules
{
    /**
     * Get validation rules for creating a post.
     *
     * @return array
     */
    public static function createRules()
    {
        return [
            'title' => ['nullable', 'string', 'max:50', new PostTitleRule],
            'content' => ['nullable', 'string', 'max:2000'],
            'status' => ['nullable', Rule::in(['draft', 'published', 'trash'])],
            'allow_comments' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Validate post data.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validate(array $data)
    {
        return Validator::make($data, self::createRules());
    }

    /**
     * Check if user is authorized to create posts.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public static function authorizeCreate(User $user)
    {
        return $user->email_verified_at !== null;
    }
}
