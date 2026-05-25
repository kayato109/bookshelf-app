<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateBookRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_ISBNのuniqueが自身を除外して動作する()
    {
        $genre = Genre::factory()->create();
        $book = Book::factory()->create(['isbn' => '1234567890123']);

        $request = new UpdateBookRequest();

        $request->setRouteResolver(function () use ($book) {
            return new class ($book) {
                public function __construct(private $book)
                {}
                public function parameter($key)
                {
                    return $this->book; }
            };
        });

        $data = [
            'title' => 'test title',
            'author' => 'test author',
            'isbn' => '1234567890123',
            'published_date' => '2024-01-01',
            'description' => 'test',
            'image_url' => 'https://example.com',
            'genres' => [$genre->id],
        ];

        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertFalse($validator->fails());
    }
}
