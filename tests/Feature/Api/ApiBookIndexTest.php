<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiBookIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_書籍一覧_ap_iが_jso_nを返し検索とページネーションが機能する()
    {
        Book::factory()->create(['title' => 'Laravel入門']);
        Book::factory()->create(['title' => 'PHPの教科書']);
        Book::factory()->create(['title' => 'JavaScriptガイド']);

        $response = $this->getJson('/api/v1/books?keyword=PHP&page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ])
            ->assertJsonFragment([
                'title' => 'PHPの教科書',
            ]);

        $this->assertFalse(
            collect($response->json('data'))->contains(fn ($b) => $b['title'] === 'Laravel入門')
        );
    }

    public function test_書籍一覧_ap_i_バリデーションエラーで422が返る()
    {
        $response = $this->getJson('/api/v1/books?page=abc');

        $response->assertStatus(422);
    }
}
