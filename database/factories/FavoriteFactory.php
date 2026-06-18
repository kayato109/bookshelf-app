<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Favorite モデルのファクトリ.
 *
 * ユーザーがお気に入り登録した書籍データを生成する。
 *
 * @extends Factory<Favorite>
 */
class FavoriteFactory extends Factory
{
    /**
     * モデルのデフォルト状態を定義.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'user_id' => User::factory(),
        ];
    }
}
