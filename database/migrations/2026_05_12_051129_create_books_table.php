<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * books テーブルを作成するマイグレーション.
     *
     * 書籍情報（タイトル・著者・ISBN・出版日など）を管理する。
     * user_id は書籍の登録者を示す。
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('title');
            $table->string('author');
            $table->string('isbn', 13)->nullable()->unique();
            $table->date('published_date')->nullable();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Drop the books table.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
