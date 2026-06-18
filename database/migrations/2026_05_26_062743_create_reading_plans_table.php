<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * reading_plans テーブルを作成するマイグレーション.
     *
     * ユーザーごとの読書計画を管理する。
     * 目標日（target_date）、状態（status）、完了日時（completed_at）を保持する。
     * 同一ユーザーが同じ書籍に複数の計画を持たないよう unique 制約を付与。
     */
    public function up(): void
    {
        Schema::create('reading_plans', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('book_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('target_date');
            $table->string('status', 50);
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // A user can have only one reading plan per book.
            $table->unique(['user_id', 'book_id']);
        });
    }

    /**
     * Drop the reading_plans table.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_plans');
    }
};
