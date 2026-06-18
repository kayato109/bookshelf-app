<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * レビューモデル.
 *
 * 書籍に対するユーザーのレビュー（評価・コメント）を管理する。
 * いいね（ReviewLike）や、いいねしたユーザーとの関係も保持する。
 */
class Review extends Model
{
    use HasFactory;

    /**
     * 一括代入可能な属性.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'book_id',
        'rating',
        'comment',
    ];

    /**
     * レビューを投稿したユーザーを取得.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * レビュー対象の書籍を取得.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * このレビューに「いいね」したユーザー一覧を取得.
     */
    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'review_likes')
            ->withTimestamps();
    }

    /**
     * このレビューについた「いいね」レコード一覧を取得.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(ReviewLike::class);
    }
}
