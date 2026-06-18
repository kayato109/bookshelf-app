<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * book_genre テーブルを作成するマイグレーション.
     *
     * 書籍とジャンルの多対多リレーションを管理する中間テーブル。
     */
    public function up(): void
    {
        Schema::create('book_genre', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('book_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('genre_id')
                ->constrained()
                ->onDelete('cascade');

            $table->timestamps();

            $table->unique(['book_id', 'genre_id']);
        });
    }

    /**
     * Drop the pivot table for books and genres.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_genre');
    }
};
