<?php

namespace Tests\Feature\Books;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BookIsbnApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_isbn検索_正常系()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response([
                'items' => [
                    [
                        'volumeInfo' => [
                            'title' => 'Mock Book',
                            'authors' => ['Mock Author'],
                            'publishedDate' => '2020-01-01',
                            'description' => 'Mock description',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->get('/books/isbn/9784000000000');

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Mock Book',
                'author' => 'Mock Author',
                'published_date' => '2020-01-01',
                'description' => 'Mock description',
            ]);
    }

    public function test_isbn検索_13桁以外は422()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/books/isbn/12345');

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'ISBN を13桁で入力してください',
            ]);
    }

    public function test_isbn検索_書籍なしは404()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response([
                'items' => [],
            ], 200),
        ]);

        $response = $this->get('/books/isbn/9784000000000');

        $response->assertStatus(404)
            ->assertJson([
                'error' => '書籍情報が見つかりませんでした',
            ]);
    }

    public function test_isbn検索_ap_i障害は503()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response(null, 500),
        ]);

        $response = $this->get('/books/isbn/9784000000000');

        $response->assertStatus(503)
            ->assertJson([
                'error' => '外部APIエラーが発生しました',
            ]);
    }

    public function test_isbn検索_タイムアウトは504()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => function () {
                throw new ConnectionException('timeout');
            },
        ]);

        $response = $this->get('/books/isbn/9784000000000');

        $response->assertStatus(504)
            ->assertJson([
                'error' => '外部サービスに接続できませんでした',
            ]);
    }
}
