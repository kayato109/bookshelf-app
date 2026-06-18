<?php

namespace Database\Factories;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Genre モデルのファクトリ.
 *
 * テストやシーディングで使用するジャンルデータを生成する。
 *
 * @extends Factory<Genre>
 */
class GenreFactory extends Factory
{
    /**
     * モデルのデフォルト状態を定義.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
        ];
    }
}
