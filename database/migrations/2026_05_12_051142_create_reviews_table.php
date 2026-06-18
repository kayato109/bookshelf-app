<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * reviews テーブルを作成するマイグレーション.
     *
     * 書籍に対するユーザーのレビュー（評価・コメント）を管理する。
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('book_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->tinyInteger('rating');
            $table->text('comment');

            $table->timestamps();
        });
    }

    /**
     * Drop the reviews table.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
