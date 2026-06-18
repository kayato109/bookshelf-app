<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

/**
 * レビューに関する認可ポリシー.
 *
 * - update: 自分のレビューのみ編集可能
 * - delete: 自分のレビューのみ削除可能
 */
class ReviewPolicy
{
    /**
     * レビュー更新を許可するか
     */
    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }

    /**
     * レビュー削除を許可するか
     */
    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }
}
