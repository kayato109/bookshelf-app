<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreBookRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreBookRequestTest extends TestCase
{
    use RefreshDatabase;

    private function validate(array $data)
    {
        $request = new StoreBookRequest();
        return Validator::make($data, $request->rules(), [], $request->attributes());
    }

    public function test_必須項目が不足している場合はエラーになる()
    {
        $validator = $this->validate([]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->messages());
        $this->assertArrayHasKey('author', $validator->errors()->messages());
        $this->assertArrayHasKey('isbn', $validator->errors()->messages());
        $this->assertArrayHasKey('published_date', $validator->errors()->messages());
    }

    public function test_ISBNが13桁でない場合はエラー()
    {
        $validator = $this->validate([
            'isbn' => '123',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('isbn', $validator->errors()->messages());
    }

    public function test_image_urlが不正なURLならエラー()
    {
        $validator = $this->validate([
            'image_url' => 'invalid-url',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('image_url', $validator->errors()->messages());
    }

    public function test_genresが配列でない場合はエラー()
    {
        $validator = $this->validate([
            'genres' => 'not-array',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('genres', $validator->errors()->messages());
    }
}
