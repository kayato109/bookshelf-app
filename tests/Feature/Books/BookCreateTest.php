<?php

namespace Tests\Feature\Books;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class BookCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_認証ユーザーは書籍登録画面を表示できる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/books/create');

        $response->assertStatus(200);
        $response->assertSee('書籍登録');
    }

    public function test_未認証ユーザーはログインへリダイレクトされる()
    {
        $response = $this->get('/books/create');

        $response->assertRedirect('/login');
    }
}
