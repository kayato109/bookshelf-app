<?php

namespace Database\Factories;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * ReadingPlan モデルのファクトリ.
 *
 * ユーザーの読書計画データを生成する。
 *
 * @extends Factory<ReadingPlan>
 */
class ReadingPlanFactory extends Factory
{
    /**
     * 対象モデル.
     *
     * @var class-string<ReadingPlan>
     */
    protected $model = ReadingPlan::class;

    /**
     * モデルのデフォルト状態を定義.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'target_date' => $this->faker->dateTimeBetween('+1 day', '+10 days')->format('Y-m-d'),
            'status' => ReadingPlanStatus::Pending,
            'completed_at' => null,
        ];
    }
}
