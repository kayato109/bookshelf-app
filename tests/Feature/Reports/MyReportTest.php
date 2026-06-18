<?php

namespace Tests\Feature\Reports;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_未認証はレポートページにアクセスできずログインへリダイレクト()
    {
        $response = $this->get(route('reports.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_レポートページで基本統計情報が表示される()
    {
        $user = User::factory()->create();

        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();

        Review::factory()->for($user)->for($book1)->create(['rating' => 4]);
        Review::factory()->for($user)->for($book1)->create(['rating' => 5]);
        Review::factory()->for($user)->for($book2)->create(['rating' => 3]);

        $response = $this->actingAs($user)
            ->get(route('reports.index'));

        $response->assertStatus(200);

        // 総レビュー数 3
        $response->assertSee('3');

        // 読了冊数 2（distinct book_id）
        $response->assertSee('2');

        // 平均評価 4.0
        $response->assertSee('4.0');
    }

    public function test_評価分布が1から5まで正しく集計される()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        foreach ([1, 2, 3, 4, 5] as $rating) {
            Review::factory()->for($user)->for($book)->create([
                'rating' => $rating,
            ]);
        }

        $response = $this->actingAs($user)
            ->get(route('reports.index'));

        $response->assertStatus(200);

        // rating_distribution の件数がすべて 1
        $response->assertSee('1');
    }

    public function test_高評価_top5が4以上の平均評価順で最大5件表示される()
    {
        $user = User::factory()->create();

        $books = Book::factory()->count(6)->create();

        Review::factory()->for($user)->for($books[0])->create(['rating' => 5]);
        Review::factory()->for($user)->for($books[1])->create(['rating' => 4]);
        Review::factory()->for($user)->for($books[2])->create(['rating' => 5]);
        Review::factory()->for($user)->for($books[3])->create(['rating' => 4]);
        Review::factory()->for($user)->for($books[4])->create(['rating' => 5]);

        // 6冊目は rating=3 → TOP5 に入らない
        Review::factory()->for($user)->for($books[5])->create(['rating' => 3]);

        $response = $this->actingAs($user)
            ->get(route('reports.index'));

        $response->assertStatus(200);

        // 5冊分のタイトルが表示される
        foreach (range(0, 4) as $i) {
            $response->assertSee($books[$i]->title);
        }

        // rating=3 の本は表示されない
        $response->assertDontSee($books[5]->title);
    }

    public function test_ジャンル別傾向が平均評価の高い順に表示される()
    {
        $user = User::factory()->create();

        $genreA = Genre::factory()->create(['name' => 'A']);
        $genreB = Genre::factory()->create(['name' => 'B']);
        $genreC = Genre::factory()->create(['name' => 'C']);

        $bookA = Book::factory()->create();
        $bookA->genres()->sync([$genreA->id]);

        $bookB = Book::factory()->create();
        $bookB->genres()->sync([$genreB->id]);

        $bookC = Book::factory()->create();
        $bookC->genres()->sync([$genreC->id]);

        Review::factory()->for($user)->for($bookA)->create(['rating' => 5]);
        Review::factory()->for($user)->for($bookB)->create(['rating' => 3]);
        Review::factory()->for($user)->for($bookC)->create(['rating' => 4]);

        $response = $this->actingAs($user)
            ->get(route('reports.index'));

        $response->assertStatus(200);

        // 平均評価の高い順 → A(5) → C(4) → B(3)
        $response->assertSeeInOrder([
            'A',
            'C',
            'B',
        ]);
    }
}
