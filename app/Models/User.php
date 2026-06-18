<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * ユーザーモデル.
 *
 * 書籍、レビュー、読書計画、お気に入り、レビューいいねなど
 * 多くの関連データの中心となるモデル。
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * 一括代入可能な属性.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * JSON シリアライズ時に隠す属性.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 属性の型キャスト設定.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * ユーザーが登録した書籍一覧を取得.
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    /**
     * ユーザーが投稿したレビュー一覧を取得.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * ユーザーがお気に入り登録した書籍一覧を取得.
     */
    public function favoriteBooks(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'favorites')
            ->withTimestamps();
    }

    /**
     * ユーザーが「いいね」したレビュー一覧を取得.
     */
    public function likedReviews(): BelongsToMany
    {
        return $this->belongsToMany(Review::class, 'review_likes')
            ->withTimestamps();
    }

    /**
     * ユーザーの読書計画一覧を取得.
     */
    public function readingPlans(): HasMany
    {
        return $this->hasMany(ReadingPlan::class);
    }
}
