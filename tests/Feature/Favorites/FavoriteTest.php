<?php

namespace Tests\Feature\Favorites;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_認証ユーザーはお気に入り一覧を表示できる()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        Favorite::factory()->for($user)->for($book)->create();

        $response = $this->actingAs($user)->get(route('favorites.index'));

        $response->assertStatus(200)
            ->assertSeeText($book->title);
    }

    public function test_未認証ユーザーはお気に入り一覧にアクセスできずログインへリダイレクト()
    {
        $response = $this->get(route('favorites.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_認証ユーザーはお気に入りをトグルできる()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // 1回目 → 追加
        $response = $this
            ->actingAs($user)
            ->from(route('books.show', $book))
            ->post(route('favorites.toggle', $book));

        $response->assertRedirect(route('books.show', $book));

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        // 2回目 → 解除
        $response = $this
            ->actingAs($user)
            ->from(route('books.show', $book))
            ->post(route('favorites.toggle', $book));

        $response->assertRedirect(route('books.show', $book));

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_同じ本に複数回お気に入りできない()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        Favorite::factory()->for($user)->for($book)->create();

        // 2回目 → トグルなので解除される
        $this->actingAs($user)->post(route('favorites.toggle', $book));

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_未認証ユーザーはトグルできずログインへリダイレクト()
    {
        $book = Book::factory()->create();

        $response = $this->post(route('favorites.toggle', $book));

        $response->assertRedirect(route('login'));
    }
}
