<?php

namespace Tests\Feature\Api;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiBookStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_未認証ユーザーは書籍登録できず401が返る()
    {
        $response = $this->postJson('/api/v1/books', []);

        $response->assertStatus(401);
    }

    public function test_書籍登録_apiでレコードが作成され201が返る()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $genre1 = Genre::factory()->create(['name' => 'ジャンルA']);
        $genre2 = Genre::factory()->create(['name' => 'ジャンルB']);

        $payload = [
            // user_id は送らない（API 側で上書きされるため）
            'title' => '新しい本',
            'author' => '新しい著者',
            'isbn' => '9781234567890',
            'published_date' => '2024-01-01',
            'description' => '説明文',
            'image_url' => 'https://example.com/image.jpg',
            'genres' => [$genre1->id, $genre2->id],
        ];

        $response = $this->postJson('/api/v1/books', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', '新しい本');

        // DB に保存されていること
        $this->assertDatabaseHas('books', [
            'title' => '新しい本',
            'isbn' => '9781234567890',
            'user_id' => $user->id, // 認証ユーザーが owner
        ]);

        // 中間テーブルも確認
        $bookId = $response->json('data.id');

        $this->assertDatabaseHas('book_genre', [
            'book_id' => $bookId,
            'genre_id' => $genre1->id,
        ]);

        $this->assertDatabaseHas('book_genre', [
            'book_id' => $bookId,
            'genre_id' => $genre2->id,
        ]);
    }

    public function test_書籍登録_api_バリデーションエラーで422が返る()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'title' => '',
        ];

        $response = $this->postJson('/api/v1/books', $payload);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['title'],
            ]);
    }
}
