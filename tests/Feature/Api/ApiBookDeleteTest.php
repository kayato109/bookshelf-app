<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Book;

class ApiBookDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_書籍削除APIで削除され204が返る()
    {
        $book = Book::factory()->create();

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }

    public function test_書籍削除API_存在しないIDで404が返る()
    {
        $response = $this->deleteJson('/api/v1/books/999999');

        $response->assertStatus(404);
    }
}
