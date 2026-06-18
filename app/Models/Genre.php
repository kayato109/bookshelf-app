<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * ジャンルモデル.
 *
 * 書籍に紐づくジャンル情報を管理する。
 * 多対多の関係で book_genre 中間テーブルを介して書籍と関連する。
 */
class Genre extends Model
{
    use HasFactory;

    /**
     * 一括代入可能な属性.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * このジャンルに属する書籍一覧を取得.
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_genre')
            ->withTimestamps();
    }
}
