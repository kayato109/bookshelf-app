<?php

namespace App\Models;

use App\Enums\ReadingPlanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 読書計画モデル.
 *
 * ユーザーが設定した読書計画を管理する。
 * 目標日（target_date）、状態（status）、完了日時（completed_at）を保持する。
 * 状態は ReadingPlanStatus Enum によって管理される。
 */
class ReadingPlan extends Model
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
        'target_date',
        'status',
        'completed_at',
    ];

    /**
     * 属性の型キャスト設定.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => ReadingPlanStatus::class,
        'target_date' => 'date',
        'completed_at' => 'datetime',
    ];

    /**
     * 読書計画を作成したユーザーを取得.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 読書計画の対象となる書籍を取得.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * 読書計画に紐づく通知一覧を取得.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
