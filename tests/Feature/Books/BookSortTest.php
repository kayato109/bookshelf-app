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

        $response = $this->get(route('books.index', ['sort' => 'newest']));

        $response->assertStatus(200)
            ->assertSeeInOrder([
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

        $response = $this->get(route('books.index', ['sort' => 'oldest']));

        $response->assertStatus(200)
            ->assertSeeInOrder([
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

        $response = $this->get(route('books.index', ['sort' => 'title']));

        $response->assertStatus(200)
            ->assertSeeInOrder([
                'Apple',
                'Laravel',
                'Zebra',
            ]);
    }

    public function test_ratingで平均評価の高い順になりレビューなしは最後()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $high = Book::factory()->create();
        Review::factory()->for($high)->create(['rating' => 5]);

        $mid = Book::factory()->create();
        Review::factory()->for($mid)->create(['rating' => 3]);

        $none = Book::factory()->create();

        $response = $this->get(route('books.index', ['sort' => 'rating']));

        $response->assertStatus(200)
            ->assertSeeInOrder([
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

        $response = $this->get(route('books.index', ['sort' => 'unknown']));

        $response->assertStatus(200)
            ->assertSeeInOrder([
                $new->title,
                $old->title,
            ]);
    }
}
