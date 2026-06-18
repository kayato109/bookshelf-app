<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * お気に入りモデル.
 *
 * ユーザーがお気に入り登録した書籍との関係を管理する。
 */
class Favorite extends Model
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
    ];

    /**
     * お気に入りを登録したユーザーを取得.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * お気に入り対象の書籍を取得.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
