<?php

namespace Tests\Feature\Ranking;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    public function test_ランキングページが表示できる()
    {
        $response = $this->get(route('ranking.index'));

        $response->assertStatus(200);
    }

    public function test_レビュー数が多い本がランキングに表示される()
    {
        $book1 = Book::factory()->create(['title' => 'レビュー多い本']);
        Review::factory()->count(5)->for($book1)->create();

        $book2 = Book::factory()->create(['title' => 'レビュー少ない本']);
        Review::factory()->count(1)->for($book2)->create();

        $response = $this->get(route('ranking.index'));

        $response->assertStatus(200)
            ->assertSeeText('レビュー多い本')
            ->assertSeeText('レビュー少ない本');
    }

    public function test_ランキングが平均評価順で並んでいる()
    {
        $bookA = Book::factory()->create(['title' => '本A']);
        Review::factory()->for($bookA)->create(['rating' => 5]);

        $bookB = Book::factory()->create(['title' => '本B']);
        Review::factory()->for($bookB)->create(['rating' => 3]);

        $bookC = Book::factory()->create(['title' => '本C']);
        Review::factory()->for($bookC)->create(['rating' => 1]);

        $response = $this->get(route('ranking.index'));

        $response->assertStatus(200)
            ->assertSeeInOrder([
                '本A', // 5.0
                '本B', // 3.0
                '本C', // 1.0
            ]);
    }
}
