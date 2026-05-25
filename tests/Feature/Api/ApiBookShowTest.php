<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Book;

class ApiBookShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_書籍詳細APIがJSONを返す()
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $book->id,
                'title' => $book->title,
            ]);
    }

    public function test_書籍詳細API_存在しないIDで404が返る()
    {
        $response = $this->getJson('/api/v1/books/999999');

        $response->assertStatus(404)
            ->assertJson([
                'error' => '書籍が見つかりませんでした。',
            ]);
    }
}
