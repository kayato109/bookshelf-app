<?php

namespace Tests\Feature\Books;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookSearchFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_keyword検索でtitle_authorの部分一致のみ表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Book::factory()->create(['title' => 'Laravel入門', 'author' => '山田太郎']);
        Book::factory()->create(['title' => 'PHPの教科書', 'author' => '佐藤花子']);
        Book::factory()->create(['title' => 'JavaScript完全ガイド', 'author' => 'Laravel太郎']);

        $response = $this->get('/books?keyword=Laravel');

        $response->assertStatus(200);
        $response->assertSee('Laravel入門');          // title 部分一致
        $response->assertSee('JavaScript完全ガイド'); // author 部分一致
        $response->assertDontSee('PHPの教科書');
    }

    public function test_genreフィルタで該当ジャンルのみ表示される_存在しない_i_dは無視される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $genreA = Genre::factory()->create();
        $genreB = Genre::factory()->create();

        $bookA = Book::factory()->create();
        $bookA->genres()->attach($genreA);

        $bookB = Book::factory()->create();
        $bookB->genres()->attach($genreB);

        // genre=A → A のみ
        $response = $this->get("/books?genre={$genreA->id}");
        $response->assertStatus(200);
        $response->assertSee($bookA->title);
        $response->assertDontSee($bookB->title);

        // genre=999999 → 存在しない → 全件表示
        $response = $this->get('/books?genre=999999');
        $response->assertStatus(200);
        $response->assertSee($bookA->title);
        $response->assertSee($bookB->title);
    }

    public function test_keywordとgenre併用で_an_d条件になる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $genre = Genre::factory()->create();

        $match = Book::factory()->create(['title' => 'Laravel入門']);
        $match->genres()->attach($genre);

        $noGenre = Book::factory()->create(['title' => 'Laravel実践']);
        $noGenre->genres()->attach(Genre::factory()->create());

        $noKeyword = Book::factory()->create(['title' => 'PHP入門']);
        $noKeyword->genres()->attach($genre);

        $response = $this->get("/books?keyword=Laravel&genre={$genre->id}");

        $response->assertStatus(200);
        $response->assertSee($match->title);
        $response->assertDontSee($noGenre->title);
        $response->assertDontSee($noKeyword->title);
    }

    public function test_検索条件がページネーションで保持される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Book::factory()->count(20)->create(['title' => 'Laravel本']);

        $response = $this->get('/books?keyword=Laravel&genre=1&sort=title&page=2');

        $response->assertStatus(200);

        // ページネーションリンクに条件が保持されていること
        $response->assertSee('keyword=Laravel');
        $response->assertSee('genre=1');
        $response->assertSee('sort=title');
    }
}
