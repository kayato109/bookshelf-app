<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * 書籍レビューの初期データを生成するシーダー.
 *
 * 各書籍に対して 2〜4 件のレビューを作成し、
 * 評価（1〜5）に応じたテンプレートコメントを自動生成する。
 */
class ReviewSeeder extends Seeder
{
    /**
     * シーディングの実行.
     */
    public function run(): void
    {
        // ['id' => 'name'] の形で取得
        $userNames = User::pluck('name', 'id');
        $books = Book::all();

        // ユーザーまたは書籍が存在しない場合は処理を中断
        if ($userNames->isEmpty() || $books->isEmpty()) {
            return;
        }

        // 評価ごとのコメントテンプレート
        $ratingTemplates = [
            1 => '「:title」はあまり好みではありませんでした。（by :user）',
            2 => '「:title」はやや物足りない内容でした。（by :user）',
            3 => '「:title」は普通に楽しめました。（by :user）',
            4 => '「:title」はとても良かったです。（by :user）',
            5 => '「:title」は最高の一冊でした！（by :user）',
        ];

        foreach ($books as $book) {
            // 各書籍に 2〜4 件のレビューを作成
            $reviewCount = rand(2, 4);

            for ($i = 0; $i < $reviewCount; $i++) {
                // ランダムユーザー
                $userId = $userNames->keys()->random();
                $userName = $userNames[$userId];

                // 評価（1〜5）
                $rating = rand(1, 5);

                // 評価に応じたテンプレートを取得
                $template = $ratingTemplates[$rating];

                // テンプレート置換
                $comment = str_replace(
                    [':title', ':user'],
                    [$book->title, $userName],
                    $template
                );

                Review::create([
                    'user_id' => $userId,
                    'book_id' => $book->id,
                    'rating' => $rating,
                    'comment' => $comment,
                ]);
            }
        }
    }
}
