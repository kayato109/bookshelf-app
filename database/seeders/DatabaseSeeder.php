<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * アプリケーション全体のシーディングを統括するシーダー.
 *
 * 個別の Seeder クラスを順番に実行する。
 */
class DatabaseSeeder extends Seeder
{
    /**
     * シーディングの実行.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            GenreSeeder::class,
            BookSeeder::class,
            ReviewSeeder::class,
            FavoriteSeeder::class,
            ReviewLikeSeeder::class,
            ReadingPlanSeeder::class,
        ]);
    }
}
