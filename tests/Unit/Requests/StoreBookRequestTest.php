<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreBookRequest;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreBookRequestTest extends TestCase
{
    use RefreshDatabase;

    private function validate(array $data)
    {
        $request = new StoreBookRequest;

        return Validator::make($data, $request->rules(), [], $request->messages());
    }

    public function test_必須項目が不足している場合はエラーになる()
    {
        $validator = $this->validate([]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->messages());
        $this->assertArrayHasKey('author', $validator->errors()->messages());
        // $this->assertArrayHasKey('isbn', $validator->errors()->messages()); // 応用要件で nullable
        // $this->assertArrayHasKey('published_date', $validator->errors()->messages()); // 応用要件で nullable
        $this->assertArrayHasKey('genres', $validator->errors()->messages());
    }

    public function test_isb_nが13桁でない場合はエラー()
    {
        $validator = $this->validate([
            'isbn' => '123',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('isbn', $validator->errors()->messages());
    }

    public function test_image_urlが不正な_ur_lならエラー()
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

    public function test_genresの要素が存在しない_i_dならエラー()
    {
        $validator = $this->validate([
            'genres' => [999],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('genres.0', $validator->errors()->messages());
    }

    public function test_正しいデータならバリデーション成功()
    {
        $genre1 = Genre::factory()->create(['name' => 'ジャンルA']);
        $genre2 = Genre::factory()->create(['name' => 'ジャンルB']);

        $validator = $this->validate([
            'title' => 'タイトル',
            'author' => '著者',
            'isbn' => '1234567890123',
            'published_date' => '2024-01-01',
            'genres' => [$genre1->id, $genre2->id],
        ]);

        $this->assertFalse($validator->fails());
    }
}
