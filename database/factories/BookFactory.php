<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Book モデルのファクトリ.
 *
 * テストやシーディングで使用するダミー書籍データを生成する。
 *
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * モデルのデフォルト状態を定義.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'isbn' => $this->faker->unique()->isbn13(),
            'published_date' => $this->faker->date(),
            'description' => $this->faker->text(200),
            'image_url' => $this->faker->imageUrl(),
        ];
    }
}
