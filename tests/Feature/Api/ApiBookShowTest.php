<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiBookShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_書籍詳細_apiがjsonを返す()
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $book->id,
                'title' => $book->title,
            ]);
    }

    public function test_書籍詳細_api_存在しない_idで404が返る()
    {
        $response = $this->getJson('/api/v1/books/999999');

        $response->assertStatus(404)
            ->assertExactJson([
                'error' => '書籍が見つかりませんでした。',
            ]);
    }
}
