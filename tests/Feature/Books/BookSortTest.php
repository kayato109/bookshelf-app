<?php

namespace Tests\Feature\Books;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookSortTest extends TestCase
{
    use RefreshDatabase;

    public function test_latestで新しい順になる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $old = Book::factory()->create(['created_at' => now()->subDays(5)]);
        $new = Book::factory()->create(['created_at' => now()->subDays(1)]);

        $response = $this->get('/books?sort=newest');

        $response->assertStatus(200);

        $response->assertSeeInOrder([
            $new->title,
            $old->title,
        ]);
    }

    public function test_oldestで古い順になる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $old = Book::factory()->create(['created_at' => now()->subDays(10)]);
        $new = Book::factory()->create(['created_at' => now()->subDays(1)]);

        $response = $this->get('/books?sort=oldest');

        $response->assertStatus(200);

        $response->assertSeeInOrder([
            $old->title,
            $new->title,
        ]);
    }

    public function test_titleでタイトル昇順になる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $b1 = Book::factory()->create(['title' => 'Apple']);
        $b2 = Book::factory()->create(['title' => 'Laravel']);
        $b3 = Book::factory()->create(['title' => 'Zebra']);

        $response = $this->get('/books?sort=title');

        $response->assertStatus(200);

        $response->assertSeeInOrder([
            'Apple',
            'Laravel',
            'Zebra',
        ]);
    }

    public function test_ratingで平均評価の高い順になりレビューなしは最後()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // ★ 評価 5
        $high = Book::factory()->create();
        Review::factory()->create([
            'book_id' => $high->id,
            'rating' => 5,
        ]);

        // ★ 評価 3
        $mid = Book::factory()->create();
        Review::factory()->create([
            'book_id' => $mid->id,
            'rating' => 3,
        ]);

        // ★ 評価なし
        $none = Book::factory()->create();

        $response = $this->get('/books?sort=rating');

        $response->assertStatus(200);

        // 高い順 → 5 → 3 → 評価なし
        $response->assertSeeInOrder([
            $high->title,
            $mid->title,
            $none->title,
        ]);
    }

    public function test_sort不正値はlatestにフォールバックする()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $old = Book::factory()->create(['created_at' => now()->subDays(10)]);
        $new = Book::factory()->create(['created_at' => now()->subDays(1)]);

        // sort=unknown → newest（created_at desc）
        $response = $this->get('/books?sort=newest');

        $response->assertStatus(200);

        $response->assertSeeInOrder([
            $new->title,
            $old->title,
        ]);
    }
}
