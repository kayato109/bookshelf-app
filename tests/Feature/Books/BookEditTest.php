<?php

namespace Tests\Feature\Books;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_作成者は編集画面を表示できる()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('books.edit', $book));

        $response->assertStatus(200)
            ->assertSeeText('書籍の編集');
    }

    public function test_作成者以外は403()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($other)->get(route('books.edit', $book));

        $response->assertStatus(403);
    }
}
