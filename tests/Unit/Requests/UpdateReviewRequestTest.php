<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdateReviewRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateReviewRequestTest extends TestCase
{
    use RefreshDatabase;

    private function validate(array $data)
    {
        $request = new UpdateReviewRequest();
        return Validator::make($data, $request->rules(), [], $request->attributes());
    }

    public function test_ratingが1から5以外はエラー()
    {
        $validator = $this->validate([
            'rating' => 0,
            'comment' => 'test',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('rating', $validator->errors()->messages());
    }

    public function test_commentが必須である()
    {
        $validator = $this->validate([
            'rating' => 3,
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('comment', $validator->errors()->messages());
    }

    public function test_commentが2000文字以内である()
    {
        $validator = $this->validate([
            'rating' => 3,
            'comment' => str_repeat('a', 2001),
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('comment', $validator->errors()->messages());
    }

    public function test_正常データは通過する()
    {
        $validator = $this->validate([
            'rating' => 5,
            'comment' => '最高でした',
        ]);

        $this->assertFalse($validator->fails());
    }
}
