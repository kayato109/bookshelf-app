<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use App\Models\Genre;

class ApiBookUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_書籍更新APIで更新され200が返る()
    {
        $book = Book::factory()->create();

        $genre1 = Genre::factory()->create();
        $genre2 = Genre::factory()->create();

        $payload = [
            'user_id' => User::factory()->create()->id,
            'title' => '更新後のタイトル',
            'author' => '更新後の著者',
            'isbn' => '9781234567891',
            'published_date' => '2024-01-01',
            'description' => '更新後の説明',
            'image_url' => 'https://example.com/image.jpg',
            'genres' => [$genre1->id, $genre2->id],
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => '更新後のタイトル',
            ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => '更新後のタイトル',
        ]);
    }

    public function test_書籍更新API_バリデーションエラーで422が返る()
    {
        $book = Book::factory()->create();

        $payload = [
            'title' => '', // 必須エラー
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $payload);

        $response->assertStatus(422);
    }
}
