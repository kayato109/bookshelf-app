<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateBookRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_ISBNのuniqueが自身を除外して動作する()
    {
        $genre = Genre::factory()->create();
        $book = Book::factory()->create(['isbn' => '1234567890123']);

        Route::put('/books/{book}', function () {})->name('books.update');

        $request = new UpdateBookRequest();

        $route = Route::getRoutes()->getByName('books.update');
        $route->bind($request);
        $route->setParameter('book', $book);

        $request->setRouteResolver(function () use ($route) {
            return $route;
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

        $validator = Validator::make($data, $request->rules(), [], $request->attributes());

        $this->assertFalse($validator->fails());
    }
}
