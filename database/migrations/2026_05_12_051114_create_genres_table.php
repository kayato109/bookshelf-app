<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * genres テーブルを作成するマイグレーション.
     *
     * ジャンル名を管理するためのテーブル。
     */
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
    }

    /**
     * Drop the genres table.
     */
    public function down(): void
    {
        Schema::dropIfExists('genres');
    }
};
