<?php

namespace Tests\Feature\Books;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_認証ユーザーは書籍登録画面を表示できる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('books.create'));

        $response->assertStatus(200)
            ->assertSeeText('書籍登録');
    }

    public function test_未認証ユーザーはログインへリダイレクトされる()
    {
        $response = $this->get(route('books.create'));

        $response->assertRedirect(route('login'));
    }
}
