<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * レビューへの「いいね」データを生成するシーダー.
 *
 * 各レビューに対して、投稿者以外のユーザーから
 * ランダムに 0〜3 件の「いいね」を付与する。
 */
class ReviewLikeSeeder extends Seeder
{
    /**
     * シーディングの実行.
     */
    public function run(): void
    {
        $userIds = User::pluck('id');
        $reviews = Review::all();

        // ユーザーまたはレビューが存在しない場合は処理を中断
        if ($userIds->isEmpty() || $reviews->isEmpty()) {
            return;
        }

        foreach ($reviews as $review) {
            // レビュー投稿者以外のユーザーに限定
            $likeUserIds = $userIds
                ->reject(fn (int $id): bool => $id === $review->user_id)
                ->shuffle()
                ->take(rand(0, 3));

            if ($likeUserIds->isNotEmpty()) {
                $review->likedByUsers()->syncWithoutDetaching($likeUserIds);
            }
        }
    }
}
