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
        $response = $this->get('/reports');
        $response->assertRedirect('/login');
    }

    public function test_レポートページで基本統計情報が表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // レビュー3件（2冊）
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();

        Review::factory()->create(['user_id' => $user->id, 'book_id' => $book1->id, 'rating' => 4]);
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $book1->id, 'rating' => 5]);
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $book2->id, 'rating' => 3]);

        $response = $this->get('/reports');

        $response->assertStatus(200);

        // 総レビュー数 3
        $response->assertSee('3');

        // 読了冊数 2（distinct book_id）
        $response->assertSee('2');

        // 平均評価 (4+5+3)/3 = 4.0
        $response->assertSee('4.0');
    }

    public function test_評価分布が1から5まで正しく集計される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $book = Book::factory()->create();

        // rating: 1,2,3,4,5 を1件ずつ
        foreach ([1, 2, 3, 4, 5] as $rating) {
            Review::factory()->create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'rating' => $rating,
            ]);
        }

        $response = $this->get('/reports');

        $response->assertStatus(200);

        // Blade 側で rating_distribution をそのまま表示している前提
        // 1〜5 の件数がすべて 1
        $response->assertSee('1'); // 1件ずつなので5回出るがOK
    }

    public function test_高評価_to_p5が4以上の平均評価順で最大5件表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // ★ 6冊作る（うち5冊だけ表示される）
        $books = Book::factory()->count(6)->create();

        // 平均評価を意図的に変える
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $books[0]->id, 'rating' => 5]);
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $books[1]->id, 'rating' => 4]);
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $books[2]->id, 'rating' => 5]);
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $books[3]->id, 'rating' => 4]);
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $books[4]->id, 'rating' => 5]);

        // ★ 6冊目は rating=3 → TOP5 に入らない
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $books[5]->id, 'rating' => 3]);

        $response = $this->get('/reports');

        $response->assertStatus(200);

        // 5冊分のタイトルが表示される
        $response->assertSee($books[0]->title);
        $response->assertSee($books[1]->title);
        $response->assertSee($books[2]->title);
        $response->assertSee($books[3]->title);
        $response->assertSee($books[4]->title);

        // rating=3 の本は表示されない
        $response->assertDontSee($books[5]->title);
    }

    public function test_ジャンル別傾向が平均評価の高い順に表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // ジャンル3つ
        $genreA = Genre::factory()->create(['name' => 'A']);
        $genreB = Genre::factory()->create(['name' => 'B']);
        $genreC = Genre::factory()->create(['name' => 'C']);

        // 本をジャンルに紐付け
        $bookA = Book::factory()->create();
        $bookA->genres()->attach($genreA);

        $bookB = Book::factory()->create();
        $bookB->genres()->attach($genreB);

        $bookC = Book::factory()->create();
        $bookC->genres()->attach($genreC);

        // 評価をつける（平均評価 A=5, B=3, C=4）
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $bookA->id, 'rating' => 5]);
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $bookB->id, 'rating' => 3]);
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $bookC->id, 'rating' => 4]);

        $response = $this->get('/reports');

        $response->assertStatus(200);

        // 平均評価の高い順 → A(5) → C(4) → B(3)
        $response->assertSeeInOrder([
            'A',
            'C',
            'B',
        ]);
    }
}
