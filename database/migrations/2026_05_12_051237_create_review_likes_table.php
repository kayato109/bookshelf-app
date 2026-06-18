<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * review_likes テーブルを作成するマイグレーション.
     *
     * ユーザーが「いいね」したレビューとの関係を管理する。
     */
    public function up(): void
    {
        Schema::create('review_likes', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('review_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->timestamps();

            $table->unique(['review_id', 'user_id']);
        });
    }

    /**
     * Drop the review_likes table.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_likes');
    }
};
