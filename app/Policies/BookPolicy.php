<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

/**
 * 書籍に関する認可ポリシー.
 *
 * - create: 全ユーザーが作成可能
 * - update: 自分が投稿した書籍のみ更新可能
 * - delete: 自分が投稿した書籍のみ削除可能
 */
class BookPolicy
{
    /**
     * 書籍作成を許可するか
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * 書籍更新を許可するか
     */
    public function update(User $user, Book $book): bool
    {
        return $user->id === $book->user_id;
    }

    /**
     * 書籍削除を許可するか
     */
    public function delete(User $user, Book $book): bool
    {
        return $user->id === $book->user_id;
    }
}
