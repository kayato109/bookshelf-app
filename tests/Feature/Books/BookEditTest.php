<?php

namespace Tests\Feature\Books;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Book;
use App\Models\User;

class BookEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_作成者は編集画面を表示できる()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get("/books/{$book->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('書籍の編集');
    }

    public function test_作成者以外は403()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($other)->get("/books/{$book->id}/edit");

        $response->assertStatus(403);
    }
}
