<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * ユーザーのお気に入り書籍データを生成するシーダー.
 *
 * 各ユーザーに対してランダムな書籍を 3〜5 冊お気に入り登録する。
 */
class FavoriteSeeder extends Seeder
{
    /**
     * シーディングの実行.
     */
    public function run(): void
    {
        $users = User::all();
        $bookIds = Book::pluck('id'); // ID のみ取得して軽量化

        // ユーザーまたは書籍が存在しない場合は処理を中断
        if ($users->isEmpty() || $bookIds->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            $favoriteBookIds = $bookIds
                ->shuffle()
                ->take(rand(3, 5));

            // 既存のお気に入りを保持しつつ追加
            $user->favoriteBooks()->syncWithoutDetaching($favoriteBookIds);
        }
    }
}
