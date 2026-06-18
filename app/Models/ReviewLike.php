<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * レビューいいねモデル.
 *
 * ユーザーがレビューに「いいね」した情報を管理する。
 */
class ReviewLike extends Model
{
    use HasFactory;

    /**
     * 一括代入可能な属性.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'review_id',
    ];

    /**
     * いいねを行ったユーザーを取得.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * いいね対象のレビューを取得.
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }
}
