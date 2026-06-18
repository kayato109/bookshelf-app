<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\ReviewLike;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * ReviewLike モデルのファクトリ.
 *
 * ユーザーがレビューに「いいね」したデータを生成する。
 *
 * @extends Factory<ReviewLike>
 */
class ReviewLikeFactory extends Factory
{
    /**
     * モデルのデフォルト状態を定義.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'review_id' => Review::factory(),
            'user_id' => User::factory(),
        ];
    }
}
