<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * favorites テーブルを作成するマイグレーション.
     *
     * ユーザーがお気に入り登録した書籍との関係を管理する。
     */
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('book_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->timestamps();

            $table->unique(['book_id', 'user_id']);
        });
    }

    /**
     * Drop the favorites table.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
