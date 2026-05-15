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
        $users = User::all();
        $books = Book::all();

        // ランダムレビュー文の候補
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
                $user = $users->random();

                // ランダムテンプレートを取得
                $template = $reviewTemplates[array_rand($reviewTemplates)];

                // :title と :user を置換
                $comment = str_replace(
                    [':title', ':user'],
                    [$book->title, $user->name],
                    $template
                );

                Review::create([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'rating' => rand(3, 5),
                    'comment' => $comment,
                ]);
            }
        }
    }
}