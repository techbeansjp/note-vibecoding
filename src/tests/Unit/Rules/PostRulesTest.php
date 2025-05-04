<?php

namespace Tests\Unit\Rules;

use App\Models\User;
use App\Rules\PostRules;
use App\Rules\PostTitleRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostRulesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test post validation rules.
     *
     * @return void
     */
    public function test_create_rules()
    {
        $rules = PostRules::createRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('title', $rules);
        $this->assertArrayHasKey('content', $rules);
        $this->assertArrayHasKey('status', $rules);
        $this->assertArrayHasKey('allow_comments', $rules);

        $this->assertContains('nullable', $rules['title']);
        $this->assertContains('string', $rules['title']);
        $this->assertContains('max:50', $rules['title']);
        
        $titleRuleHasPostTitleRule = false;
        foreach ($rules['title'] as $rule) {
            if ($rule instanceof PostTitleRule) {
                $titleRuleHasPostTitleRule = true;
                break;
            }
        }
        $this->assertTrue($titleRuleHasPostTitleRule, 'Title rules should include PostTitleRule');
    }

    /**
     * Test validate method.
     *
     * @return void
     */
    public function test_validate_method()
    {
        $data = [
            'title' => 'Test Post',
            'content' => 'This is a test post',
            'status' => 'draft',
            'allow_comments' => true,
        ];

        $validator = PostRules::validate($data);
        $this->assertFalse($validator->fails());

        $invalidData = [
            'title' => str_repeat('a', 51), // 51 characters
            'content' => str_repeat('a', 2001), // 2001 characters
            'status' => 'invalid-status',
            'allow_comments' => 'not-a-boolean',
        ];

        $validator = PostRules::validate($invalidData);
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('title'));
        $this->assertTrue($validator->errors()->has('content'));
        $this->assertTrue($validator->errors()->has('status'));
        $this->assertTrue($validator->errors()->has('allow_comments'));
    }

    /**
     * Test authorize create method.
     *
     * @return void
     */
    public function test_authorize_create()
    {
        $verifiedUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $this->assertTrue(PostRules::authorizeCreate($verifiedUser));

        $unverifiedUser = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $this->assertFalse(PostRules::authorizeCreate($unverifiedUser));
    }
}
