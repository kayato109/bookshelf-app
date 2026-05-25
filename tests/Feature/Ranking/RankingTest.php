<?php

namespace Tests\Feature\Ranking;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Book;
use App\Models\Review;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    /* ---------------------------------------------------------
        ランキングページにアクセスできる（公開ページ）
    --------------------------------------------------------- */
    public function test_ランキングページが表示できる()
    {
        $response = $this->get('/ranking');

        $response->assertStatus(200);
    }

    /* ---------------------------------------------------------
        ランキングにレビュー数が多い本が表示される
    --------------------------------------------------------- */
    public function test_レビュー数が多い本がランキングに表示される()
    {
        $book1 = Book::factory()->create(['title' => 'レビュー多い本']);
        Review::factory()->count(5)->create(['book_id' => $book1->id]);

        $book2 = Book::factory()->create(['title' => 'レビュー少ない本']);
        Review::factory()->count(1)->create(['book_id' => $book2->id]);

        $response = $this->get('/ranking');

        $response->assertStatus(200);

        $response->assertSeeText('レビュー多い本');
        $response->assertSeeText('レビュー少ない本');
    }

    /* ---------------------------------------------------------
        ランキングがレビュー平均評価順で並んでいることを確認
    --------------------------------------------------------- */
    public function test_ランキングが平均評価順で並んでいる()
    {
        $bookA = Book::factory()->create(['title' => '本A']);
        Review::factory()->create(['book_id' => $bookA->id, 'rating' => 5]);

        $bookB = Book::factory()->create(['title' => '本B']);
        Review::factory()->create(['book_id' => $bookB->id, 'rating' => 3]);

        $bookC = Book::factory()->create(['title' => '本C']);
        Review::factory()->create(['book_id' => $bookC->id, 'rating' => 1]);

        $response = $this->get('/ranking');

        $response->assertStatus(200);

        $response->assertSeeInOrder([
            '本A', // 5.0
            '本B', // 3.0
            '本C', // 1.0
        ]);
    }
}
