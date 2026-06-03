<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::pluck('name', 'id');
        $books = Book::all();

        if ($users->isEmpty() || $books->isEmpty()) {
            return;
        }

        // ★応用要件：評価別テンプレート（1〜5）
        $ratingTemplates = [
            1 => '「:title」はあまり好みではありませんでした。（by :user）',
            2 => '「:title」はやや物足りない内容でした。（by :user）',
            3 => '「:title」は普通に楽しめました。（by :user）',
            4 => '「:title」はとても良かったです。（by :user）',
            5 => '「:title」は最高の一冊でした！（by :user）',
        ];

        foreach ($books as $book) {
            // ★応用要件：2〜4件のレビュー
            $reviewCount = rand(2, 4);

            for ($i = 0; $i < $reviewCount; $i++) {
                // ランダムユーザー
                $userId = $users->keys()->random();
                $userName = $users[$userId];

                // ★応用要件：rating を 1〜5 に拡大
                $rating = rand(1, 5);

                // ★応用要件：評価別テンプレートを使用
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
