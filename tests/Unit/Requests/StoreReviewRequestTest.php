<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreReviewRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreReviewRequestTest extends TestCase
{
    use RefreshDatabase;

    private function validate(array $data)
    {
        $request = new StoreReviewRequest();
        return Validator::make($data, $request->rules(), [], $request->attributes());
    }

    public function test_ratingが1から5以外はエラー()
    {
        $validator = $this->validate([
            'rating' => 6,
            'comment' => 'test',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('rating', $validator->errors()->messages());
    }

    public function test_ratingが整数でない場合はエラー()
    {
        $validator = $this->validate([
            'rating' => 'abc',
            'comment' => 'test',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('rating', $validator->errors()->messages());
    }

    public function test_commentが必須である()
    {
        $validator = $this->validate([
            'rating' => 5,
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('comment', $validator->errors()->messages());
    }

    public function test_commentが2000文字以内である()
    {
        $validator = $this->validate([
            'rating' => 5,
            'comment' => str_repeat('a', 2001),
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('comment', $validator->errors()->messages());
    }

    public function test_正常データは通過する()
    {
        $validator = $this->validate([
            'rating' => 4,
            'comment' => '良かったです',
        ]);

        $this->assertFalse($validator->fails());
    }
}
