<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateBookRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_isbnのuniqueが自身を除外して動作する()
    {
        $genre = Genre::factory()->create();
        $book = Book::factory()->create(['isbn' => '1234567890123']);

        $request = new UpdateBookRequest;

        // ルートパラメータに $book を返すダミー resolver
        $request->setRouteResolver(function () use ($book) {
            return new class($book)
            {
                public function __construct(private $book) {}

                public function parameter($key)
                {
                    return $this->book;
                }
            };
        });

        $data = [
            'title' => 'test title',
            'author' => 'test author',
            'isbn' => '1234567890123', // ← 自身と同じ ISBN
            'published_date' => '2024-01-01',
            'description' => 'test',
            'image_url' => 'https://example.com',
            'genres' => [$genre->id],
        ];

        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages(),
            [] // 属性名置き換え（今回は空でOK）
        );

        $this->assertFalse($validator->fails());
    }
}
