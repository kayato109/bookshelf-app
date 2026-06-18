<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiBookUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_未認証ユーザーは書籍更新できず401が返る()
    {
        $book = Book::factory()->create();

        $response = $this->putJson("/api/v1/books/{$book->id}", []);

        $response->assertStatus(401);
    }

    public function test_書籍更新_apiで更新され200が返る()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $book = Book::factory()->create(['user_id' => $user->id]);

        $genre1 = Genre::factory()->create();
        $genre2 = Genre::factory()->create();

        $payload = [
            'title' => '更新後のタイトル',
            'author' => '更新後の著者',
            'isbn' => '9781234567891',
            'published_date' => '2024-01-01',
            'description' => '更新後の説明',
            'image_url' => 'https://example.com/image.jpg',
            'genres' => [$genre1->id, $genre2->id],
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('data.title', '更新後のタイトル');

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => '更新後のタイトル',
        ]);

        // 中間テーブルも確認
        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre1->id,
        ]);
        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre2->id,
        ]);
    }

    public function test_認証済みでも所有者以外は書籍更新できず403が返る()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $book = Book::factory()->create(['user_id' => $owner->id]);

        Sanctum::actingAs($other);

        $genre = Genre::factory()->create();

        $payload = [
            'title' => '更新後のタイトル',
            'author' => '更新後の著者',
            'isbn' => '9781234567891',
            'published_date' => '2024-01-01',
            'description' => '説明',
            'image_url' => 'https://example.com/image.jpg',
            'genres' => [$genre->id],
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $payload);

        $response->assertStatus(403);
    }

    public function test_書籍更新_api_バリデーションエラーで422が返る()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $book = Book::factory()->create(['user_id' => $user->id]);

        $payload = [
            'title' => '', // 必須エラー
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }
}
