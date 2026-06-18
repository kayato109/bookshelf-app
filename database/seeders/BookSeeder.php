<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * 書籍データの初期投入を行うシーダー.
 *
 * 固定データとして複数の書籍を登録し、
 * ISBN をキーに firstOrCreate で重複登録を防ぐ。
 * また、ジャンル名から Genre ID を取得して紐づける。
 */
class BookSeeder extends Seeder
{
    /**
     * シーダーの実行.
     */
    public function run(): void
    {
        $users = User::all();

        // ユーザーが存在しない場合は処理を中断
        if ($users->isEmpty()) {
            return;
        }

        $books = $this->bookData();

        // ['ジャンル名' => id] の形で取得
        $genreMap = Genre::pluck('id', 'name');

        foreach ($books as $index => $data) {
            // 書籍を ISBN で一意に作成
            $book = Book::firstOrCreate(
                ['isbn' => $data['isbn']],
                [
                    'user_id' => $users->random()->id,
                    'title' => $data['title'],
                    'author' => $data['author'],
                    'published_date' => $data['published_date'],
                    'description' => $data['description'],
                    'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text='.($index + 1),
                ]
            );

            // ジャンル名 → ID に変換
            $genreIds = collect($data['genres'])
                ->map(fn ($name) => $genreMap[$name] ?? null)
                ->filter()
                ->values();

            // 書籍にジャンルを紐づけ
            $book->genres()->sync($genreIds);
        }
    }

    /**
     * 初期投入する書籍データ.
     *
     * @return array<int, array<string, mixed>>
     */
    private function bookData(): array
    {
        return [
            [
                'title' => '吾輩は猫である',
                'author' => '夏目漱石',
                'isbn' => '9784101010014',
                'published_date' => '1905-01-01',
                'description' => '『吾輩は猫である』は夏目漱石の長編小説であり、処女小説。',
                'genres' => ['小説'],
            ],
            [
                'title' => '人を動かす',
                'author' => 'D・カーネギー',
                'isbn' => '9784422100524',
                'published_date' => '1936-10-01',
                'description' => 'デール・カーネギーの不朽の名著。',
                'genres' => ['ビジネス', '自己啓発'],
            ],
            [
                'title' => 'リーダブルコード',
                'author' => 'Dustin Boswell',
                'isbn' => '9784873115658',
                'published_date' => '2012-06-23',
                'description' => 'より良いコードを書くための実践的テクニック。',
                'genres' => ['技術書'],
            ],
            [
                'title' => '7つの習慣',
                'author' => 'スティーブン・R・コヴィー',
                'isbn' => '9784863940246',
                'published_date' => '2013-08-30',
                'description' => '人生の成功と幸せを実現するための普遍的な原則。',
                'genres' => ['ビジネス', '自己啓発'],
            ],
            [
                'title' => '坊っちゃん',
                'author' => '夏目漱石',
                'isbn' => '9784101010021',
                'published_date' => '1906-04-01',
                'description' => '夏目漱石の初期代表作。',
                'genres' => ['小説'],
            ],
            [
                'title' => 'サピエンス全史',
                'author' => 'ユヴァル・ノア・ハラリ',
                'isbn' => '9784309226712',
                'published_date' => '2016-09-08',
                'description' => '人類の歴史を概観するベストセラー。',
                'genres' => ['歴史', '科学'],
            ],
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'isbn' => '9784048930598',
                'published_date' => '2017-12-18',
                'description' => 'コードを洗練するためのケーススタディ。',
                'genres' => ['技術書'],
            ],
            [
                'title' => '嫌われる勇気',
                'author' => '岸見一郎・古賀史健',
                'isbn' => '9784478025819',
                'published_date' => '2013-12-13',
                'description' => 'アドラー心理学を分かりやすく解説。',
                'genres' => ['自己啓発'],
            ],
            [
                'title' => '火花',
                'author' => '又吉直樹',
                'isbn' => '9784163902302',
                'published_date' => '2015-03-11',
                'description' => '純文学小説として話題を呼んだ作品。',
                'genres' => ['小説'],
            ],
            [
                'title' => 'FACTFULNESS',
                'author' => 'ハンス・ロスリング',
                'isbn' => '9784822289607',
                'published_date' => '2019-01-11',
                'description' => '世界を正しく見るための思考法。',
                'genres' => ['ビジネス', '科学'],
            ],
            [
                'title' => 'コンテナ物語',
                'author' => 'マルク・レビンソン',
                'isbn' => '9784822251468',
                'published_date' => '2007-01-18',
                'description' => '輸送革命をもたらしたコンテナの歴史。',
                'genres' => ['ビジネス', '歴史'],
            ],
        ];
    }
}
