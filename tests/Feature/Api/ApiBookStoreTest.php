<?php

namespace Tests\Feature\Api;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiBookStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_書籍登録_ap_iでレコードが作成され201が返る()
    {
        $genre1 = Genre::factory()->create(['name' => 'ジャンルA']);
        $genre2 = Genre::factory()->create(['name' => 'ジャンルB']);

        $payload = [
            'user_id' => User::factory()->create()->id,
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

        $this->assertDatabaseHas('books', [
            'title' => '新しい本',
            'isbn' => '9781234567890',
        ]);
    }

    public function test_書籍登録_ap_i_バリデーションエラーで422が返る()
    {
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
