<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreReviewRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreReviewRequestTest extends TestCase
{
    use RefreshDatabase;

    private function validate(array $data)
    {
        $request = new StoreReviewRequest;

        return Validator::make(
            $data,
            $request->rules(),
            $request->messages(),
            [] // 属性名置き換え（今回は空でOK）
        );
    }

    public function test_ratingが1から5以外ならエラーになる()
    {
        $validator = $this->validate([
            'rating' => 6,
            'comment' => 'test',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('rating', $validator->errors()->messages());
    }

    public function test_ratingが整数でない場合はエラーになる()
    {
        $validator = $this->validate([
            'rating' => 'abc',
            'comment' => 'test',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('rating', $validator->errors()->messages());
    }

    public function test_commentが必須であること()
    {
        $validator = $this->validate([
            'rating' => 5,
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('comment', $validator->errors()->messages());
    }

    public function test_commentが2000文字以内でない場合はエラーになる()
    {
        $validator = $this->validate([
            'rating' => 5,
            'comment' => str_repeat('a', 2001),
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('comment', $validator->errors()->messages());
    }

    public function test_正常データならバリデーション成功する()
    {
        $validator = $this->validate([
            'rating' => 4,
            'comment' => '良かったです',
        ]);

        $this->assertFalse($validator->fails());
    }
}
