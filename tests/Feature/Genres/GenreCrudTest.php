<?php

namespace Tests\Feature\Genres;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreCrudTest extends TestCase
{
    use RefreshDatabase;

    /* ---------------------------------------------------------
        ジャンル一覧（GET /genres）
    --------------------------------------------------------- */
    public function test_認証ユーザーはジャンル一覧を表示できる()
    {
        $user = User::factory()->create();
        Genre::factory()->create(['name' => '宇宙']);

        $response = $this->actingAs($user)->get(route('genres.index'));

        $response->assertStatus(200)
            ->assertSeeText('宇宙');
    }

    public function test_未認証ユーザーはジャンル一覧にアクセスできずログインへリダイレクト()
    {
        $response = $this->get(route('genres.index'));

        $response->assertRedirect(route('login'));
    }

    /* ---------------------------------------------------------
        ジャンル登録（POST /genres）
    --------------------------------------------------------- */
    public function test_認証ユーザーはジャンルを登録できる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('genres.store'), [
            'name' => 'ミステリー',
        ]);

        $response->assertRedirect(route('genres.index'));

        $this->assertDatabaseHas('genres', [
            'name' => 'ミステリー',
        ]);
    }

    public function test_未認証ユーザーはジャンル登録できずログインへリダイレクト()
    {
        $response = $this->post(route('genres.store'), []);

        $response->assertRedirect(route('login'));
    }

    /* ---------------------------------------------------------
        ジャンル詳細（GET /genres/{genre}）
    --------------------------------------------------------- */
    public function test_認証ユーザーはジャンル詳細を表示できる()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create(['name' => '宇宙']);

        $response = $this->actingAs($user)->get(route('genres.show', $genre));

        $response->assertStatus(200)
            ->assertSeeText('宇宙');
    }

    public function test_未認証ユーザーはジャンル詳細にアクセスできずログインへリダイレクト()
    {
        $genre = Genre::factory()->create();

        $response = $this->get(route('genres.show', $genre));

        $response->assertRedirect(route('login'));
    }

    /* ---------------------------------------------------------
        ジャンル更新（PUT /genres/{genre}）
    --------------------------------------------------------- */
    public function test_認証ユーザーはジャンルを更新できる()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create(['name' => '旧ジャンル']);

        $response = $this->actingAs($user)->put(route('genres.update', $genre), [
            'name' => '新ジャンル',
        ]);

        $response->assertRedirect(route('genres.index'));

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => '新ジャンル',
        ]);
    }

    public function test_未認証ユーザーはジャンル更新できずログインへリダイレクト()
    {
        $genre = Genre::factory()->create();

        $response = $this->put(route('genres.update', $genre), [
            'name' => '不正更新',
        ]);

        $response->assertRedirect(route('login'));
    }

    /* ---------------------------------------------------------
        ジャンル削除（DELETE /genres/{genre}）
    --------------------------------------------------------- */
    public function test_認証ユーザーはジャンルを削除できる()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)->delete(route('genres.destroy', $genre));

        $response->assertRedirect(route('genres.index'));

        $this->assertDatabaseMissing('genres', [
            'id' => $genre->id,
        ]);
    }

    public function test_書籍に紐づくジャンルは削除できずエラーメッセージが表示される()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();

        $book->genres()->sync([$genre->id]);

        $response = $this->actingAs($user)->delete(route('genres.destroy', $genre));

        $response->assertRedirect(route('genres.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
        ]);
    }

    public function test_未認証ユーザーはジャンル削除できずログインへリダイレクト()
    {
        $genre = Genre::factory()->create();

        $response = $this->delete(route('genres.destroy', $genre));

        $response->assertRedirect(route('login'));
    }
}
