<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

/**
 * ジャンルデータを初期投入するシーダー.
 *
 * 固定のジャンル名を登録し、存在しない場合のみ作成する。
 */
class GenreSeeder extends Seeder
{
    /**
     * シーディングの実行.
     */
    public function run(): void
    {
        collect([
            '小説',
            'ビジネス',
            '技術書',
            '自己啓発',
            'エッセイ',
            '歴史',
            '科学',
            '芸術',
            '料理',
            '旅行',
        ])->each(function (string $name): void {
            Genre::firstOrCreate(['name' => $name]);
        });
    }
}
