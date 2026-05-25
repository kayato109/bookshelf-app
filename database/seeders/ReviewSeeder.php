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

        $reviewTemplates = [
            '「:title」を読んでとても考えさせられました。特に後半の展開が印象的です。（by :user）',
            'テンポよく読み進められて面白かったです。:title は期待以上でした！（by :user）',
            '内容が深く、読み応えがありました。:title は何度も読み返したい作品です。（by :user）',
            '正直最初は難しいと思いましたが、読み進めると魅力が分かってきました。:title はおすすめです。（by :user）',
            '登場人物の描写が素晴らしく、感情移入しながら読めました。:title は良書です。（by :user）',
            '学びが多く、読んでよかったと思える一冊でした。:title をもっと早く読めばよかった。（by :user）',
        ];

        foreach ($books as $book) {
            $reviewCount = rand(2, 4);

            for ($i = 0; $i < $reviewCount; $i++) {
                // ランダムユーザーを安全に取得
                $userId = $users->keys()->random();
                $userName = $users[$userId];

                // テンプレート置換
                $template = $reviewTemplates[array_rand($reviewTemplates)];
                $comment = str_replace(
                    [':title', ':user'],
                    [$book->title, $userName],
                    $template
                );

                Review::create([
                    'user_id' => $userId,
                    'book_id' => $book->id,
                    'rating' => rand(3, 5),
                    'comment' => $comment,
                ]);
            }
        }
    }
}
