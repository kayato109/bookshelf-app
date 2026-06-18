<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Review モデルのファクトリ.
 *
 * 書籍レビューのダミーデータを生成する。
 *
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
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
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->text(100),
        ];
    }
}
